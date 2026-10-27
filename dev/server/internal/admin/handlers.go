package admin

import (
	"crypto/md5"
	"database/sql"
	"encoding/hex"
	"encoding/json"
	"fmt"
	"io"
	"math"
	"os"
	"path/filepath"
	"strconv"
	"strings"
	"time"

	"dongjiao/server/internal/auth"
	"dongjiao/server/internal/dbx"
	"dongjiao/server/internal/httpx"
	"dongjiao/server/internal/util"
)

type Admin struct {
	*httpx.Deps
}

func New(d *httpx.Deps) *Admin { return &Admin{Deps: d} }

func (a *Admin) table(name string) string { return a.Cfg.DBPrefix + name }

func (a *Admin) requireAuth(c *httpx.Ctx) (map[string]any, bool) {
	cfg, _ := a.baseConfig()
	if ips := strings.TrimSpace(cfg["ip_white"]); ips != "" {
		ok := false
		for _, ip := range strings.Split(ips, "\n") {
			if strings.TrimSpace(ip) == c.IP() {
				ok = true
			}
		}
		if !ok {
			httpx.Fail(c.W, 411, "请求ip来源错误")
			return nil, false
		}
	}
	token := c.HeaderToken("Authorization")
	if token == "" {
		httpx.Fail(c.W, 101, "token不能为空")
		return nil, false
	}
	if len(strings.Split(token, ".")) != 3 {
		httpx.Fail(c.W, 101, "token格式错误")
		return nil, false
	}
	info, err := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("admin_token")+" WHERE token=? LIMIT 1", token)
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return nil, false
	}
	if info == nil {
		httpx.Fail(c.W, 101, "token不存在")
		return nil, false
	}
	if !util.Truthy(info["status"]) {
		httpx.Fail(c.W, 101, "状态被禁用")
		return nil, false
	}
	if !util.Truthy(info["dev_status"]) {
		httpx.Fail(c.W, 101, "你已下线,账户在其它设备登录!")
		return nil, false
	}
	claims, err := auth.Parse(token, a.Cfg.JWTSecret, a.Cfg.JWTIss, a.Cfg.JWTAud, time.Now())
	if err != nil {
		code := 101
		httpx.Fail(c.W, code, err.Error())
		return nil, false
	}
	var user map[string]any
	if err := json.Unmarshal([]byte(claims.UID), &user); err != nil {
		httpx.Fail(c.W, 101, "token失效")
		return nil, false
	}
	a.writeOpLog(c, util.AsString(user["username"]))
	return user, true
}

func (a *Admin) writeOpLog(c *httpx.Ctx, username string) {
	act := actionOf(c.R.URL.Path)
	if act != "add" && act != "update" && act != "delete" {
		return
	}
	content := map[string]any{}
	for k, v := range c.In {
		s := util.AsString(v)
		if len(s) > 200 || strings.Contains(strings.ToLower(k), "password") || strings.Contains(strings.ToLower(k), "pwd") {
			continue
		}
		content[k] = v
	}
	b, _ := json.Marshal(content)
	_, _ = dbx.Exec(a.DB, "INSERT INTO "+a.table("log")+"(application_name,username,url,ip,useragent,content,create_time,type) VALUES (?,?,?,?,?,?,?,?)",
		"admin", username, c.R.URL.RequestURI(), c.IP(), c.R.UserAgent(), string(b), time.Now().Unix(), 2)
}

func (a *Admin) Login(c *httpx.Ctx) {
	user := c.Raw("username")
	pass := c.Raw("password")
	pwd := auth.AdminPassword(pass, a.Cfg.PasswordSecret)
	info, err := dbx.QueryOne(a.DB, `SELECT a.user_id,a.name,a.user AS username,a.status,a.role_id AS user_role_ids,b.role_id,b.name AS role_name,b.status AS role_status
		FROM `+a.table("admin_user")+` a JOIN `+a.table("role")+` b ON a.role_id=b.role_id WHERE a.user=? AND a.pwd=? LIMIT 1`, user, pwd)
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	if info == nil {
		httpx.Fail(c.W, 411, "请检查用户名或者密码")
		return
	}
	if !util.Truthy(info["status"]) || !util.Truthy(info["role_status"]) {
		httpx.Fail(c.W, 411, "该账户被禁用")
		return
	}
	_, _ = dbx.Exec(a.DB, "INSERT INTO "+a.table("log")+"(application_name,username,url,ip,useragent,create_time,type) VALUES (?,?,?,?,?,?,?)",
		"admin", user, c.R.URL.RequestURI(), c.IP(), c.R.UserAgent(), time.Now().Unix(), 1)
	info["time"] = time.Now().Unix()
	raw, _ := json.Marshal(info)
	token, err := auth.Sign(a.Cfg.JWTSecret, a.Cfg.JWTIss, a.Cfg.JWTAud, string(raw), time.Now(), a.Cfg.JWTExpire)
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	exist, _ := dbx.QueryOne(a.DB, "SELECT id FROM "+a.table("admin_token")+" WHERE token=? LIMIT 1", token)
	if exist == nil {
		if !a.Cfg.MultipleLogin {
			_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("admin_token")+" SET dev_status=0 WHERE username=?", user)
		}
		_, _ = dbx.Exec(a.DB, "INSERT INTO "+a.table("admin_token")+"(token,username,status,dev_status) VALUES (?,?,1,1)", token, user)
	}
	httpx.OK(c.W, map[string]any{"token": token})
}

func (a *Admin) Logout(c *httpx.Ctx) {
	token := c.HeaderToken("Authorization")
	if token != "" {
		_, _ = dbx.Exec(a.DB, "DELETE FROM "+a.table("admin_token")+" WHERE token=?", token)
	}
	httpx.OK(c.W, nil)
}

func (a *Admin) Verify(c *httpx.Ctx) {
	cfg, _ := a.baseConfig()
	httpx.JSON(c.W, map[string]any{
		"rememberMe":    true,
		"data":          "",
		"site_title":    cfg["site_title"],
		"success_url":   "/admin/Home/index.html",
		"verify_status": a.Cfg.VerifyStatus,
		"status":        200,
	})
}

func (a *Admin) GetUserInfo(c *httpx.Ctx) {
	user, ok := a.requireAuth(c)
	if !ok {
		return
	}
	uid := util.AsString(user["user_id"])
	row, err := dbx.QueryOne(a.DB, "SELECT role_id,user FROM "+a.table("admin_user")+" WHERE user_id=? LIMIT 1", uid)
	if err != nil || row == nil {
		httpx.Fail(c.W, 411, "用户信息不存在")
		return
	}
	role, _ := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("role")+" WHERE role_id=? LIMIT 1", row["role_id"])
	if role == nil {
		httpx.Fail(c.W, 411, "用户信息不存在")
		return
	}
	menus := a.menuTree()
	if util.AsString(role["role_id"]) != "1" {
		menus = filterMenus(menus, strings.Split(util.AsString(role["access"]), ","))
	}
	comps := components(menus)
	if util.AsString(row["role_id"]) == "1" {
		comps = append(comps,
			map[string]any{"name": "Field", "path": "/admin/Field/index.html", "meta": map[string]any{"title": "字段管理"}, "component_path": "admin/sys/field/Index.vue"},
			map[string]any{"name": "Action", "path": "/admin/Action/index.html", "meta": map[string]any{"title": "方法管理"}, "component_path": "admin/sys/action/Index.vue"},
		)
	}
	comps = append(comps, a.hiddenComponents(role)...)
	cfg, _ := a.baseConfig()
	row["site_title"] = cfg["site_title"]
	row["headimg"] = cfg["logo"]
	row["show_notice"] = a.Cfg.ShowHomeChats || true
	httpx.JSON(c.W, map[string]any{
		"status":     200,
		"menu":       menus,
		"components": comps,
		"actions":    strings.Split(util.AsString(role["access"]), ","),
		"data":       row,
	})
}

func (a *Admin) menuTree() []map[string]any {
	rows, err := dbx.Query(a.DB, "SELECT menu_id,pid,title,controller_name,status,icon,sortid,component_path FROM "+a.table("menu")+" WHERE status=1 AND app_id=1 ORDER BY sortid ASC")
	if err != nil {
		return []map[string]any{}
	}
	var flat []map[string]any
	for _, v := range rows {
		icon := util.AsString(v["icon"])
		if icon == "" {
			icon = "el-icon-menu"
		}
		ctrl := strings.ReplaceAll(util.AsString(v["controller_name"]), "/", ".")
		flat = append(flat, map[string]any{
			"name":           v["controller_name"],
			"pid":            v["pid"],
			"menu_id":        v["menu_id"],
			"title":          v["title"],
			"sortid":         v["sortid"],
			"icon":           icon,
			"path":           "/admin/" + ctrl + "/index.html",
			"component_path": v["component_path"],
		})
	}
	return util.GenerateListTree(flat, "0", "menu_id", "pid")
}

func filterMenus(menus []map[string]any, access []string) []map[string]any {
	allow := map[string]bool{}
	for _, a := range access {
		allow[a] = true
	}
	var out []map[string]any
	for _, m := range menus {
		if !allow[util.AsString(m["name"])] {
			continue
		}
		item := map[string]any{}
		for k, v := range m {
			item[k] = v
		}
		if children, ok := m["children"].([]map[string]any); ok {
			item["children"] = filterMenus(children, access)
		}
		out = append(out, item)
	}
	if out == nil {
		out = []map[string]any{}
	}
	return out
}

func components(menus []map[string]any) []map[string]any {
	var out []map[string]any
	for _, v := range menus {
		out = append(out, map[string]any{
			"name":           v["name"],
			"path":           v["path"],
			"meta":           map[string]any{"title": v["title"]},
			"component_path": v["component_path"],
		})
		if children, ok := v["children"].([]map[string]any); ok {
			out = append(out, components(children)...)
		}
	}
	if out == nil {
		out = []map[string]any{}
	}
	return out
}

func (a *Admin) hiddenComponents(role map[string]any) []map[string]any {
	rows, _ := dbx.Query(a.DB, "SELECT controller_name,title,component_path FROM "+a.table("menu")+" WHERE app_id=1 AND status=0 AND component_path<>''")
	access := strings.Split(util.AsString(role["access"]), ",")
	allow := map[string]bool{}
	for _, x := range access {
		allow[x] = true
	}
	var out []map[string]any
	for _, v := range rows {
		name := util.AsString(v["controller_name"])
		if util.AsString(role["role_id"]) != "1" && !allow[name] {
			continue
		}
		ctrl := strings.ReplaceAll(name, "/", ".")
		out = append(out, map[string]any{
			"name":           name,
			"path":           "/admin/" + ctrl + "/index.html",
			"meta":           map[string]any{"title": v["title"]},
			"component_path": v["component_path"],
		})
	}
	return out
}

func (a *Admin) baseConfig() (map[string]string, error) {
	rows, err := dbx.Query(a.DB, "SELECT name,data FROM "+a.table("base_config"))
	if err != nil {
		return map[string]string{}, err
	}
	out := map[string]string{}
	for _, r := range rows {
		out[util.AsString(r["name"])] = util.AsString(r["data"])
	}
	return out, nil
}

func (a *Admin) HomeIndex(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	days := util.DaysOfMonth(time.Now().Format("2006-01-02"))
	data := make([]any, 0, len(days))
	for _, day := range days {
		start, end := util.DayRange(padDay(day))
		n, _ := dbx.Scalar(a.DB, "SELECT IFNULL(SUM(pay_money),0) FROM "+a.table("order")+" WHERE order_status>0 AND create_time>=? AND create_time<?", start, end)
		data = append(data, n)
	}
	cards := a.cardData()
	body := map[string]any{
		"status":    200,
		"card_data": cards,
		"menus":     a.homeMenus(),
	}
	if a.Cfg.ShowHomeChats {
		body["echat_data"] = map[string]any{
			"day_count": map[string]any{"title": "当月业绩折线图", "day": days, "data": data},
			"cycle_data": map[string]any{
				"title":    "网站访问类别",
				"catagory": []string{"超级管理员", "管理员", "作者", "普通用户", "普通用户", "访客"},
				"data": []map[string]any{
					{"value": 0, "name": "超级管理员"},
					{"value": 0, "name": "管理员"},
					{"value": 0, "name": "作者"},
					{"value": 0, "name": "普通用户"},
					{"value": 0, "name": "访客"},
				},
			},
		}
	}
	httpx.JSON(c.W, body)
}

func padDay(day string) string {
	parts := strings.Split(day, "-")
	if len(parts) != 3 {
		return day
	}
	y, _ := strconv.Atoi(parts[0])
	m, _ := strconv.Atoi(parts[1])
	d, _ := strconv.Atoi(parts[2])
	return fmt.Sprintf("%04d-%02d-%02d", y, m, d)
}

func (a *Admin) cardData() []map[string]any {
	start, end := util.DayRange(time.Now().Format("2006-01-02"))
	n1, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("technician")+" WHERE status=1 AND create_time>=? AND create_time<?", start, end)
	n2, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("technician")+" WHERE status=1")
	n3, _ := dbx.Scalar(a.DB, "SELECT IFNULL(SUM(left_money),0) FROM "+a.table("withdraw_apply")+" WHERE status=1 AND create_time>=? AND create_time<?", start, end)
	n4, _ := dbx.Scalar(a.DB, "SELECT IFNULL(SUM(left_money),0) FROM "+a.table("withdraw_apply"))
	n5, _ := dbx.Scalar(a.DB, "SELECT IFNULL(SUM(pay_money),0) FROM "+a.table("order")+" WHERE order_status>1 AND create_time>=? AND create_time<?", start, end)
	n6, _ := dbx.Scalar(a.DB, "SELECT IFNULL(SUM(pay_money),0) FROM "+a.table("order")+" WHERE order_status>1")
	n7, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("user")+" WHERE create_time>=? AND create_time<?", start, end)
	n8, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("user"))
	return []map[string]any{
		{"card_title_icon": "el-icon-user", "card_title": "技师数量", "card_cycle": "日", "card_cycle_back_color": "#409EFF", "bottom_title": "技师总数量", "vist_num": n1, "vist_all_num": n2, "vist_all_icon": "el-icon-trophy"},
		{"card_title_icon": "el-icon-download", "card_title": "提现", "card_cycle": "日", "card_cycle_back_color": "#67C23A", "bottom_title": "总提现数", "vist_num": n3, "vist_all_num": n4, "vist_all_icon": "el-icon-download"},
		{"card_title_icon": "el-icon-wallet", "card_title": "收入", "card_cycle": "日", "card_cycle_back_color": "#F56C6C", "bottom_title": "总收入", "vist_num": n5, "vist_all_num": n6, "vist_all_icon": "el-icon-coin"},
		{"card_title_icon": "el-icon-coordinate", "card_title": "用户", "card_cycle": "日", "card_cycle_back_color": "#E6A23C", "bottom_title": "总用户", "vist_num": n7, "vist_all_num": n8, "vist_all_icon": "el-icon-data-line"},
	}
}

func (a *Admin) homeMenus() []map[string]any {
	if !a.Cfg.ShowHomeMenu {
		return nil
	}
	rows, _ := dbx.Query(a.DB, "SELECT title,menu_pic,controller_name FROM "+a.table("menu")+" WHERE home_show=1 LIMIT 8")
	for i, v := range rows {
		ctrl := strings.ReplaceAll(util.AsString(v["controller_name"]), "/", ".")
		rows[i]["url"] = "/admin/" + ctrl + "/index.html"
	}
	return rows
}

func (a *Admin) GetNotice(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	start, end := util.DayRange(time.Now().Format("2006-01-02"))
	n1, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("order")+" WHERE order_status>0 AND create_time>=? AND create_time<?", start, end)
	n2, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("technician_apply")+" WHERE status=0")
	n3, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("technician")+" WHERE status=0")
	n4, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("withdraw_apply")+" WHERE status=1")
	httpx.OK(c.W, map[string]any{"data": []map[string]any{
		{"num": n1, "title": "条订单待处理（今日）", "url": "/admin/Order/index.html"},
		{"num": n2, "title": "技师入驻申请", "url": "/admin/Technicianapply/index.html"},
		{"num": n3, "title": "技师待审核处理", "url": "/admin/Technician/index.html"},
		{"num": n4, "title": "提现申请待处理", "url": "/admin/Withdrawapply/index.html"},
	}})
}

func (a *Admin) FileList(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	page := c.Int("page", 1)
	limit := c.Int("limit", 20)
	p, err := dbx.Paginate(a.DB, a.table("file"), "id,filepath,hash,create_time", "1=1", "id desc", nil, page, limit)
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	httpx.OK(c.W, map[string]any{"data": p})
}

func (a *Admin) DeleteFile(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	fp := c.Raw("filepath")
	if fp == "" {
		httpx.Fail(c.W, 411, "请选择图片")
		return
	}
	ids := strings.Split(fp, ",")
	ph := make([]string, len(ids))
	args := make([]any, len(ids))
	for i, id := range ids {
		ph[i] = "?"
		args[i] = id
	}
	_, err := dbx.Exec(a.DB, "DELETE FROM "+a.table("file")+" WHERE filepath IN ("+strings.Join(ph, ",")+")", args...)
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	httpx.OK(c.W, map[string]any{"msg": "操作成功"})
}

func (a *Admin) ResetPwd(c *httpx.Ctx) {
	user, ok := a.requireAuth(c)
	if !ok {
		return
	}
	password := c.Raw("password")
	if password == "" {
		httpx.Fail(c.W, 411, "密码不能为空")
		return
	}
	pwd := auth.AdminPassword(password, a.Cfg.PasswordSecret)
	_, err := dbx.Exec(a.DB, "UPDATE "+a.table("admin_user")+" SET pwd=? WHERE user_id=?", pwd, user["user_id"])
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	httpx.OK(c.W, map[string]any{"msg": "操作成功"})
}

func (a *Admin) ClearCache(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	httpx.OK(c.W, map[string]any{"msg": "操作成功"})
}

func (a *Admin) GetRoleMenus(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	rows, err := dbx.Query(a.DB, "SELECT menu_id,title,controller_name,status,icon,sortid,pid FROM "+a.table("menu")+" WHERE app_id=1 ORDER BY sortid ASC")
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	httpx.JSON(c.W, map[string]any{"status": 200, "menus": a.nodeMenus(rows, "0")})
}

func (a *Admin) nodeMenus(rows []map[string]any, pid string) []map[string]any {
	var menus []map[string]any
	for _, val := range rows {
		if util.AsString(val["pid"]) != pid {
			continue
		}
		item := map[string]any{
			"sortid": val["sortid"],
			"access": val["controller_name"],
			"title":  util.AsString(val["title"]) + " (/admin/" + util.AsString(val["controller_name"]) + ")",
		}
		children := a.nodeMenus(rows, util.AsString(val["menu_id"]))
		funs := a.funs(val)
		if len(children) > 0 {
			if len(funs) > 0 {
				item["children"] = append(funs, children...)
			} else {
				item["children"] = children
			}
		} else if len(funs) > 0 {
			item["children"] = funs
		}
		menus = append(menus, item)
	}
	if menus == nil {
		return []map[string]any{}
	}
	return menus
}

func (a *Admin) funs(info map[string]any) []map[string]any {
	list, _ := dbx.Query(a.DB, "SELECT name,action_name FROM "+a.table("action")+" WHERE menu_id=? ORDER BY sortid ASC", info["menu_id"])
	var out []map[string]any
	ctrl := util.AsString(info["controller_name"])
	for _, val := range list {
		path := "/admin/" + ctrl + "/" + util.AsString(val["action_name"]) + ".html"
		out = append(out, map[string]any{"title": util.AsString(val["name"]) + " " + path, "access": path})
	}
	return out
}

func (a *Admin) BaseconfigIndex(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	data := map[string]any{}
	for k, v := range c.In {
		data[k] = v
	}
	if kw, ok := data["keyword"].([]any); ok {
		parts := make([]string, len(kw))
		for i, item := range kw {
			parts[i] = util.AsString(item)
		}
		data["keyword"] = strings.Join(parts, ",")
	}
	info, _ := a.baseConfig()
	for k, v := range data {
		val := util.AsString(v)
		if _, exists := info[k]; exists {
			_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("base_config")+" SET data=? WHERE name=?", val, k)
		} else {
			_, _ = dbx.Exec(a.DB, "INSERT INTO "+a.table("base_config")+"(name,data,remarks) VALUES (?,?,?)", k, val, "")
		}
	}
	httpx.OK(c.W, nil)
}

func (a *Admin) BaseconfigInfo(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	info, err := a.baseConfig()
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	out := map[string]any{}
	for k, v := range info {
		out[k] = v
	}
	if kw, ok := out["keyword"].(string); ok {
		if kw == "" {
			out["keyword"] = []string{}
		} else {
			out["keyword"] = strings.Split(kw, ",")
		}
	}
	out["water_alpha"] = util.AsInt(out["water_alpha"], 0)
	httpx.OK(c.W, map[string]any{"data": out})
}

func (a *Admin) ValuationIndex(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	id := c.Raw("id")
	if id == "" {
		httpx.Fail(c.W, 411, "参数错误")
		return
	}
	cols, vals := a.pick(c, nil)
	if len(cols) == 0 {
		httpx.OK(c.W, nil)
		return
	}
	if err := dbx.Update(a.DB, a.table("valuation"), "id", id, cols, vals); err != nil {
		httpx.Fail(c.W, 411, err.Error())
		return
	}
	httpx.OK(c.W, nil)
}

func (a *Admin) ValuationInfo(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	name := c.Str("name")
	row, err := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("valuation")+" WHERE name=? LIMIT 1", name)
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	httpx.OK(c.W, map[string]any{"data": row})
}

func (a *Admin) pick(c *httpx.Ctx, allow []string) ([]string, []any) {
	allowSet := map[string]bool{}
	for _, f := range allow {
		if f != "" {
			allowSet[f] = true
		}
	}
	var cols []string
	var vals []any
	for k, v := range c.In {
		if k == "s" || strings.HasPrefix(k, "_") {
			continue
		}
		if len(allowSet) > 0 && !allowSet[k] {
			continue
		}
		if !util.SafeIdent(k) {
			continue
		}
		cols = append(cols, k)
		switch t := v.(type) {
		case []any, map[string]any:
			b, _ := json.Marshal(t)
			vals = append(vals, string(b))
		default:
			vals = append(vals, util.AsString(v))
		}
	}
	return cols, vals
}

func (a *Admin) Upload(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	if err := c.R.ParseMultipartForm(32 << 20); err != nil && c.R.MultipartForm == nil {
		httpx.Fail(c.W, 411, "文件类型验证失败")
		return
	}
	file, hdr, err := c.R.FormFile("file")
	if err != nil {
		httpx.Fail(c.W, 411, "文件类型验证失败")
		return
	}
	defer file.Close()
	cfg, _ := a.baseConfig()
	ext := strings.ToLower(strings.TrimPrefix(filepath.Ext(hdr.Filename), "."))
	if cfg["filetype"] != "" && !containsExt(cfg["filetype"], ext) {
		httpx.Fail(c.W, 411, "文件类型验证失败")
		return
	}
	if maxMB := util.AsInt(cfg["filesize"], 0); maxMB > 0 && hdr.Size > int64(maxMB)*1024*1024 {
		httpx.Fail(c.W, 411, "文件大小验证失败")
		return
	}
	data, err := io.ReadAll(file)
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	sum := md5.Sum(data)
	hash := hex.EncodeToString(sum[:])
	if a.Cfg.CheckFileExist {
		if old, _ := dbx.QueryOne(a.DB, "SELECT filepath FROM "+a.table("file")+" WHERE hash=? LIMIT 1", hash); old != nil {
			httpx.OK(c.W, map[string]any{"data": old["filepath"], "filestatus": true})
			return
		}
	}
	dir := filepath.Join(a.Cfg.UploadRoot, "admin", time.Now().Format("200601"))
	if err := os.MkdirAll(dir, 0o755); err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	name := strconv.FormatInt(time.Now().UnixNano(), 10) + "." + ext
	if err := os.WriteFile(filepath.Join(dir, name), data, 0o644); err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	rel := "/uploads/admin/" + time.Now().Format("200601") + "/" + name
	domain := strings.TrimRight(a.Cfg.PublicDomain, "/")
	if domain == "" {
		host := c.R.Host
		if host == "" {
			host = "127.0.0.1:8088"
		}
		domain = "http://" + host
	}
	url := domain + rel
	if strings.HasPrefix(hdr.Header.Get("Content-Type"), "image") || isImage(ext) {
		_, _ = dbx.Exec(a.DB, "INSERT INTO "+a.table("file")+"(filepath,hash,create_time) VALUES (?,?,?)", url, hash, time.Now().Unix())
	}
	httpx.OK(c.W, map[string]any{"data": url})
}

func (a *Admin) CreateFile(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	fp := c.Raw("filepath")
	base := filepath.Base(fp)
	parts := strings.Split(base, ".")
	if len(parts) == 2 && imageExt(parts[1]) {
		_, _ = dbx.Exec(a.DB, "INSERT INTO "+a.table("file")+"(filepath,hash,create_time) VALUES (?,?,?)", fp, parts[0], time.Now().Unix())
	}
	httpx.OK(c.W, nil)
}

func containsExt(list, ext string) bool {
	for _, item := range strings.Split(strings.ToLower(list), ",") {
		if strings.TrimSpace(item) == ext {
			return true
		}
	}
	return false
}

func isImage(ext string) bool { return imageExt(ext) }

func imageExt(ext string) bool {
	switch strings.ToLower(ext) {
	case "jpg", "jpeg", "png", "gif", "bmp", "webp":
		return true
	}
	return false
}

func actionOf(path string) string {
	path = strings.TrimSuffix(strings.TrimSuffix(path, "/"), ".html")
	parts := strings.Split(path, "/")
	if len(parts) == 0 {
		return ""
	}
	return strings.ToLower(parts[len(parts)-1])
}

func (a *Admin) sqlOptions(sql string) ([]map[string]any, error) {
	rows, err := dbx.Query(a.DB, strings.ReplaceAll(sql, "pre_", a.Cfg.DBPrefix))
	if err != nil {
		return nil, err
	}
	var out []map[string]any
	for _, row := range rows {
		keys := make([]string, 0, len(row))
		for k := range row {
			keys = append(keys, k)
		}
		if len(keys) < 2 {
			continue
		}
		// Query map loses column order. Callers pass known pairs.
		out = append(out, row)
	}
	return out, nil
}

func pairOptions(rows []map[string]any, valKey, keyKey string) []map[string]any {
	out := make([]map[string]any, 0, len(rows))
	for _, row := range rows {
		item := map[string]any{"key": row[keyKey], "val": row[valKey]}
		out = append(out, item)
	}
	return out
}

func percentage(page, total, limit int) int {
	if limit <= 0 {
		limit = 1000
	}
	pages := int(math.Ceil(float64(total) / float64(limit)))
	if pages <= 0 {
		return 100
	}
	return int(math.Ceil(float64(page) * 100 / float64(pages)))
}

var _ = sql.ErrNoRows
