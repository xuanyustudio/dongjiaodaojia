package api

import (
	"encoding/json"
	"fmt"
	"math"
	"strconv"
	"strings"
	"time"

	"dongjiao/server/internal/auth"
	"dongjiao/server/internal/dbx"
	"dongjiao/server/internal/httpx"
	"dongjiao/server/internal/util"
)

type API struct{ *httpx.Deps }

func New(d *httpx.Deps) *API { return &API{Deps: d} }

type RouteRegistrar interface {
	Handle(appName, controller, action string, h httpx.Handler)
}

func (a *API) Register(r RouteRegistrar) {
	r.Handle("api", "index", "index", a.Index)
	r.Handle("api", "index", "service", a.Service)
	r.Handle("api", "index", "serviceDetails", a.ServiceDetails)
	r.Handle("api", "index", "comment", a.Comment)
	r.Handle("api", "index", "coupon", a.auth(a.CouponCenter))
	r.Handle("api", "index", "receive", a.auth(a.Receive))
	r.Handle("api", "index", "upload", a.auth(a.Upload))
	r.Handle("api", "user", "register", a.UserRegister)
	r.Handle("api", "user", "login", a.Login)
	r.Handle("api", "user", "editPassword", a.EditPassword)
	r.Handle("api", "user", "config", a.Config)
	r.Handle("api", "user", "smsCode", a.SmsCode)
	r.Handle("api", "user", "cs", a.Cs)
	r.Handle("api", "user", "userInfo", a.UserInfo)
	r.Handle("api", "user", "editUser", a.auth(a.EditUser))
	r.Handle("api", "user", "technicianApply", a.auth(a.TechnicianApply))
	r.Handle("api", "user", "technicianApply2", a.TechnicianApply2)
	r.Handle("api", "user", "customerService", a.auth(a.CustomerService))
	r.Handle("api", "technician", "index", a.TechnicianIndex)
	r.Handle("api", "technician", "details", a.auth(a.TechnicianDetails))
	r.Handle("api", "order", "upload", a.Upload)
	r.Handle("api", "order", "cleanGarbageData", a.CleanGarbage)
	r.Handle("api", "order", "confirmCancel", a.ConfirmCancel)
	r.Handle("api", "pay", "pay", a.auth(a.Pay))
	r.Handle("api", "share", "index", a.auth(a.ShareIndex))
	r.Handle("api", "share", "team", a.auth(a.ShareTeam))
	r.Handle("api", "coupon", "index", a.auth(a.CouponIndex))
	r.Handle("api", "coupon", "exchange", a.auth(a.CouponExchange))
	r.Handle("api", "news", "index", a.auth(a.News))
	r.Handle("api", "member", "index", a.auth(a.MemberIndex))
	r.Handle("api", "member", "memberList", a.auth(a.MemberList))
	r.Handle("api", "member", "createOrder", a.auth(a.MemberCreate))
	r.Handle("api", "recharge", "index", a.auth(a.RechargeIndex))
	r.Handle("api", "recharge", "rechargeList", a.auth(a.RechargeList))
	r.Handle("api", "recharge", "createOrder", a.auth(a.RechargeCreate))
	r.Handle("api", "recharge", "orderList", a.auth(a.RechargeOrders))
	r.Handle("api", "recharge", "consumeList", a.auth(a.ConsumeList))
	r.Handle("api", "recharge", "refundList", a.auth(a.RefundList))
	r.Handle("api", "recharge", "brokerageList", a.auth(a.BrokerageList))
	r.Handle("api", "opinion", "index", a.auth(a.OpinionIndex))
	r.Handle("api", "opinion", "opinionList", a.auth(a.OpinionList))
	r.Handle("api", "opinion", "createOpinion", a.auth(a.CreateOpinion))
	r.Handle("api", "pay", "payWay", a.auth(a.PayWay))
	r.Handle("api", "pay", "is_pay", a.auth(a.IsPay))
	r.Handle("api", "address", "index", a.auth(a.AddressIndex))
	r.Handle("api", "address", "details", a.auth(a.AddressDetails))
	r.Handle("api", "address", "editAddress", a.auth(a.AddressEdit))
	r.Handle("api", "address", "del", a.auth(a.AddressDel))
	r.Handle("api", "order", "getDistance", a.auth(a.GetDistance))
	r.Handle("api", "order", "settlement", a.auth(a.Settlement))
	r.Handle("api", "order", "serviceDate", a.auth(a.ServiceDate))
	r.Handle("api", "order", "dateList", a.auth(a.DateList))
	r.Handle("api", "order", "coupon", a.auth(a.OrderCoupon))
	r.Handle("api", "order", "createOrder", a.auth(a.CreateOrder))
	r.Handle("api", "order", "orderList", a.auth(a.OrderList))
	r.Handle("api", "order", "orderDetails", a.auth(a.OrderDetails))
	r.Handle("api", "order", "speclist", a.auth(a.SpecList))
	r.Handle("api", "order", "cancelOrder", a.auth(a.CancelOrder))
	r.Handle("api", "order", "completeOrder", a.auth(a.CompleteOrder))
	r.Handle("api", "order", "complaint", a.auth(a.Complaint))
	r.Handle("api", "order", "userInstructions", a.auth(a.UserInstructions))
	r.Handle("api", "order", "commentType", a.auth(a.CommentType))
	r.Handle("api", "order", "comment", a.auth(a.OrderComment))
	r.Handle("api", "collect", "index", a.auth(a.CollectIndex))
	r.Handle("api", "collect", "collect", a.auth(a.Collect))
	r.Handle("api", "wallet", "aliBind", a.auth(a.AliBind))
	r.Handle("api", "wallet", "wallet", a.auth(a.Wallet))
	r.Handle("api", "wallet", "withdraw", a.auth(a.Withdraw))
	r.Handle("api", "wallet", "withdrawApply", a.auth(a.WithdrawApply))
	r.Handle("api", "wallet", "withdrawList", a.auth(a.WithdrawList))
}

func (a *API) auth(next httpx.Handler) httpx.Handler {
	return func(c *httpx.Ctx) {
		token := c.HeaderToken("token")
		if token == "" || token == "null" {
			httpx.Msg(c.W, "0", "请先登录", "", 0)
			return
		}
		id, ok := a.Tokens.Get("user_token", token)
		if !ok {
			httpx.Msg(c.W, "0", "登录已过期，请重新登录", "", 0)
			return
		}
		c.In["__uid"] = id
		next(c)
	}
}

func (a *API) uid(c *httpx.Ctx) int64 {
	switch v := c.In["__uid"].(type) {
	case int64:
		return v
	case int:
		return int64(v)
	default:
		return int64(util.AsInt(v, 0))
	}
}

func (a *API) table(name string) string { return a.Cfg.DBPrefix + name }

func (a *API) Login(c *httpx.Ctx) {
	username := strings.TrimSpace(c.Raw("username"))
	if username == "" {
		httpx.Msg(c.W, 0, "账号不能为空", "", 0)
		return
	}
	if c.Raw("type") == "2" && c.Raw("sms_code") != "888888" {
		httpx.Msg(c.W, 0, "验证码不正确", "", 0)
		return
	}
	user, err := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("user")+" WHERE username=? LIMIT 1", username)
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	if user == nil {
		if c.Raw("type") != "2" && c.Raw("password") == "" {
			httpx.Msg(c.W, 0, "password不能为空", "", 0)
			return
		}
		now := time.Now().Unix()
		id, err := dbx.Insert(a.DB, a.table("user"),
			[]string{"username", "mobile", "nickname", "avatar", "password", "brokerage", "create_time"},
			[]any{username, username, "用户" + username, "/public/uploads/api/202201/avatar.jpg", "", 0, now})
		if err != nil {
			httpx.Msg(c.W, 0, "网络异常，注册失败", "", 0)
			return
		}
		token := auth.RandomHex(16)
		a.Tokens.Set("user_token", token, id, 7*24*time.Hour)
		_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("user")+" SET token=? WHERE id=?", token, id)
		httpx.Msg(c.W, 3, "登录成功", token, 0)
		return
	}
	if c.Raw("type") != "2" {
		if util.AsString(user["password"]) == "" {
			httpx.Msg(c.W, 0, "该账号暂未设置密码，请使用验证码登录", "", 0)
			return
		}
		if !auth.CheckUserPassword(util.AsString(user["password"]), c.Raw("password")) {
			httpx.Msg(c.W, 0, "请检查账号密码", "", 0)
			return
		}
	}
	token := auth.RandomHex(16)
	_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("user")+" SET token=?, last_ip=?, last_login=? WHERE id=?", token, c.IP(), time.Now().Unix(), user["id"])
	a.Tokens.Set("user_token", token, int64(util.AsInt(user["id"], 0)), 7*24*time.Hour)
	httpx.Msg(c.W, 1, "登录成功", token, 0)
}

func (a *API) UserRegister(c *httpx.Ctx) {
	mobile := c.Raw("mobile")
	if mobile == "" {
		httpx.Msg(c.W, 0, "手机号不能为空", "", 0)
		return
	}
	exist, _ := dbx.QueryOne(a.DB, "SELECT id FROM "+a.table("user")+" WHERE mobile=? LIMIT 1", mobile)
	if exist != nil {
		httpx.Msg(c.W, 0, "该手机已注册", "", 0)
		return
	}
	pwd := ""
	if c.Raw("password") != "" {
		hash, err := auth.HashUserPassword(c.Raw("password"))
		if err != nil {
			httpx.Msg(c.W, 0, err.Error(), "", 0)
			return
		}
		pwd = hash
	}
	id, err := dbx.Insert(a.DB, a.table("user"),
		[]string{"username", "nickname", "mobile", "password", "avatar", "brokerage", "create_time"},
		[]any{mobile, "用户" + mobile, mobile, pwd, "/public/uploads/api/202201/avatar.jpg", 0, time.Now().Unix()})
	if err != nil || id == 0 {
		httpx.Msg(c.W, 0, "网络异常，注册失败", "", 0)
		return
	}
	httpx.Msg(c.W, 1, "注册成功", "", 0)
}

func (a *API) UserInfo(c *httpx.Ctx) {
	id := a.uid(c)
	if id == 0 {
		token := c.HeaderToken("token")
		if got, ok := a.Tokens.Get("user_token", token); ok {
			id = got
		}
	}
	if id == 0 {
		httpx.Msg(c.W, "0", "请先登录", "", 0)
		return
	}
	row, err := dbx.QueryOne(a.DB, "SELECT id,nickname,username,realname,mobile,avatar,balance,brokerage,is_vip,praise,status FROM "+a.table("user")+" WHERE id=?", id)
	if err != nil || row == nil {
		httpx.Msg(c.W, 0, "用户不存在", "", 0)
		return
	}
	httpx.Msg(c.W, 1, "", row, 0)
}

func (a *API) Config(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT name,data FROM "+a.table("base_config"))
	out := map[string]any{}
	for _, row := range rows {
		out[util.AsString(row["name"])] = row["data"]
	}
	httpx.Msg(c.W, 1, "", out, 0)
}

func (a *API) Index(c *httpx.Ctx) {
	banners, err := dbx.Query(a.DB, "SELECT id,image FROM "+a.table("banner")+" WHERE type=0 ORDER BY sort DESC,id DESC")
	if err != nil || banners == nil {
		banners = []map[string]any{}
	}
	httpx.Msg(c.W, 1, "", banners, len(banners))
}

func (a *API) Service(c *httpx.Ctx) {
	page := util.AsInt(c.In["page"], 1)
	if page < 1 {
		page = 1
	}
	orderBy, order := "sort", "DESC"
	switch util.AsInt(c.In["orderBy"], 0) {
	case 1:
		orderBy, order = "price", "ASC"
	case 2:
		orderBy, order = "sales", "DESC"
	case 3:
		orderBy, order = "praise", "DESC"
	}
	const limit = 10
	q := "SELECT id,name,original_price,price,cover,discount,sales,praise,duration,label FROM " + a.table("service") + " WHERE `show`=1 ORDER BY " + orderBy + " " + order + ",id DESC LIMIT ? OFFSET ?"
	rows, err := dbx.Query(a.DB, q, limit, (page-1)*limit)
	if err != nil {
		q = "SELECT id,name,original_price,price,cover,sales,praise,duration FROM " + a.table("service") + " WHERE `show`=1 ORDER BY " + orderBy + " " + order + ",id DESC LIMIT ? OFFSET ?"
		rows, err = dbx.Query(a.DB, q, limit, (page-1)*limit)
	}
	if err != nil || rows == nil {
		rows = []map[string]any{}
	}
	for i := range rows {
		rows[i]["coupon"] = 0
		n := util.AsFloat(rows[i]["discount"])
		if n != 0 && int(n)%10 == 0 {
			rows[i]["discount"] = n / 10
		}
	}
	var count int
	_ = a.DB.QueryRow("SELECT COUNT(*) FROM " + a.table("service") + " WHERE `show`=1").Scan(&count)
	pages := 0
	if count > 0 {
		pages = (count + limit - 1) / limit
	}
	httpx.Msg(c.W, 1, "", map[string]any{"list": rows, "total": pages, "page": page}, count)
}

func (a *API) CleanGarbage(c *httpx.Ctx) {
	httpx.Msg(c.W, 1, "", "", 0)
}

func (a *API) ServiceDetails(c *httpx.Ctx) {
	id := c.Raw("id")
	row, err := dbx.QueryOne(a.DB, "SELECT id,banner,praise,introduce FROM "+a.table("service")+" WHERE id=? LIMIT 1", id)
	if err != nil || row == nil {
		httpx.Msg(c.W, 0, "服务不存在", "", 0)
		return
	}
	banner := []any{}
	if s := util.AsString(row["banner"]); s != "" {
		var parsed any
		if json.Unmarshal([]byte(s), &parsed) == nil {
			if arr, ok := parsed.([]any); ok {
				banner = arr
			}
		}
	}
	row["banner"] = banner
	row["praise"] = int(util.AsFloat(row["praise"]) * 20)
	n, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("comment")+" WHERE service_id=?", id)
	row["comment_count"] = n

	specs, _ := dbx.Query(a.DB, "SELECT id,name,price,duration,sort FROM "+a.table("service_spec")+" WHERE service_id=? ORDER BY sort DESC, price ASC", id)
	comments, _ := dbx.Query(a.DB, "SELECT id,name FROM "+a.table("types")+" WHERE type='comment'")
	explain, _ := dbx.Query(a.DB, "SELECT name,content FROM "+a.table("order_explain")+" WHERE service_id=?", id)

	vip := map[string]any{"is_vip": 0}
	if uid := a.uid(c); uid > 0 {
		if u, _ := dbx.QueryOne(a.DB, "SELECT is_vip FROM "+a.table("user")+" WHERE id=?", uid); u != nil {
			vip["is_vip"] = util.AsInt(u["is_vip"], 0)
		}
	}
	if util.AsInt(vip["is_vip"], 0) == 0 {
		discount := 0
		if rights, _ := dbx.QueryOne(a.DB, "SELECT value FROM "+a.table("member_rights")+" WHERE id=2"); rights != nil {
			discount = util.AsInt(rights["value"], 0)
		}
		if discount%10 == 0 {
			discount = discount / 10
		}
		vip["vip_discount"] = discount
	}
	httpx.Msg(c.W, 1, "", map[string]any{
		"service": row,
		"vip":     vip,
		"spec":    listOrEmpty(specs),
		"comment": listOrEmpty(comments),
		"config":  listOrEmpty(explain),
	}, 0)
}

func listOrEmpty(rows []map[string]any) []map[string]any {
	if rows == nil {
		return []map[string]any{}
	}
	return rows
}

func (a *API) Comment(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("comment")+" ORDER BY id DESC LIMIT 20")
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) News(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT id,title,cover,create_time FROM "+a.table("news")+" ORDER BY id DESC")
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) Cs(c *httpx.Ctx) { a.CustomerService(c) }

func (a *API) CustomerService(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(a.DB, "SELECT data FROM "+a.table("base_config")+" WHERE name='customer_service' LIMIT 1")
	httpx.Msg(c.W, 1, "", configObject(row), 0)
}

func configObject(row map[string]any) map[string]any {
	if row == nil {
		return map[string]any{}
	}
	var obj map[string]any
	if json.Unmarshal([]byte(util.AsString(row["data"])), &obj) == nil && obj != nil {
		return obj
	}
	return map[string]any{}
}

func (a *API) SmsCode(c *httpx.Ctx) {
	httpx.Msg(c.W, 1, "发送成功", "", 0)
}

func (a *API) EditPassword(c *httpx.Ctx) {
	mobile := c.Raw("mobile")
	if mobile == "" {
		mobile = c.Raw("username")
	}
	pwd := c.Raw("password")
	if mobile == "" || pwd == "" {
		httpx.Msg(c.W, 0, "参数错误", "", 0)
		return
	}
	hash, err := auth.HashUserPassword(pwd)
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	n, err := dbx.Exec(a.DB, "UPDATE "+a.table("user")+" SET password=? WHERE mobile=? OR username=?", hash, mobile, mobile)
	if err != nil || n == 0 {
		httpx.Msg(c.W, 0, "用户不存在", "", 0)
		return
	}
	httpx.Msg(c.W, 1, "修改成功", "", 0)
}

func (a *API) EditUser(c *httpx.Ctx) {
	id := a.uid(c)
	cols := []string{}
	vals := []any{}
	for _, f := range []string{"nickname", "avatar", "sex", "realname"} {
		if _, ok := c.In[f]; ok {
			cols = append(cols, f)
			vals = append(vals, c.Raw(f))
		}
	}
	if len(cols) > 0 {
		_ = dbx.Update(a.DB, a.table("user"), "id", id, cols, vals)
	}
	httpx.Msg(c.W, 1, "修改成功", "", 0)
}

func (a *API) TechnicianApply2(c *httpx.Ctx) {
	realname := strings.TrimSpace(c.Raw("realname"))
	mobile := strings.TrimSpace(c.Raw("mobile"))
	if realname == "" || mobile == "" {
		httpx.Msg(c.W, 0, "请填写姓名和手机号", "", 0)
		return
	}
	if row, _ := dbx.QueryOne(a.DB, "SELECT id FROM "+a.table("technician_apply")+" WHERE mobile=? LIMIT 1", mobile); row != nil {
		httpx.Msg(c.W, 0, "您已经申请过，请勿重复申请", "", 0)
		return
	}
	_, err := dbx.Insert(a.DB, a.table("technician_apply"),
		[]string{"realname", "mobile", "sex", "age", "city", "avatar", "user_id", "status", "create_time"},
		[]any{realname, mobile, c.Raw("sex"), c.Raw("age"), c.Raw("city"), c.Raw("avatar"), 0, 0, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "申请成功", "", 0)
}

func (a *API) TechnicianApply(c *httpx.Ctx) {
	_, err := dbx.Insert(a.DB, a.table("technician_apply"),
		[]string{"realname", "mobile", "sex", "age", "city", "avatar", "user_id", "status", "create_time"},
		[]any{c.Raw("realname"), c.Raw("mobile"), c.Raw("sex"), c.Raw("age"), c.Raw("city"), c.Raw("avatar"), a.uid(c), 0, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "提交成功", "", 0)
}

func (a *API) TechnicianIndex(c *httpx.Ctx) {
	page := util.AsInt(c.In["page"], 1)
	if page < 1 {
		page = 1
	}
	const limit = 5
	lat := util.AsFloat(c.In["lat"])
	lng := util.AsFloat(c.In["lng"])
	where := " WHERE a.status=1"
	args := []any{}
	if name := strings.TrimSpace(c.Raw("realname")); name != "" {
		where += " AND a.realname LIKE ?"
		args = append(args, "%"+name+"%")
	}
	dist := "0"
	distArgs := []any{}
	if lat != 0 || lng != 0 {
		dist = `CASE WHEN a.lat IS NULL OR a.lat='' OR a.lng IS NULL OR a.lng='' THEN 0 ELSE IFNULL(ROUND(6378.138 * 2 * ASIN(SQRT(
			POW(SIN((? * PI() / 180 - a.lat * PI() / 180) / 2), 2)
			+ COS(? * PI() / 180) * COS(a.lat * PI() / 180) * POW(SIN((? * PI() / 180 - a.lng * PI() / 180) / 2), 2)
		)), 2), 0) END`
		distArgs = []any{lat, lat, lng}
	}
	q := "SELECT a.id,a.realname,a.avatar,a.praise,IFNULL(a.sales,0)+IFNULL(a.fquantity,0) sales,a.label,a.hframe,a.hrocket,a.hhot,a.work_status,IFNULL(b.practice,'') practice,(SELECT COUNT(*) FROM " + a.table("comment") + " c WHERE c.technician_id=a.id) comment," + dist + " distance FROM " + a.table("technician") + " a LEFT JOIN " + a.table("technician_data") + " b ON a.id=b.technician_id" + where + " ORDER BY a.work_status DESC, distance ASC, a.id DESC LIMIT ? OFFSET ?"
	qArgs := make([]any, 0, len(distArgs)+len(args)+2)
	qArgs = append(qArgs, distArgs...)
	qArgs = append(qArgs, args...)
	qArgs = append(qArgs, limit, (page-1)*limit)
	rows, err := dbx.Query(a.DB, q, qArgs...)
	if err != nil {
		q = "SELECT a.id,a.realname,a.avatar,a.praise,a.sales,a.label,a.work_status,0 distance,'' practice,0 comment FROM " + a.table("technician") + " a" + where + " ORDER BY a.id DESC LIMIT ? OFFSET ?"
		fb := make([]any, 0, len(args)+2)
		fb = append(fb, args...)
		fb = append(fb, limit, (page-1)*limit)
		rows, err = dbx.Query(a.DB, q, fb...)
	}
	if err != nil || rows == nil {
		rows = []map[string]any{}
	}
	year := time.Now().Year()
	for i := range rows {
		years := 1
		p := fmt.Sprint(rows[i]["practice"])
		if len(p) >= 4 {
			if y, e := strconv.Atoi(p[:4]); e == nil && year-y > 0 {
				years = year - y
			}
		}
		rows[i]["practice"] = years
		if util.AsInt(rows[i]["work_status"], 1) == 0 {
			rows[i]["appointment_time"] = "休息中"
		} else {
			rows[i]["appointment_time"] = "可约"
		}
		if rows[i]["comment"] == nil {
			rows[i]["comment"] = 0
		}
		if rows[i]["label"] == nil {
			rows[i]["label"] = ""
		}
		if rows[i]["distance"] == nil {
			rows[i]["distance"] = "0.00"
		}
	}
	var count int
	_ = a.DB.QueryRow("SELECT COUNT(*) FROM "+a.table("technician")+" a"+where, args...).Scan(&count)
	pages := 0
	if count > 0 {
		pages = (count + limit - 1) / limit
	}
	httpx.Msg(c.W, 1, "", map[string]any{"list": rows, "total": pages, "page": page}, count)
}

func (a *API) TechnicianDetails(c *httpx.Ctx) {
	row, err := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("technician")+" WHERE id=? LIMIT 1", c.Raw("id"))
	if err != nil || row == nil {
		httpx.Msg(c.W, 0, "技师不存在", "", 0)
		return
	}
	httpx.Msg(c.W, 1, "", row, 0)
}

func (a *API) AddressIndex(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("address")+" WHERE user_id=? ORDER BY id DESC", a.uid(c))
	list := listOrEmpty(rows)
	httpx.Msg(c.W, 1, "", list, len(list))
}

func (a *API) AddressDetails(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("address")+" WHERE id=? AND user_id=? LIMIT 1", c.Raw("id"), a.uid(c))
	httpx.Msg(c.W, 1, "", row, 0)
}

func (a *API) AddressEdit(c *httpx.Ctx) {
	id := c.Raw("id")
	cols := []string{"realname", "mobile", "province", "city", "district", "address", "lat", "lng", "is_default"}
	vals := []any{c.Raw("realname"), c.Raw("mobile"), c.Raw("province"), c.Raw("city"), c.Raw("district"), c.Raw("address"), c.Raw("lat"), c.Raw("lng"), c.Raw("is_default")}
	if id == "" || id == "0" {
		cols = append(cols, "user_id", "create_time")
		vals = append(vals, a.uid(c), time.Now().Unix())
		newID, err := dbx.Insert(a.DB, a.table("address"), cols, vals)
		if err != nil {
			httpx.Msg(c.W, 0, err.Error(), "", 0)
			return
		}
		httpx.Msg(c.W, 1, "保存成功", newID, 0)
		return
	}
	if err := dbx.Update(a.DB, a.table("address"), "id", id, cols, vals); err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "保存成功", "", 0)
}

func (a *API) AddressDel(c *httpx.Ctx) {
	_, err := dbx.Exec(a.DB, "DELETE FROM "+a.table("address")+" WHERE id=? AND user_id=?", c.Raw("id"), a.uid(c))
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "删除成功", "", 0)
}

func (a *API) CouponIndex(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("coupon")+" WHERE user_id=? ORDER BY id DESC", a.uid(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) CouponCenter(c *httpx.Ctx) {
	rows, err := dbx.Query(a.DB, "SELECT id,name,money,discount,`condition`,type,start_time,end_time FROM "+a.table("coupon")+" WHERE status=1 AND get_type=0 AND user_id=0 ORDER BY id DESC")
	if err != nil || rows == nil {
		rows = []map[string]any{}
	}
	for i := range rows {
		n, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("coupon")+" WHERE user_id=? AND primary_id=?", a.uid(c), rows[i]["id"])
		if n > 0 {
			rows[i]["is_receive"] = 1
		} else {
			rows[i]["is_receive"] = 0
		}
		rows[i]["start_time"] = formatMinute(rows[i]["start_time"])
		rows[i]["end_time"] = formatMinute(rows[i]["end_time"])
	}
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) Receive(c *httpx.Ctx) {
	id := strings.TrimSpace(c.Raw("id"))
	row, _ := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("coupon")+" WHERE id=? AND status=1 AND get_type=0 LIMIT 1", id)
	if row == nil {
		httpx.Msg(c.W, 0, "优惠券不存在或已失效", "", 0)
		return
	}
	got, _ := dbx.QueryOne(a.DB, "SELECT id FROM "+a.table("coupon")+" WHERE user_id=? AND primary_id=? LIMIT 1", a.uid(c), id)
	if got != nil {
		httpx.Msg(c.W, 0, "您已领取此优惠券", "", 0)
		return
	}
	_, err := dbx.Insert(a.DB, a.table("coupon"),
		[]string{"primary_id", "name", "money", "discount", "condition", "type", "get_type", "user_id", "service_id", "create_time", "start_time", "end_time", "status", "use"},
		[]any{row["id"], row["name"], row["money"], row["discount"], row["condition"], row["type"], row["get_type"], a.uid(c), row["service_id"], time.Now().Unix(), row["start_time"], row["end_time"], 1, 0})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "领取成功", "", 0)
}

func (a *API) CouponExchange(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("coupon")+" WHERE exchange=? AND (user_id=0 OR user_id IS NULL) LIMIT 1", c.Raw("exchange"))
	if row == nil {
		httpx.Msg(c.W, 0, "兑换码无效", "", 0)
		return
	}
	c.In["id"] = row["id"]
	a.Receive(c)
}

func (a *API) MemberIndex(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("member")+" WHERE status=1 ORDER BY sort DESC,id DESC")
	rights, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("member_rights")+" WHERE status=1")
	httpx.Msg(c.W, 1, "", map[string]any{"member": rows, "rights": rights}, 0)
}

func (a *API) MemberList(c *httpx.Ctx) { a.MemberIndex(c) }

func (a *API) MemberCreate(c *httpx.Ctx) {
	member, _ := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("member")+" WHERE id=? LIMIT 1", c.Raw("id"))
	if member == nil {
		httpx.Msg(c.W, 0, "会员不存在", "", 0)
		return
	}
	sn := util.OrderSN("M")
	id, err := dbx.Insert(a.DB, a.table("member_order"),
		[]string{"order_sn", "user_id", "pay_money", "pay_status", "member_name", "month", "create_time", "payment"},
		[]any{sn, a.uid(c), member["price"], 0, member["name"], member["month"], time.Now().Unix(), c.Raw("payment")})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "", map[string]any{"id": id, "order_sn": sn}, 0)
}

func (a *API) RechargeIndex(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("recharge")+" ORDER BY id DESC")
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) RechargeList(c *httpx.Ctx) { a.RechargeIndex(c) }

func (a *API) RechargeCreate(c *httpx.Ctx) {
	sn := util.OrderSN("R")
	id, err := dbx.Insert(a.DB, a.table("recharge_order"),
		[]string{"order_sn", "user_id", "pay_money", "pay_status", "create_time"},
		[]any{sn, a.uid(c), c.Raw("money"), 0, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "", map[string]any{"id": id, "order_sn": sn}, 0)
}

func (a *API) RechargeOrders(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("recharge_order")+" WHERE user_id=? ORDER BY id DESC", a.uid(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) ConsumeList(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("user_log")+" WHERE user_id=? AND income=2 ORDER BY id DESC", a.uid(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) RefundList(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("user_log")+" WHERE user_id=? AND type=2 ORDER BY id DESC", a.uid(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) BrokerageList(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("user_log")+" WHERE user_id=? AND type=3 ORDER BY id DESC", a.uid(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) OpinionIndex(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT id,name FROM "+a.table("types")+" WHERE type='opinion'")
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) OpinionList(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("opinion")+" WHERE user_id=? ORDER BY id DESC", a.uid(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) CreateOpinion(c *httpx.Ctx) {
	_, err := dbx.Insert(a.DB, a.table("opinion"),
		[]string{"user_id", "content", "images", "mobile", "type", "user_type", "create_time"},
		[]any{a.uid(c), c.Raw("content"), c.Raw("images"), c.Raw("mobile"), c.Raw("type"), 1, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "提交成功", "", 0)
}

func (a *API) PayWay(c *httpx.Ctx) {
	httpx.Msg(c.W, 1, "", []map[string]any{{"id": 1, "name": "余额"}, {"id": 2, "name": "微信"}, {"id": 3, "name": "支付宝"}}, 0)
}

func (a *API) IsPay(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(a.DB, "SELECT pay_status FROM "+a.table("order")+" WHERE order_sn=? LIMIT 1", c.Raw("order_sn"))
	httpx.Msg(c.W, 1, "", row, 0)
}

func (a *API) Pay(c *httpx.Ctx) {
	sn := strings.TrimSpace(c.Raw("order_sn"))
	way := util.AsInt(c.Raw("pay_way"), 0)
	form := strings.TrimSpace(c.Raw("form"))
	if sn == "" || way == 0 || form == "" {
		httpx.Msg(c.W, 0, "参数错误", "", 0)
		return
	}
	if way != 1 {
		httpx.Msg(c.W, 0, "当前仅支持余额支付", "", 0)
		return
	}
	if form != "order" {
		httpx.Msg(c.W, 0, "暂不支持该订单类型", "", 0)
		return
	}
	a.payOrderByBalance(c, sn)
}

func (a *API) payOrderByBalance(c *httpx.Ctx, sn string) {
	tx, err := a.DB.Begin()
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	defer tx.Rollback()

	var payMoney, totalMoney, specRate string
	var payStatus int
	err = tx.QueryRow("SELECT pay_money,IFNULL(total_money,0),pay_status,IFNULL((SELECT technician_rate FROM "+a.table("service_spec")+" WHERE id="+a.table("order")+".spec_id),0) FROM "+a.table("order")+" WHERE order_sn=? AND user_id=? FOR UPDATE", sn, a.uid(c)).Scan(&payMoney, &totalMoney, &payStatus, &specRate)
	if err != nil {
		httpx.Msg(c.W, 0, "该订单不存在", "", 0)
		return
	}
	if payStatus == 1 {
		httpx.Msg(c.W, 0, "该订单已支付", "", 0)
		return
	}
	money := util.AsFloat(totalMoney)
	if money <= 0 {
		money = util.AsFloat(payMoney)
	}
	var balance string
	if err = tx.QueryRow("SELECT balance FROM "+a.table("user")+" WHERE id=? FOR UPDATE", a.uid(c)).Scan(&balance); err != nil {
		httpx.Msg(c.W, 0, "用户不存在", "", 0)
		return
	}
	if util.AsFloat(balance)+0.001 < money {
		httpx.Msg(c.W, 0, "余额不足", "", 0)
		return
	}
	techMoney, brokerage, second := a.orderShares(util.AsFloat(totalMoney), util.AsFloat(payMoney), util.AsFloat(specRate))
	status := 1
	if strings.HasPrefix(sn, "J") {
		status = 3
	}
	res, err := tx.Exec("UPDATE "+a.table("order")+" SET payment=1, pay_status=1, order_status=?, technician_money=?, brokerage=?, second_brokerage=? WHERE order_sn=? AND user_id=? AND pay_status<>1", status, techMoney, brokerage, second, sn, a.uid(c))
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	if n, _ := res.RowsAffected(); n == 0 {
		httpx.Msg(c.W, 0, "该订单已支付", "", 0)
		return
	}
	res, err = tx.Exec("UPDATE "+a.table("user")+" SET balance=balance-? WHERE id=? AND balance>=?", money, a.uid(c), money)
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	if n, _ := res.RowsAffected(); n == 0 {
		httpx.Msg(c.W, 0, "余额不足", "", 0)
		return
	}
	if _, err = tx.Exec("INSERT INTO "+a.table("user_log")+"(user_id,type,money,income,create_time,payment,user_type,order_sn) VALUES (?,?,?,?,?,?,?,?)", a.uid(c), 1, util.AsFloat(payMoney), 2, time.Now().Unix(), 1, 1, sn); err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	if err = tx.Commit(); err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "支付成功", "", 0)
}

func (a *API) orderShares(total, pay, specRate float64) (tech, first, second float64) {
	base := total
	if base <= 0 {
		base = pay
	}
	if specRate < 0 {
		specRate = 0
	}
	tech = math.Round(base*specRate) / 100
	if row, _ := dbx.QueryOne(a.DB, "SELECT data FROM "+a.table("base_config")+" WHERE name='brokerage' LIMIT 1"); row != nil {
		var br map[string]any
		if json.Unmarshal([]byte(fmt.Sprint(row["data"])), &br) == nil {
			first = math.Round(pay*util.AsFloat(br["first"])) / 100
			second = math.Round(pay*util.AsFloat(br["second"])) / 100
		}
	}
	return tech, first, second
}

func (a *API) OrderList(c *httpx.Ctx) {
	page := util.AsInt(c.In["page"], 1)
	if page < 1 {
		page = 1
	}
	const limit = 10
	where := " WHERE a.user_id=?"
	args := []any{a.uid(c)}
	if st := strings.TrimSpace(c.Raw("order_status")); st != "" {
		if st == "4" {
			where += " AND a.order_status IN (4,5)"
		} else {
			where += " AND a.order_status=?"
			args = append(args, st)
		}
	}
	q := "SELECT a.id,a.order_sn,a.pay_money,a.order_status,a.appointment_time,a.create_time,IFNULL(b.name,'') name,IFNULL(c.nickname,'') nickname,IFNULL(c.realname,'') realname,IFNULL(d.cover,'') cover FROM " + a.table("order") + " a LEFT JOIN " + a.table("service_spec") + " b ON a.spec_id=b.id LEFT JOIN " + a.table("technician") + " c ON a.technician_id=c.id LEFT JOIN " + a.table("service") + " d ON a.service_id=d.id" + where + " ORDER BY a.id DESC LIMIT ? OFFSET ?"
	qArgs := make([]any, 0, len(args)+2)
	qArgs = append(qArgs, args...)
	qArgs = append(qArgs, limit, (page-1)*limit)
	rows, err := dbx.Query(a.DB, q, qArgs...)
	if err != nil {
		q = "SELECT a.id,a.order_sn,a.pay_money,a.order_status,a.appointment_time,a.create_time,'' name,'' nickname,'' realname,'' cover FROM " + a.table("order") + " a" + where + " ORDER BY a.id DESC LIMIT ? OFFSET ?"
		rows, err = dbx.Query(a.DB, q, qArgs...)
	}
	if err != nil || rows == nil {
		rows = []map[string]any{}
	}
	for i := range rows {
		rows[i]["create_time"] = formatMinute(rows[i]["create_time"])
		rows[i]["appointment_time"] = formatMinute(rows[i]["appointment_time"])
	}
	count, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("order")+" a"+where, args...)
	pages := 0
	if count > 0 {
		pages = (count + limit - 1) / limit
	}
	httpx.Msg(c.W, 1, "", map[string]any{"list": rows, "total": pages, "page": page}, count)
}

func formatMinute(v any) string {
	n := int64(util.AsInt(v, 0))
	if n <= 0 {
		return ""
	}
	return time.Unix(n, 0).Format("2006-01-02 15:04")
}

func (a *API) OrderDetails(c *httpx.Ctx) {
	sn := strings.TrimSpace(c.Raw("order_sn"))
	if sn == "" {
		httpx.Msg(c.W, 0, "订单不存在", "", 0)
		return
	}
	row, err := dbx.QueryOne(a.DB, "SELECT a.id,a.order_sn,a.pay_money,a.order_status,a.appointment_time,a.fare,a.discount,a.refund,a.total_money,a.create_time,a.duration,a.service_id,a.spec_id,a.technician_id,a.realname AS user_realname,a.mobile AS user_mobile,a.province,a.city,a.district,a.address,IFNULL(b.name,'') AS name,IFNULL(d.cover,'') AS cover,IFNULL(c.nickname,'') AS nickname,IFNULL(c.realname,'') AS realname,IFNULL(c.praise,0) AS praise,IFNULL(c.avatar,'') AS avatar,IFNULL(c.mobile,'') AS technician_mobile FROM "+a.table("order")+" a LEFT JOIN "+a.table("service_spec")+" b ON a.spec_id=b.id LEFT JOIN "+a.table("technician")+" c ON a.technician_id=c.id LEFT JOIN "+a.table("service")+" d ON a.service_id=d.id WHERE a.order_sn=? AND a.user_id=? LIMIT 1", sn, a.uid(c))
	if err != nil || row == nil {
		httpx.Msg(c.W, 0, "订单不存在", "", 0)
		return
	}
	row["appointment_time"] = formatMinute(row["appointment_time"])
	row["create_time"] = formatMinute(row["create_time"])
	row["address_id"] = 0
	if addr, _ := dbx.QueryOne(a.DB, "SELECT id FROM "+a.table("address")+" WHERE user_id=? AND address=? ORDER BY id DESC LIMIT 1", a.uid(c), row["address"]); addr != nil {
		row["address_id"] = addr["id"]
	}
	if row["discount"] == nil {
		row["discount"] = 0
	}
	httpx.Msg(c.W, 1, "", row, 0)
}

func (a *API) SpecList(c *httpx.Ctx) {
	rows, err := dbx.Query(a.DB, "SELECT a.id,a.name,a.price,a.duration,a.service_id,IFNULL(b.cover,'') AS cover,IFNULL(b.original_price,0) AS original_price FROM "+a.table("service_spec")+" a LEFT JOIN "+a.table("service")+" b ON a.service_id=b.id WHERE b.`show`=1 ORDER BY a.sort DESC, a.id DESC")
	if err != nil || rows == nil {
		rows = []map[string]any{}
	}
	httpx.Msg(c.W, 1, "返回成功", rows, len(rows))
}

func (a *API) CreateOrder(c *httpx.Ctx) {
	specID := c.Raw("spec_id")
	addrID := c.Raw("address_id")
	if specID == "" || addrID == "" || c.Raw("technician_id") == "" {
		httpx.Msg(c.W, 0, "参数错误", "", 0)
		return
	}
	spec, _ := dbx.QueryOne(a.DB, "SELECT price,duration FROM "+a.table("service_spec")+" WHERE id=? LIMIT 1", specID)
	if spec == nil {
		httpx.Msg(c.W, 0, "服务不存在", "", 0)
		return
	}
	addr, _ := dbx.QueryOne(a.DB, "SELECT realname,mobile,province,city,district,address,lat,lng FROM "+a.table("address")+" WHERE id=? AND user_id=? LIMIT 1", addrID, a.uid(c))
	if addr == nil {
		httpx.Msg(c.W, 0, "请先选择地址", "", 0)
		return
	}
	appt := util.Unix(c.Raw("appointment_time"))
	if appt == 0 {
		httpx.Msg(c.W, 0, "请选择服务时间", "", 0)
		return
	}
	duration := util.AsInt(spec["duration"], 0)
	fare := util.AsFloat(c.Raw("fare"))
	pay := util.AsFloat(spec["price"])
	if c.Raw("pay_money") != "" {
		pay = util.AsFloat(c.Raw("pay_money"))
	}
	sn := util.OrderSN("O")
	_, err := dbx.Insert(a.DB, a.table("order"),
		[]string{"order_sn", "user_id", "technician_id", "service_id", "spec_id", "pay_money", "total_money", "discount", "order_status", "pay_status", "realname", "mobile", "province", "city", "district", "address", "lat", "lng", "remark", "create_time", "appointment_time", "duration", "fare"},
		[]any{sn, a.uid(c), c.Raw("technician_id"), c.Raw("service_id"), specID, pay, pay + fare, util.AsFloat(c.Raw("discount")), 0, 0, addr["realname"], addr["mobile"], addr["province"], addr["city"], addr["district"], addr["address"], addr["lat"], addr["lng"], c.Raw("remark"), time.Now().Unix(), appt, duration, fare})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	count := 0
	switch {
	case duration <= 0:
		count = 0
	case duration <= 30:
		count = 1
	case duration%30 == 0:
		count = duration / 30
	default:
		count = duration/30 + 1
	}
	for i := 0; i < count; i++ {
		t := time.Unix(appt+int64(i*30*60), 0)
		hour := t.Hour() * 2
		if t.Minute() > 29 {
			hour++
		}
		_, _ = dbx.Insert(a.DB, a.table("service_date"),
			[]string{"appointment_time", "technician_id", "order_sn", "type", "date", "week", "hour", "create_time"},
			[]any{t.Unix(), c.Raw("technician_id"), sn, 1, t.Format("20060102"), int(t.Weekday()), hour, time.Now().Unix()})
	}
	httpx.Msg(c.W, 1, "创建订单成功", sn, 0)
}

func (a *API) CancelOrder(c *httpx.Ctx) {
	_, err := dbx.Exec(a.DB, "UPDATE "+a.table("order")+" SET order_status=6, cancel_reason=?, cancel_type=0 WHERE id=? AND user_id=?", c.Raw("cancel_reason"), c.Raw("id"), a.uid(c))
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "取消成功", "", 0)
}

func (a *API) ConfirmCancel(c *httpx.Ctx) { a.CancelOrder(c) }

func (a *API) CompleteOrder(c *httpx.Ctx) {
	_, err := dbx.Exec(a.DB, "UPDATE "+a.table("order")+" SET order_status=3, complete_time=? WHERE id=? AND user_id=?", time.Now().Unix(), c.Raw("id"), a.uid(c))
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "完成成功", "", 0)
}

func (a *API) Complaint(c *httpx.Ctx) {
	_, err := dbx.Insert(a.DB, a.table("complaint"),
		[]string{"order_id", "user_id", "content", "images", "user_type", "create_time"},
		[]any{c.Raw("order_id"), a.uid(c), c.Raw("content"), c.Raw("images"), 1, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "提交成功", "", 0)
}

func (a *API) UserInstructions(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(a.DB, "SELECT data FROM "+a.table("base_config")+" WHERE name='user_instructions' LIMIT 1")
	httpx.Msg(c.W, 1, "", row, 0)
}

func (a *API) CommentType(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT id,name FROM "+a.table("types")+" WHERE type='comment'")
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) OrderComment(c *httpx.Ctx) {
	_, err := dbx.Insert(a.DB, a.table("comment"),
		[]string{"order_id", "user_id", "technician_id", "content", "create_time"},
		[]any{c.Raw("order_id"), a.uid(c), c.Raw("technician_id"), c.Raw("content"), time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("order")+" SET order_status=4 WHERE id=? AND user_id=?", c.Raw("order_id"), a.uid(c))
	httpx.Msg(c.W, 1, "评价成功", "", 0)
}

func (a *API) CollectIndex(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("collect")+" WHERE user_id=? ORDER BY id DESC", a.uid(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) Collect(c *httpx.Ctx) {
	exist, _ := dbx.QueryOne(a.DB, "SELECT id FROM "+a.table("collect")+" WHERE user_id=? AND technician_id=? LIMIT 1", a.uid(c), c.Raw("technician_id"))
	if exist != nil {
		_, _ = dbx.Exec(a.DB, "DELETE FROM "+a.table("collect")+" WHERE id=?", exist["id"])
		httpx.Msg(c.W, 1, "取消收藏", "", 0)
		return
	}
	_, err := dbx.Insert(a.DB, a.table("collect"), []string{"user_id", "technician_id", "create_time"}, []any{a.uid(c), c.Raw("technician_id"), time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "收藏成功", "", 0)
}

func (a *API) ShareIndex(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(a.DB, "SELECT id,nickname,promo_code FROM "+a.table("user")+" WHERE id=?", a.uid(c))
	httpx.Msg(c.W, 1, "", row, 0)
}

func (a *API) ShareTeam(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT id,nickname,mobile,create_time FROM "+a.table("user")+" WHERE pid=? ORDER BY id DESC", a.uid(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) Wallet(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(a.DB, "SELECT balance,brokerage FROM "+a.table("user")+" WHERE id=?", a.uid(c))
	httpx.Msg(c.W, 1, "", row, 0)
}

func (a *API) AliBind(c *httpx.Ctx) {
	_, err := dbx.Exec(a.DB, "INSERT INTO "+a.table("user_ali")+"(user_id,realname,mobile,create_time) VALUES (?,?,?,?)", a.uid(c), c.Raw("realname"), c.Raw("mobile"), time.Now().Unix())
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "绑定成功", "", 0)
}

func (a *API) Withdraw(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(a.DB, "SELECT balance FROM "+a.table("user")+" WHERE id=?", a.uid(c))
	ali, _ := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("user_ali")+" WHERE user_id=? ORDER BY id DESC LIMIT 1", a.uid(c))
	httpx.Msg(c.W, 1, "", map[string]any{"balance": row, "ali": ali}, 0)
}

func (a *API) WithdrawApply(c *httpx.Ctx) {
	money := util.AsFloat(c.In["money"])
	user, _ := dbx.QueryOne(a.DB, "SELECT balance FROM "+a.table("user")+" WHERE id=?", a.uid(c))
	if user == nil || util.AsFloat(user["balance"]) < money {
		httpx.Msg(c.W, 0, "余额不足", "", 0)
		return
	}
	sn := util.OrderSN("W")
	_, err := dbx.Insert(a.DB, a.table("withdraw_apply"),
		[]string{"sn", "user_id", "real_name", "account", "type", "user_type", "money", "left_money", "poundage", "status", "create_time"},
		[]any{sn, a.uid(c), c.Raw("real_name"), c.Raw("account"), c.Raw("type"), 1, money, money, 0, 1, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("user")+" SET balance=balance-? WHERE id=?", money, a.uid(c))
	httpx.Msg(c.W, 1, "提交成功", "", 0)
}

func (a *API) WithdrawList(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("withdraw_apply")+" WHERE user_id=? AND user_type=1 ORDER BY id DESC", a.uid(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) Upload(c *httpx.Ctx) {
	httpx.Msg(c.W, 1, "", "", 0)
}

func (a *API) GetDistance(c *httpx.Ctx) {
	httpx.Msg(c.W, 1, "", map[string]any{"distance": 0}, 0)
}

func (a *API) Settlement(c *httpx.Ctx) {
	if c.Raw("service_id") == "" || c.Raw("spec_id") == "" {
		httpx.Msg(c.W, 0, "参数错误", "", 0)
		return
	}
	service, err := dbx.QueryOne(a.DB, "SELECT a.name AS spec_name,a.price,a.duration,b.name,b.cover,b.sales,b.discount FROM "+a.table("service_spec")+" a LEFT JOIN "+a.table("service")+" b ON b.id=a.service_id WHERE a.id=? LIMIT 1", c.Raw("spec_id"))
	if err != nil || service == nil {
		httpx.Msg(c.W, 0, "服务不存在", "", 0)
		return
	}
	discount := util.AsInt(service["discount"], 0)
	if discount%10 == 0 {
		discount = discount / 10
	}
	service["discount"] = discount
	valuation, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("valuation")+" ORDER BY id DESC")
	technician := map[string]any{}
	if id := c.Raw("technician_id"); id != "" && id != "0" {
		if row, _ := dbx.QueryOne(a.DB, "SELECT id,nickname,realname,lat,lng FROM "+a.table("technician")+" WHERE id=? LIMIT 1", id); row != nil {
			technician = row
		}
	}
	user := map[string]any{"id": a.uid(c), "is_vip": 0, "balance": "0", "vip_discount": 0}
	if u, _ := dbx.QueryOne(a.DB, "SELECT id,is_vip,balance FROM "+a.table("user")+" WHERE id=?", a.uid(c)); u != nil {
		user = u
	}
	if rights, _ := dbx.QueryOne(a.DB, "SELECT value FROM "+a.table("member_rights")+" WHERE id=2"); rights != nil {
		user["vip_discount"] = rights["value"]
	} else {
		user["vip_discount"] = 0
	}
	httpx.Msg(c.W, 1, "", map[string]any{
		"service":    service,
		"technician": technician,
		"valuation":  listOrEmpty(valuation),
		"user":       user,
	}, 0)
}

func (a *API) ServiceDate(c *httpx.Ctx) {
	rows, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("service_date")+" WHERE technician_id=? ORDER BY id DESC LIMIT 30", c.Raw("technician_id"))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func (a *API) DateList(c *httpx.Ctx) {
	id := c.Raw("id")
	if id == "" {
		id = c.Raw("technician_id")
	}
	if id == "" || id == "0" {
		httpx.Msg(c.W, 1, "", map[string]any{"list": []any{}, "server_hour_range": []any{}}, 0)
		return
	}
	tech, _ := dbx.QueryOne(a.DB, "SELECT id,start_time,end_time FROM "+a.table("technician")+" WHERE id=? LIMIT 1", id)
	if tech == nil {
		httpx.Msg(c.W, 0, "该技师不存在", "", 0)
		return
	}
	day, err := time.ParseInLocation("2006-01-02", time.Now().Format("2006")+"-"+c.Raw("date"), time.Local)
	if err != nil {
		httpx.Msg(c.W, 0, "日期错误", "", 0)
		return
	}
	start := day.Unix()
	end := day.Add(24 * time.Hour).Unix()
	rows, _ := dbx.Query(a.DB, "SELECT hour FROM "+a.table("service_date")+" WHERE type IN (1,2) AND technician_id=? AND appointment_time>=? AND appointment_time<=? ORDER BY hour ASC", id, start, end)
	list := []map[string]any{}
	for _, v := range rows {
		list = append(list, map[string]any{"hour": indexToHour(util.AsInt(v["hour"], 0))})
	}
	startIdx := util.AsInt(tech["start_time"], 0)
	endIdx := util.AsInt(tech["end_time"], 0)
	if strings.TrimSpace(util.AsString(tech["start_time"])) == "" || strings.TrimSpace(util.AsString(tech["end_time"])) == "" {
		startIdx, endIdx = 18, 46
	}
	httpx.Msg(c.W, 1, "", map[string]any{
		"list":              list,
		"server_hour_range": hourRange(startIdx, endIdx),
	}, 0)
}

func indexToHour(index int) string {
	min := ":00"
	if index%2 == 1 {
		index--
		min = ":30"
	}
	return strconv.Itoa(index/2) + min
}

func hourRange(start, end int) []float64 {
	if end < start {
		return []float64{}
	}
	out := make([]float64, 0, end-start+1)
	for s := start; s <= end; s++ {
		out = append(out, float64(s)/2)
	}
	return out
}

func (a *API) OrderCoupon(c *httpx.Ctx) {
	money := util.AsFloat(c.Raw("money"))
	now := time.Now().Unix()
	rows, _ := dbx.Query(a.DB, "SELECT id,name,money,discount,`condition`,type,service_id,start_time,end_time FROM "+a.table("coupon")+" WHERE user_id=? AND service_id=? AND `use`=0 AND end_time>?", a.uid(c), c.Raw("service_id"), now)
	available := []map[string]any{}
	noAvailable := []map[string]any{}
	for _, v := range rows {
		start := int64(util.AsInt(v["start_time"], 0))
		end := int64(util.AsInt(v["end_time"], 0))
		if start > now || end < now {
			v["reason"] = "不在使用时间"
			noAvailable = append(noAvailable, v)
			continue
		}
		if util.AsFloat(v["condition"]) > money {
			v["reason"] = "不满足使用条件"
			noAvailable = append(noAvailable, v)
			continue
		}
		v["reason"] = ""
		available = append(available, v)
	}
	httpx.Msg(c.W, 1, "", map[string]any{"available": available, "noAvailable": noAvailable}, 0)
}
