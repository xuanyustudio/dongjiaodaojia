package tech

import (
	"encoding/json"
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

type Tech struct{ *httpx.Deps }

func New(d *httpx.Deps) *Tech { return &Tech{Deps: d} }

type RouteRegistrar interface {
	Handle(appName, controller, action string, h httpx.Handler)
}

func (t *Tech) Register(r RouteRegistrar) {
	r.Handle("technician", "news", "index", t.News)
	r.Handle("technician", "login", "login", t.Login)
	r.Handle("technician", "login", "login1", t.Login)
	r.Handle("technician", "login", "getOpenid", t.GetOpenid)
	r.Handle("technician", "login", "bindMobile", t.BindMobile)
	r.Handle("technician", "login", "cs", t.Cs)
	r.Handle("technician", "index", "cs", t.Cs)
	r.Handle("technician", "index", "tcp", t.Tcp)
	r.Handle("technician", "index", "uploadImage", t.Upload)
	r.Handle("technician", "order", "comment", t.Comment)
	r.Handle("technician", "login", "follow", t.auth(t.Follow))
	r.Handle("technician", "index", "uploadAudio", t.auth(t.UploadAudio))
	r.Handle("technician", "index", "index", t.auth(t.Home))
	r.Handle("technician", "index", "status", t.auth(t.Status))
	r.Handle("technician", "index", "signIn", t.auth(t.SignIn))
	r.Handle("technician", "index", "callPolice", t.auth(t.CallPolice))
	r.Handle("technician", "index", "busyDate", t.auth(t.BusyDate))
	r.Handle("technician", "index", "busyHour", t.auth(t.BusyHour))
	r.Handle("technician", "index", "setBusyHour", t.auth(t.SetBusyHour))
	r.Handle("technician", "index", "incomeStatistics", t.auth(t.IncomeStatistics))
	r.Handle("technician", "index", "incomeList", t.auth(t.IncomeList))
	r.Handle("technician", "index", "orderStatistics", t.auth(t.OrderStatistics))
	r.Handle("technician", "index", "aliBind", t.auth(t.AliBind))
	r.Handle("technician", "index", "wallet", t.auth(t.Wallet))
	r.Handle("technician", "index", "withdraw", t.auth(t.Withdraw))
	r.Handle("technician", "index", "withdrawApply", t.auth(t.WithdrawApply))
	r.Handle("technician", "index", "withdrawList", t.auth(t.WithdrawList))
	r.Handle("technician", "index", "getTmplIds", t.auth(t.TmplIDs))
	r.Handle("technician", "order", "orderList", t.auth(t.OrderList))
	r.Handle("technician", "order", "orderReceiving", t.auth(t.OrderReceiving))
	r.Handle("technician", "order", "orderService", t.auth(t.OrderService))
	r.Handle("technician", "order", "orderDetails", t.auth(t.OrderDetails))
	r.Handle("technician", "order", "cancelReason", t.auth(t.CancelReason))
	r.Handle("technician", "order", "cancelOrder", t.auth(t.CancelOrder))
	r.Handle("technician", "order", "completeOrder", t.auth(t.CompleteOrder))
	r.Handle("technician", "order", "complaint", t.auth(t.Complaint))
	r.Handle("technician", "my.Index", "index", t.auth(t.MyIndex))
	r.Handle("technician", "my.Index", "get_technician_tcp", t.auth(t.GetTCP))
	r.Handle("technician", "my.Index", "sign_tcp", t.auth(t.SignTCP))
	r.Handle("technician", "my.Index", "get_service", t.auth(t.GetService))
	r.Handle("technician", "my.Index", "set_service", t.auth(t.SetService))
	r.Handle("technician", "my.Index", "set_service_data", t.auth(t.SetServiceData))
	r.Handle("technician", "my.Index", "setAlbum", t.auth(t.SetAlbum))
	r.Handle("technician", "my.Index", "setData", t.auth(t.SetData))
	r.Handle("technician", "my.Index", "setHealthy", t.auth(t.SetHealthy))
	r.Handle("technician", "my.Index", "setIdentity", t.auth(t.SetIdentity))
	r.Handle("technician", "my.Index", "setQualifications", t.auth(t.SetQualifications))
	r.Handle("technician", "my.Index", "creditLog", t.auth(t.CreditLog))
	r.Handle("technician", "my.Index", "bindMobile", t.auth(t.BindMobile))
	r.Handle("technician", "my.Opinion", "index", t.auth(t.OpinionIndex))
	r.Handle("technician", "my.Opinion", "list", t.auth(t.OpinionList))
	r.Handle("technician", "my.Opinion", "add", t.auth(t.OpinionAdd))
	r.Handle("technician", "my.Comment", "get_type", t.auth(t.CommentType))
	r.Handle("technician", "my.Comment", "list", t.auth(t.CommentList))
}

func (t *Tech) auth(next httpx.Handler) httpx.Handler {
	return func(c *httpx.Ctx) {
		token := c.HeaderToken("token")
		if token == "" || token == "null" {
			httpx.Msg(c.W, "0", "请先登录", "", 0)
			return
		}
		id, ok := t.Tokens.Get("technician_token", token)
		if !ok {
			httpx.Msg(c.W, "0", "登录已过期，请重新登录", "", 0)
			return
		}
		c.In["__tid"] = id
		next(c)
	}
}

func (t *Tech) id(c *httpx.Ctx) int64 {
	switch v := c.In["__tid"].(type) {
	case int64:
		return v
	default:
		return int64(util.AsInt(v, 0))
	}
}

func (t *Tech) table(name string) string { return t.Cfg.DBPrefix + name }

func (t *Tech) Login(c *httpx.Ctx) {
	mobile := c.Raw("mobile")
	if mobile == "" {
		mobile = c.Raw("username")
	}
	row, err := dbx.QueryOne(t.DB, "SELECT * FROM "+t.table("technician")+" WHERE mobile=? OR username=? LIMIT 1", mobile, mobile)
	if err != nil || row == nil {
		httpx.Msg(c.W, 0, "账号不存在", "", 0)
		return
	}
	if c.Raw("password") != "" && util.AsString(row["password"]) != "" && !auth.CheckUserPassword(util.AsString(row["password"]), c.Raw("password")) {
		httpx.Msg(c.W, 0, "请检查账号密码", "", 0)
		return
	}
	token := auth.RandomHex(16)
	id := int64(util.AsInt(row["id"], 0))
	t.Tokens.Set("technician_token", token, id, 24*time.Hour)
	_, _ = dbx.Exec(t.DB, "UPDATE "+t.table("technician")+" SET last_login=?, last_ip=? WHERE id=?", time.Now().Unix(), c.IP(), id)
	httpx.Msg(c.W, 1, "登录成功", token, 0)
}

func (t *Tech) GetOpenid(c *httpx.Ctx) { httpx.Msg(c.W, 1, "", map[string]any{"openid": ""}, 0) }
func (t *Tech) BindMobile(c *httpx.Ctx) {
	_, _ = dbx.Exec(t.DB, "UPDATE "+t.table("technician")+" SET mobile=? WHERE id=?", c.Raw("mobile"), t.id(c))
	httpx.Msg(c.W, 1, "绑定成功", "", 0)
}
func (t *Tech) Cs(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(t.DB, "SELECT data FROM "+t.table("base_config")+" WHERE name='customer_service' LIMIT 1")
	obj := map[string]any{}
	if row != nil {
		var parsed map[string]any
		if json.Unmarshal([]byte(util.AsString(row["data"])), &parsed) == nil && parsed != nil {
			obj = parsed
		}
	}
	httpx.Msg(c.W, 1, "", obj, 0)
}
func (t *Tech) Tcp(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(t.DB, "SELECT data FROM "+t.table("base_config")+" WHERE name='technician_tcp' LIMIT 1")
	httpx.Msg(c.W, 1, "", row, 0)
}
func (t *Tech) News(c *httpx.Ctx) {
	rows, _ := dbx.Query(t.DB, "SELECT id,title,cover,create_time FROM "+t.table("news")+" ORDER BY id DESC")
	httpx.Msg(c.W, 1, "", rows, len(rows))
}
func (t *Tech) Upload(c *httpx.Ctx) {
	t.saveUpload(c, []string{"jpg", "jpeg", "png", "gif", "bmp", "webp"}, "请选择图片", "图片格式不正确", "图片大小不符合要求")
}
func (t *Tech) UploadAudio(c *httpx.Ctx) {
	t.saveUpload(c, []string{"mp3", "wav", "aac", "m4a", "webm", "amr", "ogg"}, "请选择录音", "录音格式不正确", "录音大小不符合要求")
}
func (t *Tech) saveUpload(c *httpx.Ctx, allow []string, emptyMsg, extMsg, sizeMsg string) {
	file, hdr, err := c.R.FormFile("file")
	if err != nil {
		httpx.Msg(c.W, 0, emptyMsg, "", 0)
		return
	}
	defer file.Close()
	ext := strings.ToLower(strings.TrimPrefix(filepath.Ext(hdr.Filename), "."))
	ok := false
	for _, item := range allow {
		if ext == item {
			ok = true
			break
		}
	}
	if !ok {
		httpx.Msg(c.W, 0, extMsg, "", 0)
		return
	}
	data, err := io.ReadAll(io.LimitReader(file, 8<<20+1))
	if err != nil || len(data) == 0 || len(data) > 8<<20 {
		httpx.Msg(c.W, 0, sizeMsg, "", 0)
		return
	}
	dir := filepath.Join(t.Cfg.UploadRoot, "tech", time.Now().Format("200601"))
	if err := os.MkdirAll(dir, 0o755); err != nil {
		httpx.Msg(c.W, 0, "上传失败", "", 0)
		return
	}
	name := strconv.FormatInt(time.Now().UnixNano(), 10) + "." + ext
	if err := os.WriteFile(filepath.Join(dir, name), data, 0o644); err != nil {
		httpx.Msg(c.W, 0, "上传失败", "", 0)
		return
	}
	rel := "/uploads/tech/" + time.Now().Format("200601") + "/" + name
	domain := strings.TrimRight(t.Cfg.PublicDomain, "/")
	if domain == "" {
		host := c.R.Host
		if host == "" {
			host = "127.0.0.1:8088"
		}
		domain = "http://" + host
	}
	httpx.Msg(c.W, 1, "上传成功", domain+rel, 0)
}
func (t *Tech) Comment(c *httpx.Ctx) {
	rows, _ := dbx.Query(t.DB, "SELECT * FROM "+t.table("comment")+" ORDER BY id DESC LIMIT 20")
	httpx.Msg(c.W, 1, "", rows, len(rows))
}
func (t *Tech) Follow(c *httpx.Ctx) { httpx.Msg(c.W, 1, "", "", 0) }
func (t *Tech) Home(c *httpx.Ctx) {
	id := t.id(c)
	tech, err := dbx.QueryOne(t.DB, "SELECT id,nickname,username,mobile,realname,avatar,status,work_status,province,city,district,address FROM "+t.table("technician")+" WHERE id=?", id)
	if err != nil || tech == nil {
		httpx.Msg(c.W, 0, "技师不存在", "", 0)
		return
	}
	tech["new_order"] = t.orderCount(id, "order_status=1")
	tech["ongoing_order"] = t.orderCount(id, "order_status=3")
	tech["complete_order"] = t.orderCount(id, "order_status IN (4,5)")
	banners, _ := dbx.Query(t.DB, "SELECT id,image FROM "+t.table("banner")+" WHERE type=1 ORDER BY sort DESC")
	if banners == nil {
		banners = []map[string]any{}
	}
	tips := ""
	if row, _ := dbx.QueryOne(t.DB, "SELECT data FROM "+t.table("base_config")+" WHERE name='tips' LIMIT 1"); row != nil {
		tips = util.AsString(row["data"])
	}
	httpx.Msg(c.W, 1, "", map[string]any{
		"banner":     banners,
		"technician": tech,
		"tips":       tips,
	}, 0)
}

func (t *Tech) orderCount(id int64, where string) int {
	n, err := dbx.Count(t.DB, "SELECT COUNT(*) FROM "+t.table("order")+" WHERE technician_id=? AND "+where, id)
	if err != nil {
		return 0
	}
	return n
}
func (t *Tech) Status(c *httpx.Ctx) {
	_, err := dbx.Exec(t.DB, "UPDATE "+t.table("technician")+" SET work_status=? WHERE id=?", c.Raw("work_status"), t.id(c))
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "操作成功", "", 0)
}
func (t *Tech) SignIn(c *httpx.Ctx) {
	lat := clipRunes(c.Raw("lat"), 15)
	lng := clipRunes(c.Raw("lng"), 15)
	if lat == "" || lng == "" {
		httpx.Msg(c.W, 0, "请先设置位置", "", 0)
		return
	}
	radius := util.AsInt(c.Raw("radius"), 1)
	if radius < 1 {
		radius = 1
	}
	_, err := dbx.Exec(t.DB, "UPDATE "+t.table("technician")+" SET province=?, city=?, district=?, address=?, lat=?, lng=?, radius=? WHERE id=?",
		clipRunes(c.Raw("province"), 30), clipRunes(c.Raw("city"), 30), clipRunes(c.Raw("district"), 30), clipRunes(c.Raw("address"), 255), lat, lng, radius, t.id(c))
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "签到成功", "", 0)
}
func (t *Tech) CallPolice(c *httpx.Ctx) {
	_, err := dbx.Insert(t.DB, t.table("call_police"), []string{"technician_id", "lat", "lng", "province", "city", "district", "address", "audio", "create_time"},
		[]any{t.id(c), clipRunes(c.Raw("lat"), 15), clipRunes(c.Raw("lng"), 15), clipRunes(c.Raw("province"), 20), clipRunes(c.Raw("city"), 20), clipRunes(c.Raw("district"), 20), clipRunes(c.Raw("address"), 255), c.Raw("audio"), time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "已报警", "", 0)
}
func (t *Tech) BusyDate(c *httpx.Ctx) {
	rows, _ := dbx.Query(t.DB, "SELECT * FROM "+t.table("service_date")+" WHERE technician_id=? ORDER BY id DESC", t.id(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}
func (t *Tech) BusyHour(c *httpx.Ctx) { t.BusyDate(c) }
func (t *Tech) SetBusyHour(c *httpx.Ctx) {
	_, err := dbx.Insert(t.DB, t.table("service_date"), []string{"technician_id", "date", "hour", "create_time"}, []any{t.id(c), c.Raw("date"), c.Raw("hour"), time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "设置成功", "", 0)
}
func (t *Tech) IncomeStatistics(c *httpx.Ctx) {
	sum, _ := dbx.Scalar(t.DB, "SELECT IFNULL(SUM(technician_money),0) FROM "+t.table("order")+" WHERE technician_id=? AND order_status>=3", t.id(c))
	httpx.Msg(c.W, 1, "", map[string]any{"income": sum}, 0)
}
func (t *Tech) IncomeList(c *httpx.Ctx) {
	rows, _ := dbx.Query(t.DB, "SELECT * FROM "+t.table("user_log")+" WHERE technician_id=? ORDER BY id DESC", t.id(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}
func (t *Tech) OrderStatistics(c *httpx.Ctx) {
	n, _ := dbx.Count(t.DB, "SELECT COUNT(*) FROM "+t.table("order")+" WHERE technician_id=?", t.id(c))
	httpx.Msg(c.W, 1, "", map[string]any{"count": n}, 0)
}
func (t *Tech) AliBind(c *httpx.Ctx) {
	_, err := dbx.Insert(t.DB, t.table("technician_ali"), []string{"technician_id", "realname", "mobile", "create_time"}, []any{t.id(c), c.Raw("realname"), c.Raw("mobile"), time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "绑定成功", "", 0)
}
func (t *Tech) Wallet(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(t.DB, "SELECT balance FROM "+t.table("technician")+" WHERE id=?", t.id(c))
	httpx.Msg(c.W, 1, "", row, 0)
}
func (t *Tech) Withdraw(c *httpx.Ctx) { t.Wallet(c) }
func (t *Tech) WithdrawApply(c *httpx.Ctx) {
	money := util.AsFloat(c.In["money"])
	row, _ := dbx.QueryOne(t.DB, "SELECT balance FROM "+t.table("technician")+" WHERE id=?", t.id(c))
	if row == nil || util.AsFloat(row["balance"]) < money {
		httpx.Msg(c.W, 0, "余额不足", "", 0)
		return
	}
	_, err := dbx.Insert(t.DB, t.table("withdraw_apply"),
		[]string{"sn", "user_id", "real_name", "account", "money", "left_money", "user_type", "status", "create_time"},
		[]any{util.OrderSN("T"), t.id(c), c.Raw("real_name"), c.Raw("account"), money, money, 2, 1, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	_, _ = dbx.Exec(t.DB, "UPDATE "+t.table("technician")+" SET balance=balance-? WHERE id=?", money, t.id(c))
	httpx.Msg(c.W, 1, "提交成功", "", 0)
}
func (t *Tech) WithdrawList(c *httpx.Ctx) {
	rows, _ := dbx.Query(t.DB, "SELECT * FROM "+t.table("withdraw_apply")+" WHERE user_id=? AND user_type=2 ORDER BY id DESC", t.id(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}
func (t *Tech) TmplIDs(c *httpx.Ctx) { httpx.Msg(c.W, 1, "", []any{}, 0) }
func (t *Tech) OrderList(c *httpx.Ctx) {
	where := "a.technician_id=?"
	args := []any{t.id(c)}
	if st := c.Raw("order_status"); st != "" {
		if st == "4" {
			where += " AND a.order_status IN (4,5)"
		} else {
			where += " AND a.order_status=?"
			args = append(args, st)
		}
	}
	if sn := c.Raw("order_sn"); sn != "" {
		where += " AND a.order_sn=?"
		args = append(args, sn)
	}
	page := util.AsInt(c.Raw("page"), 1)
	if page < 1 {
		page = 1
	}
	limit := 10
	count, _ := dbx.Count(t.DB, "SELECT COUNT(*) FROM "+t.table("order")+" a WHERE "+where, args...)
	q := `SELECT a.id,a.order_sn,a.technician_money,a.order_status,a.appointment_time,a.duration,a.create_time,
		d.name AS service_name,c.username,c.avatar,IFNULL(NULLIF(c.mobile,''),a.mobile) AS mobile,c.praise,
		a.province,a.city,a.district,a.address,a.fare
		FROM ` + t.table("order") + ` a
		LEFT JOIN ` + t.table("service") + ` d ON a.service_id=d.id
		LEFT JOIN ` + t.table("user") + ` c ON a.user_id=c.id
		WHERE ` + where + ` ORDER BY a.id DESC LIMIT ? OFFSET ?`
	rows, err := dbx.Query(t.DB, q, append(args, limit, (page-1)*limit)...)
	if err != nil {
		httpx.Msg(c.W, 0, "订单加载失败", map[string]any{"list": []any{}, "total": 0, "page": page}, 0)
		return
	}
	if rows == nil {
		rows = []map[string]any{}
	}
	for _, row := range rows {
		row["appointment_time"] = formatTS(row["appointment_time"], "2006-01-02 15:04")
		row["create_time"] = formatTS(row["create_time"], "2006-01-02 15:04")
	}
	total := 0
	if count > 0 {
		total = (count + limit - 1) / limit
	}
	httpx.Msg(c.W, 1, "", map[string]any{"list": rows, "total": total, "page": page}, count)
}
func (t *Tech) OrderReceiving(c *httpx.Ctx) {
	sn := c.Raw("order_sn")
	order, _ := dbx.QueryOne(t.DB, "SELECT id,order_status FROM "+t.table("order")+" WHERE order_sn=? AND technician_id=? LIMIT 1", sn, t.id(c))
	if order == nil {
		httpx.Msg(c.W, 0, "订单不存在", "", 0)
		return
	}
	if util.AsInt(order["order_status"], 0) > 1 {
		httpx.Msg(c.W, 0, "该订单已被接取", "", 0)
		return
	}
	_, err := dbx.Exec(t.DB, "UPDATE "+t.table("order")+" SET order_status=2, receiving_time=? WHERE order_sn=? AND technician_id=?", time.Now().Unix(), sn, t.id(c))
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "接取订单成功", "", 0)
}
func (t *Tech) OrderService(c *httpx.Ctx) {
	sn := c.Raw("order_sn")
	order, _ := dbx.QueryOne(t.DB, "SELECT id,order_status FROM "+t.table("order")+" WHERE order_sn=? AND technician_id=? LIMIT 1", sn, t.id(c))
	if order == nil {
		httpx.Msg(c.W, 0, "订单不存在", "", 0)
		return
	}
	if util.AsInt(order["order_status"], 0) > 2 {
		httpx.Msg(c.W, 0, "该订单已开始服务", "", 0)
		return
	}
	_, err := dbx.Exec(t.DB, "UPDATE "+t.table("order")+" SET order_status=3, service_time=? WHERE order_sn=? AND technician_id=?", time.Now().Unix(), sn, t.id(c))
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "开始服务成功", "", 0)
}
func (t *Tech) OrderDetails(c *httpx.Ctx) {
	sn := c.Raw("order_sn")
	row, err := dbx.QueryOne(t.DB, `SELECT a.id,a.order_sn,a.technician_money,a.order_status,a.appointment_time,a.fare,a.discount,a.refund,a.total_money,a.lat,a.lng,a.create_time,a.service_time,a.complete_time,
		IFNULL(b.duration,a.duration) AS duration,d.cover,d.name AS service_name,c.username,c.praise,c.avatar,a.province,a.city,a.district,a.address
		FROM `+t.table("order")+` a
		LEFT JOIN `+t.table("service_spec")+` b ON a.spec_id=b.id
		LEFT JOIN `+t.table("user")+` c ON a.user_id=c.id
		LEFT JOIN `+t.table("service")+` d ON a.service_id=d.id
		WHERE a.order_sn=? AND a.technician_id=? LIMIT 1`, sn, t.id(c))
	if err != nil || row == nil {
		httpx.Msg(c.W, 0, "订单不存在", "", 0)
		return
	}
	tech, _ := dbx.QueryOne(t.DB, "SELECT lat,lng FROM "+t.table("technician")+" WHERE id=?", t.id(c))
	row["distance"] = orderDistance(row["lat"], row["lng"], tech)
	row["appointment_time"] = formatTS(row["appointment_time"], "2006-01-02 15:04")
	row["create_time"] = formatTS(row["create_time"], "2006-01-02 15:04")
	row["complete_time"] = formatTS(row["complete_time"], "2006-01-02 15:04:05")
	httpx.Msg(c.W, 1, "", row, 0)
}
func (t *Tech) CancelReason(c *httpx.Ctx) {
	rows, _ := dbx.Query(t.DB, "SELECT id,name FROM "+t.table("types")+" WHERE type='cancel'")
	if rows == nil {
		rows = []map[string]any{}
	}
	httpx.Msg(c.W, 1, "", rows, len(rows))
}
func (t *Tech) CancelOrder(c *httpx.Ctx) {
	sn := c.Raw("order_sn")
	reason := c.Raw("cancel_reason")
	if reason == "" {
		httpx.Msg(c.W, 0, "原因不能为空", "", 0)
		return
	}
	order, _ := dbx.QueryOne(t.DB, "SELECT id,order_status FROM "+t.table("order")+" WHERE order_sn=? AND technician_id=? LIMIT 1", sn, t.id(c))
	if order == nil {
		httpx.Msg(c.W, 0, "订单不存在", "", 0)
		return
	}
	if util.AsInt(order["order_status"], 0) > 1 {
		httpx.Msg(c.W, 0, "该订单已无法取消", "", 0)
		return
	}
	_, err := dbx.Exec(t.DB, "UPDATE "+t.table("order")+" SET order_status=6, cancel_type=1, cancel_reason=?, pay_status=2, complete_time=? WHERE order_sn=? AND technician_id=?", reason, time.Now().Unix(), sn, t.id(c))
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	_, _ = dbx.Exec(t.DB, "UPDATE "+t.table("technician")+" SET credit=IF(credit>0,credit-1,0) WHERE id=?", t.id(c))
	httpx.Msg(c.W, 1, "取消订单成功", "", 0)
}
func (t *Tech) CompleteOrder(c *httpx.Ctx) {
	sn := c.Raw("order_sn")
	order, _ := dbx.QueryOne(t.DB, "SELECT id,order_status,technician_money FROM "+t.table("order")+" WHERE order_sn=? AND technician_id=? LIMIT 1", sn, t.id(c))
	if order == nil {
		httpx.Msg(c.W, 0, "订单不存在", "", 0)
		return
	}
	if util.AsInt(order["order_status"], 0) != 3 {
		httpx.Msg(c.W, 0, "订单状态不正确", "", 0)
		return
	}
	_, err := dbx.Exec(t.DB, "UPDATE "+t.table("order")+" SET order_status=4, complete_time=? WHERE order_sn=? AND technician_id=?", time.Now().Unix(), sn, t.id(c))
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	_, _ = dbx.Exec(t.DB, "UPDATE "+t.table("technician")+" SET balance=balance+?, credit=credit+1, sales=sales+1 WHERE id=?", util.AsFloat(order["technician_money"]), t.id(c))
	httpx.Msg(c.W, 1, "完成成功", "", 0)
}
func (t *Tech) Complaint(c *httpx.Ctx) {
	_, err := dbx.Insert(t.DB, t.table("complaint"), []string{"order_id", "technician_id", "content", "user_type", "create_time"}, []any{c.Raw("order_id"), t.id(c), c.Raw("content"), 2, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "提交成功", "", 0)
}
func (t *Tech) MyIndex(c *httpx.Ctx) {
	id := t.id(c)
	info, err := dbx.QueryOne(t.DB, "SELECT * FROM "+t.table("technician")+" WHERE id=?", id)
	if err != nil || info == nil {
		httpx.Msg(c.W, 0, "该技师不存在", "", 0)
		return
	}
	delete(info, "password")
	delete(info, "token")
	start, end := util.AsString(info["start_time"]), util.AsString(info["end_time"])
	if hourIndex(start) && hourIndex(end) {
		info["start_time"] = indexToHour(util.AsInt(start, 0))
		info["end_time"] = indexToHour(util.AsInt(end, 0))
	} else if start == "" && end == "" {
		info["start_time"] = "0:00"
		info["end_time"] = "0:00"
	}
	orderSum, _ := dbx.Count(t.DB, "SELECT COUNT(*) FROM "+t.table("order")+" WHERE technician_id=?", id)
	serviceSum, _ := dbx.Count(t.DB, "SELECT COUNT(*) FROM "+t.table("order")+" WHERE technician_id=? AND order_status IN (3,4,5)", id)
	turnover := 0
	if orderSum > 0 {
		turnover = serviceSum * 100 / orderSum
	}
	commentSum, _ := dbx.Count(t.DB, "SELECT COUNT(*) FROM "+t.table("comment")+" WHERE technician_id=?", id)
	one := func(table string) map[string]any {
		row, _ := dbx.QueryOne(t.DB, "SELECT * FROM "+t.table(table)+" WHERE technician_id=? ORDER BY id DESC LIMIT 1", id)
		return row
	}
	httpx.Msg(c.W, 1, "", map[string]any{
		"info": info,
		"service_index": map[string]any{
			"composite":      info["composite"],
			"turnover_ratio": turnover,
			"service_sum":    serviceSum,
			"credit":         info["credit"],
			"comment_sum":    commentSum,
		},
		"technician_album":    one("technician_album"),
		"technician_data":     one("technician_data"),
		"technician_healthy":  one("technician_healthy"),
		"technician_identity": one("technician_identity"),
		"qualifications":      one("technician_qualifications"),
		"technician_tcp":      one("technician_tcp"),
	}, 0)
}
func (t *Tech) GetTCP(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(t.DB, "SELECT * FROM "+t.table("technician_tcp")+" WHERE technician_id=? ORDER BY id DESC LIMIT 1", t.id(c))
	httpx.Msg(c.W, 1, "", row, 0)
}
func (t *Tech) SignTCP(c *httpx.Ctx) {
	_, err := dbx.Insert(t.DB, t.table("technician_tcp"), []string{"technician_id", "sign", "status", "create_time"}, []any{t.id(c), c.Raw("sign"), 0, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "签署成功", "", 0)
}
func (t *Tech) GetService(c *httpx.Ctx) {
	row, _ := dbx.QueryOne(t.DB, "SELECT service FROM "+t.table("technician")+" WHERE id=?", t.id(c))
	httpx.Msg(c.W, 1, "", row, 0)
}
func (t *Tech) SetService(c *httpx.Ctx) {
	_, err := dbx.Exec(t.DB, "UPDATE "+t.table("technician")+" SET service=? WHERE id=?", c.Raw("service"), t.id(c))
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "设置成功", "", 0)
}
func (t *Tech) SetServiceData(c *httpx.Ctx) {
	week := c.Raw("week")
	start := hourToIndex(c.Raw("start_time"))
	end := hourToIndex(c.Raw("end_time"))
	if start >= end {
		httpx.Msg(c.W, 0, "下班时间需大于上班时间", "", 0)
		return
	}
	_, err := dbx.Exec(t.DB, "UPDATE "+t.table("technician")+" SET week=?, start_time=?, end_time=? WHERE id=?", week, start, end, t.id(c))
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "操作成功", "", 0)
}
func jsonList(v any) string {
	switch t := v.(type) {
	case []any:
		b, err := json.Marshal(t)
		if err != nil {
			return "[]"
		}
		return string(b)
	case string:
		s := strings.TrimSpace(t)
		if s == "" {
			return "[]"
		}
		if strings.HasPrefix(s, "[") {
			return s
		}
		b, _ := json.Marshal([]string{s})
		return string(b)
	default:
		s := strings.TrimSpace(util.AsString(v))
		if s == "" {
			return "[]"
		}
		if strings.HasPrefix(s, "[") {
			return s
		}
		b, _ := json.Marshal([]string{s})
		return string(b)
	}
}

func (t *Tech) SetAlbum(c *httpx.Ctx) {
	_, err := dbx.Insert(t.DB, t.table("technician_album"), []string{"technician_id", "avatar", "album", "status", "create_time"}, []any{t.id(c), c.Raw("avatar"), jsonList(c.Any("album")), 0, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "提交成功", "", 0)
}
func (t *Tech) SetData(c *httpx.Ctx) {
	_, err := dbx.Insert(t.DB, t.table("technician_data"), []string{"technician_id", "height", "birthday", "profile", "practice", "status", "create_time"}, []any{t.id(c), c.Raw("height"), c.Raw("birthday"), c.Raw("profile"), c.Raw("practice"), 0, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "提交成功", "", 0)
}
func (t *Tech) SetHealthy(c *httpx.Ctx) {
	_, err := dbx.Insert(t.DB, t.table("technician_healthy"), []string{"technician_id", "certificates", "status", "create_time"}, []any{t.id(c), c.Raw("certificates"), 0, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "提交成功", "", 0)
}
func (t *Tech) SetIdentity(c *httpx.Ctx) {
	front := c.Raw("front")
	back := c.Raw("back")
	realname := c.Raw("realname")
	cardsn := c.Raw("cardsn")
	if front == "" || back == "" {
		httpx.Msg(c.W, 0, "请上传身份证正反面", "", 0)
		return
	}
	if realname == "" || cardsn == "" {
		httpx.Msg(c.W, 0, "请填写身份证姓名和证件号", "", 0)
		return
	}
	row, _ := dbx.QueryOne(t.DB, "SELECT id,status FROM "+t.table("technician_identity")+" WHERE technician_id=? ORDER BY id DESC LIMIT 1", t.id(c))
	if row != nil {
		if util.AsInt(row["status"], 0) == 1 {
			httpx.Msg(c.W, 0, "资料已认证，不能修改", "", 0)
			return
		}
		err := dbx.Update(t.DB, t.table("technician_identity"), "id", row["id"], []string{"front", "back", "realname", "cardsn", "status"}, []any{front, back, realname, cardsn, 0})
		if err != nil {
			httpx.Msg(c.W, 0, err.Error(), "", 0)
			return
		}
		httpx.Msg(c.W, 1, "提交成功", "", 0)
		return
	}
	_, err := dbx.Insert(t.DB, t.table("technician_identity"), []string{"technician_id", "front", "back", "realname", "cardsn", "status", "create_time"}, []any{t.id(c), front, back, realname, cardsn, 0, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "提交成功", "", 0)
}
func (t *Tech) SetQualifications(c *httpx.Ctx) {
	_, err := dbx.Insert(t.DB, t.table("technician_qualifications"), []string{"technician_id", "certificates", "status", "create_time"}, []any{t.id(c), c.Raw("certificates"), 0, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "提交成功", "", 0)
}
func (t *Tech) CreditLog(c *httpx.Ctx) {
	rows, _ := dbx.Query(t.DB, "SELECT * FROM "+t.table("user_log")+" WHERE technician_id=? ORDER BY id DESC", t.id(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}
func (t *Tech) OpinionIndex(c *httpx.Ctx) {
	rows, _ := dbx.Query(t.DB, "SELECT id,name FROM "+t.table("types")+" WHERE type='opinion'")
	httpx.Msg(c.W, 1, "", rows, len(rows))
}
func (t *Tech) OpinionList(c *httpx.Ctx) {
	rows, _ := dbx.Query(t.DB, "SELECT * FROM "+t.table("opinion")+" WHERE technician_id=? ORDER BY id DESC", t.id(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}
func (t *Tech) OpinionAdd(c *httpx.Ctx) {
	_, err := dbx.Insert(t.DB, t.table("opinion"), []string{"technician_id", "content", "type", "user_type", "create_time"}, []any{t.id(c), c.Raw("content"), c.Raw("type"), 2, time.Now().Unix()})
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	httpx.Msg(c.W, 1, "提交成功", "", 0)
}
func (t *Tech) CommentType(c *httpx.Ctx) {
	rows, _ := dbx.Query(t.DB, "SELECT id,name FROM "+t.table("types")+" WHERE type='comment'")
	httpx.Msg(c.W, 1, "", rows, len(rows))
}
func (t *Tech) CommentList(c *httpx.Ctx) {
	rows, _ := dbx.Query(t.DB, "SELECT * FROM "+t.table("comment")+" WHERE technician_id=? ORDER BY id DESC", t.id(c))
	httpx.Msg(c.W, 1, "", rows, len(rows))
}

func formatTS(v any, layout string) string {
	s := strings.TrimSpace(util.AsString(v))
	if s == "" || s == "0" {
		return ""
	}
	if strings.Contains(s, "-") {
		return s
	}
	n := util.AsInt(s, 0)
	if n <= 0 {
		return ""
	}
	return time.Unix(int64(n), 0).In(time.Local).Format(layout)
}

func hourIndex(s string) bool {
	if s == "" {
		return false
	}
	for _, r := range s {
		if r < '0' || r > '9' {
			return false
		}
	}
	return true
}

func clipRunes(s string, n int) string {
	r := []rune(strings.TrimSpace(s))
	if len(r) > n {
		return string(r[:n])
	}
	return string(r)
}

func hourToIndex(s string) int {
	parts := strings.Split(strings.TrimSpace(s), ":")
	if len(parts) == 0 || parts[0] == "" {
		return 0
	}
	hour := util.AsInt(parts[0], 0)
	min := 0
	if len(parts) > 1 {
		min = util.AsInt(parts[1], 0)
	}
	extra := 0
	if min > 29 {
		extra = 1
	}
	return hour*2 + extra
}

func indexToHour(index int) string {
	min := ":00"
	if index%2 == 1 {
		index--
		min = ":30"
	}
	return strconv.Itoa(index/2) + min
}

func orderDistance(lat, lng any, tech map[string]any) string {
	if tech == nil || strings.TrimSpace(util.AsString(lat)) == "" || strings.TrimSpace(util.AsString(lng)) == "" || strings.TrimSpace(util.AsString(tech["lat"])) == "" || strings.TrimSpace(util.AsString(tech["lng"])) == "" {
		return "0.00"
	}
	la := util.AsFloat(lat) * 3.1415 / 180
	ln := util.AsFloat(lng) * 3.1415 / 180
	tla := util.AsFloat(tech["lat"]) * 3.1415 / 180
	tln := util.AsFloat(tech["lng"]) * 3.1415 / 180
	d := math.Acos(math.Sin(la)*math.Sin(tla)+math.Cos(la)*math.Cos(tla)*math.Cos(ln-tln)) * 6380
	if math.IsNaN(d) || math.IsInf(d, 0) {
		return "0.00"
	}
	return strconv.FormatFloat(d, 'f', 2, 64)
}
