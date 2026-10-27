package admin

import (
	"encoding/json"
	"fmt"
	"strings"
	"time"

	"dongjiao/server/internal/auth"
	"dongjiao/server/internal/dbx"
	"dongjiao/server/internal/httpx"
	"dongjiao/server/internal/util"
)

type Search struct {
	Column string
	Param  string
	Op     string
	Fixed  any
	Time   bool
}

type Join struct {
	Name    string
	Table   string
	Local   string
	Foreign string
	Fields  []string
}

type Dump struct {
	Header string
	File   string
	Labels map[string]string
	Times  []string
}

type Resource struct {
	Name         string
	Table        string
	Alias        string
	PK           string
	ListFields   string
	InfoFields   string
	DetailFields string
	Write        []string
	UpdateFields []string
	Ext          []string
	Search       []Search
	Joins        []Join
	ExtraWhere   string
	CreateNow    []string
	UpdateNow    []string
	UpdateParse  []string
	AutoSort     bool
	JSONFields   []string
	SplitFields  []string
	ProtectID    string
	PageSQL      bool
	Dump         *Dump
	DumpFields   string
}

func (r *Resource) alias() string {
	if r.Alias != "" {
		return r.Alias
	}
	return r.Table
}

func (r *Resource) pk() string {
	if r.PK != "" {
		return r.PK
	}
	return "id"
}

func (a *Admin) Index(res *Resource) httpx.Handler {
	return func(c *httpx.Ctx) {
		if _, ok := a.requireAuth(c); !ok {
			return
		}
		page := c.Int("page", 1)
		limit := c.Int("limit", 20)
		p, err := a.page(res, c, page, limit, res.ListFields)
		if err != nil {
			httpx.Fail(c.W, 500, err.Error())
			return
		}
		body := map[string]any{"status": 200, "data": p}
		if res.Name == "Adminuser" && page == 1 {
			rows, _ := dbx.Query(a.DB, "SELECT role_id,name FROM "+a.table("role"))
			body["sql_field_data"] = map[string]any{"role_ids": pairOptions(rows, "role_id", "name")}
		}
		if res.Name == "Servicespec" && page == 1 {
			rows, _ := dbx.Query(a.DB, "SELECT id,name FROM "+a.table("service"))
			body["sql_field_data"] = map[string]any{"service_ids": pairOptions(rows, "id", "name")}
		}
		if res.Name == "Technician" {
			cfg, _ := a.baseConfig()
			var list []any
			_ = json.Unmarshal([]byte(cfg["tglist"]), &list)
			body["tgradecount"] = len(list)
		}
		httpx.JSON(c.W, body)
	}
}

func (a *Admin) page(res *Resource, c *httpx.Ctx, page, limit int, fields string) (dbx.Page, error) {
	if page < 1 {
		page = 1
	}
	if limit < 1 {
		limit = 20
	}
	alias := res.alias()
	selects := prefixFields(fields, alias)
	from := a.table(res.Table) + " AS " + quote(alias)
	for _, j := range res.Joins {
		selects += "," + prefixRel(j)
		from += a.joinOn(alias, j)
	}
	where, args := a.filters(res, c)
	total, err := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+from+" WHERE "+where, args...)
	if err != nil {
		return dbx.Page{}, err
	}
	off := (page - 1) * limit
	order := quote(alias) + "." + res.pk() + " DESC"
	list, err := dbx.Query(a.DB, "SELECT "+selects+" FROM "+from+" WHERE "+where+" ORDER BY "+order+" LIMIT ? OFFSET ?", append(args, limit, off)...)
	if err != nil {
		return dbx.Page{}, err
	}
	if res.Name == "Order" {
		for i, row := range list {
			list[i]["address"] = util.AsString(row["province"]) + util.AsString(row["city"]) + util.AsString(row["district"]) + util.AsString(row["address"])
		}
	}
	last := 0
	if total > 0 {
		last = (total + limit - 1) / limit
	}
	return dbx.Page{Total: total, PerPage: limit, CurrentPage: page, LastPage: last, Data: list}, nil
}

func (a *Admin) filters(res *Resource, c *httpx.Ctx) (string, []any) {
	var conds []dbx.Cond
	for _, s := range res.Search {
		op := s.Op
		if op == "" {
			op = "="
		}
		val := s.Fixed
		if val == nil {
			val = c.In[s.Param]
			if op == "=" || op == "like" {
				val = util.SearchIn(util.AsString(val))
			}
		}
		if s.Time {
			a0, b0, ok := util.Pair(val)
			if !ok {
				continue
			}
			val = []any{util.Unix(a0), util.Unix(b0)}
			if util.Unix(a0) == 0 {
				continue
			}
		}
		conds = append(conds, dbx.Cond{Column: s.Column, Op: op, Val: val})
	}
	where, args := dbx.Where(conds)
	if res.ExtraWhere != "" {
		where += " AND " + res.ExtraWhere
	}
	return where, args
}

func (a *Admin) UpdateExt(res *Resource) httpx.Handler {
	return func(c *httpx.Ctx) {
		if _, ok := a.requireAuth(c); !ok {
			return
		}
		id := c.Raw(res.pk())
		if id == "" {
			httpx.Fail(c.W, 411, "参数错误")
			return
		}
		cols, vals := a.pick(c, res.Ext)
		if len(cols) == 0 {
			httpx.OK(c.W, map[string]any{"msg": "操作成功"})
			return
		}
		if err := dbx.Update(a.DB, a.table(res.Table), res.pk(), id, cols, vals); err != nil {
			httpx.Fail(c.W, 411, err.Error())
			return
		}
		httpx.OK(c.W, map[string]any{"msg": "操作成功"})
	}
}

func (a *Admin) Add(res *Resource) httpx.Handler {
	return func(c *httpx.Ctx) {
		if _, ok := a.requireAuth(c); !ok {
			return
		}
		cols, vals := a.writeCols(res, c, res.Write, false)
		if res.Name == "Adminuser" {
			cols, vals = replaceCol(cols, vals, "pwd", auth.AdminPassword(c.Raw("pwd"), a.Cfg.PasswordSecret))
		}
		id, err := dbx.Insert(a.DB, a.table(res.Table), cols, vals)
		if err != nil {
			httpx.Fail(c.W, 411, err.Error())
			return
		}
		if res.AutoSort && (c.Raw("sort") == "" || c.Raw("sort") == "0") {
			_, _ = dbx.Exec(a.DB, "UPDATE "+a.table(res.Table)+" SET sort=? WHERE "+res.pk()+"=?", id, id)
		}
		httpx.OK(c.W, map[string]any{"data": id})
	}
}

func (a *Admin) Update(res *Resource) httpx.Handler {
	return func(c *httpx.Ctx) {
		if _, ok := a.requireAuth(c); !ok {
			return
		}
		id := c.Raw(res.pk())
		if id == "" {
			httpx.Fail(c.W, 411, "参数错误")
			return
		}
		fields := res.Write
		if len(res.UpdateFields) > 0 {
			fields = res.UpdateFields
		}
		cols, vals := a.writeCols(res, c, fields, true)
		if err := dbx.Update(a.DB, a.table(res.Table), res.pk(), id, cols, vals); err != nil {
			httpx.Fail(c.W, 411, err.Error())
			return
		}
		httpx.OK(c.W, nil)
	}
}

func (a *Admin) writeCols(res *Resource, c *httpx.Ctx, allow []string, update bool) ([]string, []any) {
	cols, vals := a.pick(c, allow)
	if res.Name == "Role" {
		cols, vals = replaceCol(cols, vals, "access", joinRoleAccess(c.In["access"]))
	}
	if res.Name == "Withdrawapply" {
		for i, col := range cols {
			if col == "deion" {
				cols[i] = "description"
			}
		}
	}
	now := time.Now().Unix()
	for _, f := range res.CreateNow {
		if !update {
			cols, vals = replaceCol(cols, vals, f, now)
		}
	}
	for _, f := range res.UpdateNow {
		if update {
			cols, vals = replaceCol(cols, vals, f, now)
		}
	}
	if update {
		for _, f := range res.UpdateParse {
			cols, vals = replaceCol(cols, vals, f, util.Unix(c.Raw(f)))
		}
	}
	for _, f := range res.JSONFields {
		if v, ok := c.In[f]; ok {
			cols, vals = replaceCol(cols, vals, f, util.ItemData(v))
		}
	}
	for _, f := range res.SplitFields {
		if arr, ok := c.In[f].([]any); ok {
			parts := make([]string, len(arr))
			for i, item := range arr {
				parts[i] = util.AsString(item)
			}
			cols, vals = replaceCol(cols, vals, f, strings.Join(parts, ","))
		}
	}
	cols, vals = dropCol(cols, vals, res.pk())
	if update {
		// pk is the WHERE key, not a SET column, unless the client posted it.
	}
	return cols, vals
}

func (a *Admin) GetUpdateInfo(res *Resource) httpx.Handler {
	return func(c *httpx.Ctx) {
		if _, ok := a.requireAuth(c); !ok {
			return
		}
		id := c.Str(res.pk())
		if id == "" {
			httpx.Fail(c.W, 411, "参数错误")
			return
		}
		fields := res.InfoFields
		if fields == "" {
			fields = res.ListFields
		}
		row, err := dbx.QueryOne(a.DB, "SELECT "+quoteFields(fields)+" FROM "+a.table(res.Table)+" WHERE "+quote(res.pk())+"=? LIMIT 1", id)
		if err != nil {
			httpx.Fail(c.W, 500, err.Error())
			return
		}
		if res.Name == "Role" && row != nil {
			row["access"] = splitRoleAccess(row["access"])
		}
		presentRow(res, row)
		a.decorate(res, row)
		httpx.OK(c.W, map[string]any{"data": row})
	}
}

func (a *Admin) Detail(res *Resource) httpx.Handler {
	return func(c *httpx.Ctx) {
		if _, ok := a.requireAuth(c); !ok {
			return
		}
		id := c.Str(res.pk())
		if id == "" {
			httpx.Fail(c.W, 411, "参数错误")
			return
		}
		fields := res.DetailFields
		if fields == "" {
			fields = res.ListFields
		}
		if len(res.Joins) > 0 && (res.Name == "Order" || res.Name == "Technician") {
			alias := res.alias()
			selects := prefixFields(fields, alias)
			from := a.table(res.Table) + " AS " + quote(alias)
			for _, j := range res.Joins {
				selects += "," + prefixRel(j)
				from += a.joinOn(alias, j)
			}
			row, err := dbx.QueryOne(a.DB, "SELECT "+selects+" FROM "+from+" WHERE "+quote(alias)+"."+res.pk()+"=? LIMIT 1", id)
			if err != nil {
				httpx.Fail(c.W, 500, err.Error())
				return
			}
			presentRow(res, row)
			if res.Name == "Technician" && row != nil {
				ids := strings.Split(util.AsString(row["service"]), ",")
				ph := make([]string, 0, len(ids))
				args := make([]any, 0, len(ids))
				for _, sid := range ids {
					if sid == "" {
						continue
					}
					ph = append(ph, "?")
					args = append(args, sid)
				}
				if len(args) > 0 {
					sv, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("service")+" WHERE id IN ("+strings.Join(ph, ",")+")", args...)
					row["service"] = sv
				}
				cfg, _ := a.baseConfig()
				var list []any
				_ = json.Unmarshal([]byte(cfg["tglist"]), &list)
				row["tgradecount"] = len(list)
			}
			httpx.OK(c.W, map[string]any{"data": row})
			return
		}
		row, err := dbx.QueryOne(a.DB, "SELECT "+quoteFields(fields)+" FROM "+a.table(res.Table)+" WHERE "+quote(res.pk())+"=? LIMIT 1", id)
		if err != nil {
			httpx.Fail(c.W, 500, err.Error())
			return
		}
		presentRow(res, row)
		httpx.OK(c.W, map[string]any{"data": row})
	}
}

func presentRow(res *Resource, row map[string]any) {
	if row == nil {
		return
	}
	switch res.Name {
	case "Order":
		row["Refund"] = row["refund"]
	case "Withdrawapply":
		row["deion"] = row["description"]
	}
}

func (a *Admin) Delete(res *Resource) httpx.Handler {
	return func(c *httpx.Ctx) {
		if _, ok := a.requireAuth(c); !ok {
			return
		}
		id := c.Str(res.pk())
		if res.ProtectID != "" && id == res.ProtectID {
			httpx.Fail(c.W, 411, "超级用户禁止删除")
			return
		}
		if id == "" {
			httpx.Fail(c.W, 411, "参数错误")
			return
		}
		if err := dbx.DeleteIn(a.DB, a.table(res.Table), res.pk(), strings.Split(id, ",")); err != nil {
			httpx.Fail(c.W, 500, err.Error())
			return
		}
		httpx.OK(c.W, map[string]any{"msg": "操作成功"})
	}
}

func (a *Admin) Dump(res *Resource) httpx.Handler {
	return func(c *httpx.Ctx) {
		if _, ok := a.requireAuth(c); !ok {
			return
		}
		if res.Dump == nil {
			httpx.Fail(c.W, 404, "方法不存在")
			return
		}
		page := c.Int("page", 1)
		limit := a.Cfg.DumpLimit
		fields := res.DumpFields
		if fields == "" {
			fields = res.ListFields
		}
		p, err := a.page(res, c, page, limit, fields)
		if err != nil {
			httpx.Fail(c.W, 500, err.Error())
			return
		}
		for i, row := range p.Data {
			for _, f := range res.Dump.Times {
				p.Data[i][f] = util.FormatUnix(row[f])
			}
			for f, cfg := range res.Dump.Labels {
				p.Data[i][f] = util.ItemVal(util.AsString(row[f]), cfg)
			}
		}
		httpx.JSON(c.W, map[string]any{
			"status":     200,
			"header":     strings.Split(res.Dump.Header, ","),
			"percentage": percentage(page, p.Total, limit),
			"filename":   res.Dump.File + "." + a.Cfg.DumpExt,
			"data":       p.Data,
		})
	}
}

func (a *Admin) decorate(res *Resource, row map[string]any) {
	if row == nil {
		return
	}
	for _, f := range res.JSONFields {
		var v any
		if err := json.Unmarshal([]byte(util.AsString(row[f])), &v); err == nil {
			row[f] = v
		}
	}
	for _, f := range res.SplitFields {
		s := util.AsString(row[f])
		if s == "" {
			row[f] = []string{}
		} else {
			row[f] = strings.Split(s, ",")
		}
	}
}

func prefixFields(fields, alias string) string {
	if strings.TrimSpace(fields) == "*" {
		return quote(alias) + ".*"
	}
	parts := strings.Split(fields, ",")
	out := make([]string, 0, len(parts))
	seen := map[string]bool{}
	for _, p := range parts {
		p = strings.TrimSpace(p)
		if p == "" || seen[p] || !util.SafeIdent(p) {
			continue
		}
		seen[p] = true
		out = append(out, quote(alias)+"."+quote(p)+" AS "+quote(p))
	}
	return strings.Join(out, ",")
}

// 一对多的附表只挂最新一条，避免列表把同一个技师乘成多行。
func (a *Admin) joinOn(alias string, j Join) string {
	on := fmt.Sprintf(" LEFT JOIN %s AS %s ON %s.%s=%s.%s", a.table(j.Table), quote(j.Name), quote(alias), quote(j.Local), quote(j.Name), quote(j.Foreign))
	if j.Foreign != "id" {
		on += fmt.Sprintf(" AND %s.%s=(SELECT MAX(%s) FROM %s WHERE %s=%s.%s)", quote(j.Name), quote("id"), quote("id"), a.table(j.Table), quote(j.Foreign), quote(alias), quote(j.Local))
	}
	return on
}

func prefixRel(j Join) string {
	var out []string
	seen := map[string]bool{}
	for _, f := range j.Fields {
		f = strings.TrimSpace(f)
		if f == "" || seen[f] || !util.SafeIdent(f) {
			continue
		}
		seen[f] = true
		out = append(out, quote(j.Name)+"."+quote(f)+" AS "+quote(j.Name+"__"+f))
	}
	return strings.Join(out, ",")
}

func splitRoleAccess(v any) []string {
	switch t := v.(type) {
	case []string:
		return t
	case []any:
		out := make([]string, 0, len(t))
		for _, item := range t {
			s := strings.TrimSpace(util.AsString(item))
			if s != "" {
				out = append(out, s)
			}
		}
		return out
	}
	s := strings.TrimSpace(util.AsString(v))
	if s == "" || s == "null" {
		return []string{}
	}
	if strings.HasPrefix(s, "[") {
		var arr []string
		if json.Unmarshal([]byte(s), &arr) == nil {
			return arr
		}
	}
	parts := strings.Split(s, ",")
	out := make([]string, 0, len(parts))
	for _, p := range parts {
		p = strings.TrimSpace(p)
		if p != "" {
			out = append(out, p)
		}
	}
	return out
}

func joinRoleAccess(v any) string {
	parts := splitRoleAccess(v)
	seen := map[string]bool{}
	out := make([]string, 0, len(parts)+1)
	for _, p := range parts {
		if p == "" || seen[p] {
			continue
		}
		seen[p] = true
		out = append(out, p)
	}
	if !seen["Home"] {
		out = append(out, "Home")
	}
	return strings.Join(out, ",")
}

func quote(s string) string { return "`" + strings.ReplaceAll(s, "`", "") + "`" }

func quoteFields(fields string) string {
	if strings.TrimSpace(fields) == "*" {
		return "*"
	}
	parts := strings.Split(fields, ",")
	out := make([]string, 0, len(parts))
	for _, p := range parts {
		p = strings.TrimSpace(p)
		if p == "" || !util.SafeIdent(p) {
			continue
		}
		out = append(out, quote(p))
	}
	if len(out) == 0 {
		return "*"
	}
	return strings.Join(out, ",")
}

func replaceCol(cols []string, vals []any, name string, val any) ([]string, []any) {
	for i, c := range cols {
		if c == name {
			vals[i] = val
			return cols, vals
		}
	}
	return append(cols, name), append(vals, val)
}

func dropCol(cols []string, vals []any, name string) ([]string, []any) {
	var c2 []string
	var v2 []any
	for i, c := range cols {
		if c == name {
			continue
		}
		c2 = append(c2, c)
		v2 = append(v2, vals[i])
	}
	return c2, v2
}
