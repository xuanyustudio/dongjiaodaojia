package admin

import (
	"strings"

	"dongjiao/server/internal/dbx"
	"dongjiao/server/internal/httpx"
	"dongjiao/server/internal/util"
)

func (a *Admin) ApplicationList(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	page := c.Int("page", 1)
	limit := c.Int("limit", 20)
	if page < 1 {
		page = 1
	}
	if limit < 1 {
		limit = 20
	}
	total, err := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("application"))
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	rows, err := dbx.Query(a.DB, "SELECT * FROM "+a.table("application")+" ORDER BY app_id ASC LIMIT ? OFFSET ?", limit, (page-1)*limit)
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	if rows == nil {
		rows = []map[string]any{}
	}
	last := 0
	if total > 0 {
		last = (total + limit - 1) / limit
	}
	httpx.OK(c.W, map[string]any{"data": dbx.Page{
		Total: total, PerPage: limit, CurrentPage: page, LastPage: last, Data: rows,
	}})
}

func (a *Admin) ApplicationInfo(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	row, err := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("application")+" WHERE app_id=? LIMIT 1", c.Raw("app_id"))
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	if row == nil {
		httpx.Fail(c.W, 411, "应用不存在")
		return
	}
	httpx.OK(c.W, map[string]any{"data": row})
}

func (a *Admin) ApplicationSave(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	fields := []string{"application_name", "app_dir", "status", "app_type", "login_table", "login_fields", "domain", "pk"}
	cols, vals := a.pick(c, fields)
	if c.Raw("app_id") == "" {
		if _, err := dbx.Insert(a.DB, a.table("application"), cols, vals); err != nil {
			httpx.Fail(c.W, 411, err.Error())
			return
		}
		httpx.OK(c.W, nil)
		return
	}
	if err := dbx.Update(a.DB, a.table("application"), "app_id", c.Raw("app_id"), cols, vals); err != nil {
		httpx.Fail(c.W, 411, err.Error())
		return
	}
	httpx.OK(c.W, nil)
}

func (a *Admin) ApplicationDelete(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	id := c.Raw("app_id")
	if id == "" || id == "1" {
		httpx.Fail(c.W, 411, "该应用禁止卸载")
		return
	}
	if _, err := dbx.Exec(a.DB, "DELETE FROM "+a.table("application")+" WHERE app_id=?", id); err != nil {
		httpx.Fail(c.W, 411, err.Error())
		return
	}
	httpx.OK(c.W, nil)
}

func (a *Admin) MenuList(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	appID := c.Raw("app_id")
	if appID == "" {
		appID = "1"
	}
	rows, err := dbx.Query(a.DB, "SELECT menu_id,pid,title,controller_name,create_code,create_table,table_name,status,sortid FROM "+a.table("menu")+" WHERE app_id=? ORDER BY sortid ASC, menu_id ASC", appID)
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	apps, err := dbx.Query(a.DB, "SELECT app_id,app_type,application_name FROM "+a.table("application")+" ORDER BY app_id ASC")
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	if apps == nil {
		apps = []map[string]any{}
	}
	for i := range apps {
		apps[i]["url"] = "/admin/Menu/index.html?app_id=" + util.AsString(apps[i]["app_id"]) + "&app_type=" + util.AsString(apps[i]["app_type"])
	}
	tables, err := a.tableNames()
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	httpx.JSON(c.W, map[string]any{
		"status":         200,
		"list":           menuTree(rows, "0"),
		"defaultConnect": "mysql",
		"connects":       []string{"mysql"},
		"tableList":      tables,
		"app_list":       apps,
		"page_type_list": []map[string]any{
			{"name": "table表格列表", "type": 1},
			{"name": "form表单", "type": 2},
		},
	})
}

func (a *Admin) MenuInfo(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	row, err := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("menu")+" WHERE menu_id=? LIMIT 1", c.Raw("menu_id"))
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	if row == nil {
		httpx.Fail(c.W, 411, "菜单不存在")
		return
	}
	httpx.OK(c.W, map[string]any{"data": row})
}

func (a *Admin) MenuUpdateExt(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	id := c.Raw("menu_id")
	if id == "" {
		httpx.Fail(c.W, 411, "参数错误")
		return
	}
	cols, vals := a.pick(c, []string{"create_code", "status", "sortid"})
	if len(cols) == 0 {
		httpx.OK(c.W, nil)
		return
	}
	if err := dbx.Update(a.DB, a.table("menu"), "menu_id", id, cols, vals); err != nil {
		httpx.Fail(c.W, 411, err.Error())
		return
	}
	httpx.OK(c.W, nil)
}

func (a *Admin) MenuTables(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	tables, err := a.tableNames()
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	httpx.OK(c.W, map[string]any{"data": tables})
}

func (a *Admin) LocalOnly(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	httpx.Fail(c.W, 411, "本地环境不生成代码")
}

func (a *Admin) tableNames() ([]string, error) {
	rows, err := dbx.Query(a.DB, "SHOW TABLES")
	if err != nil {
		return nil, err
	}
	hide := map[string]bool{"menu": true, "application": true, "admin_user": true, "action": true, "log": true, "field": true}
	prefix := a.Cfg.DBPrefix
	out := make([]string, 0, len(rows))
	for _, row := range rows {
		name := ""
		for _, v := range row {
			name = util.AsString(v)
			break
		}
		name = strings.TrimPrefix(name, prefix)
		if name == "" || hide[name] {
			continue
		}
		out = append(out, name)
	}
	return out, nil
}

func menuTree(rows []map[string]any, pid string) []map[string]any {
	out := make([]map[string]any, 0)
	for _, row := range rows {
		if util.AsString(row["pid"]) != pid {
			continue
		}
		children := menuTree(rows, util.AsString(row["menu_id"]))
		if len(children) > 0 {
			row["children"] = children
		}
		out = append(out, row)
	}
	return out
}
