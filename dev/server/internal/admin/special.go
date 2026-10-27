package admin

import (
	"encoding/json"
	"strings"
	"time"

	"dongjiao/server/internal/auth"
	"dongjiao/server/internal/dbx"
	"dongjiao/server/internal/httpx"
	"dongjiao/server/internal/util"
)

func (a *Admin) ServiceAdd(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	explain, _ := c.In["explain"].([]any)
	cols, vals := a.pick(c, []string{"name", "cover", "banner", "sales", "introduce", "sort", "praise", "show", "original_price", "price", "discount", "duration", "label"})
	cols, vals = replaceCol(cols, vals, "banner", util.ItemData(c.In["banner"]))
	if arr, ok := c.In["label"].([]any); ok {
		parts := make([]string, len(arr))
		for i, item := range arr {
			parts[i] = util.AsString(item)
		}
		cols, vals = replaceCol(cols, vals, "label", strings.Join(parts, ","))
	}
	id, err := dbx.Insert(a.DB, a.table("service"), cols, vals)
	if err != nil {
		httpx.Fail(c.W, 411, err.Error())
		return
	}
	a.saveExplain(int(id), explain, false)
	if c.Raw("sort") == "" || c.Raw("sort") == "0" {
		_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("service")+" SET sort=? WHERE id=?", id, id)
	}
	httpx.OK(c.W, map[string]any{"data": id})
}

func (a *Admin) ServiceUpdate(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	id := c.Raw("id")
	if id == "" {
		httpx.Fail(c.W, 411, "参数错误")
		return
	}
	explain, _ := c.In["explain"].([]any)
	cols, vals := a.pick(c, []string{"name", "cover", "banner", "sales", "introduce", "sort", "praise", "show", "original_price", "price", "discount", "duration", "label"})
	cols, vals = replaceCol(cols, vals, "banner", util.ItemData(c.In["banner"]))
	cols, vals = replaceCol(cols, vals, "update_time", time.Now().Unix())
	if arr, ok := c.In["label"].([]any); ok {
		parts := make([]string, len(arr))
		for i, item := range arr {
			parts[i] = util.AsString(item)
		}
		cols, vals = replaceCol(cols, vals, "label", strings.Join(parts, ","))
	}
	if err := dbx.Update(a.DB, a.table("service"), "id", id, cols, vals); err != nil {
		httpx.Fail(c.W, 411, err.Error())
		return
	}
	a.saveExplain(util.AsInt(id, 0), explain, true)
	httpx.OK(c.W, nil)
}

func (a *Admin) saveExplain(serviceID int, rows []any, update bool) {
	if serviceID == 0 {
		return
	}
	for _, row := range rows {
		m, ok := row.(map[string]any)
		if !ok {
			continue
		}
		m["service_id"] = serviceID
		m["create_time"] = time.Now().Unix()
		cols := []string{}
		vals := []any{}
		for k, v := range m {
			if !util.SafeIdent(k) || k == "id" {
				continue
			}
			cols = append(cols, k)
			vals = append(vals, util.AsString(v))
		}
		if update && util.AsString(m["id"]) != "" {
			_ = dbx.Update(a.DB, a.table("order_explain"), "id", m["id"], cols, vals)
			continue
		}
		_, _ = dbx.Insert(a.DB, a.table("order_explain"), cols, vals)
	}
}

func (a *Admin) ServiceInfo(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	id := c.Str("id")
	if id == "" {
		httpx.Fail(c.W, 411, "参数错误")
		return
	}
	row, err := dbx.QueryOne(a.DB, "SELECT id,name,cover,banner,sales,introduce,update_time,sort,praise,`show`,original_price,price,discount,duration,label FROM "+a.table("service")+" WHERE id=?", id)
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	if row != nil {
		if s := util.AsString(row["banner"]); s != "" {
			row["banner"] = jsonRaw(s)
		}
		if s := util.AsString(row["label"]); s == "" {
			row["label"] = []string{}
		} else {
			row["label"] = strings.Split(s, ",")
		}
		ex, _ := dbx.Query(a.DB, "SELECT * FROM "+a.table("order_explain")+" WHERE service_id=?", id)
		row["explain"] = ex
	}
	httpx.OK(c.W, map[string]any{"data": row})
}

func jsonRaw(s string) any {
	var v any
	if err := json.Unmarshal([]byte(s), &v); err != nil {
		return s
	}
	return v
}

func (a *Admin) OrderCount(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	start, end := dayBounds()
	n1, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("order")+" WHERE order_status>0")
	n2, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("order")+" WHERE order_status>0 AND create_time>=? AND create_time<?", start, end)
	n3, _ := dbx.Scalar(a.DB, "SELECT IFNULL(SUM(pay_money),0) FROM "+a.table("order")+" WHERE order_status>1")
	n4, _ := dbx.Scalar(a.DB, "SELECT IFNULL(SUM(pay_money),0) FROM "+a.table("order")+" WHERE order_status>0 AND create_time>=? AND create_time<?", start, end)
	httpx.OK(c.W, map[string]any{"data": map[string]any{"num1": n1, "num2": n2, "num3": n3, "num4": n4}})
}

func (a *Admin) OperateRefund(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	sn := c.Raw("order_sn")
	money := util.AsFloat(c.In["refund_money"])
	if sn == "" || money == 0 {
		httpx.Msg(c.W, 0, "参数错误", "", 0)
		return
	}
	order, err := dbx.QueryOne(a.DB, "SELECT * FROM "+a.table("order")+" WHERE order_sn=? LIMIT 1", sn)
	if err != nil || order == nil {
		httpx.Msg(c.W, 0, "订单不存在", "", 0)
		return
	}
	if util.AsFloat(order["total_money"]) < util.AsFloat(order["refund"])+money {
		httpx.Msg(c.W, 0, "退款金额不能超过订单总金额", "", 0)
		return
	}
	pay := util.AsInt(order["payment"], 0)
	if pay != 1 {
		httpx.Msg(c.W, 0, "当前仅完成余额退款，微信/支付宝退款保持原通道参数，请在支付配置完整后使用", "", 0)
		return
	}
	_, err = dbx.Exec(a.DB, "UPDATE "+a.table("user")+" SET balance=balance+? WHERE id=?", money, order["user_id"])
	if err != nil {
		httpx.Msg(c.W, 0, err.Error(), "", 0)
		return
	}
	_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("order")+" SET refund=IFNULL(refund,0)+?, refreason=? WHERE order_sn=?", money, "管理员操作退款", sn)
	_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("technician")+" SET balance=balance-? WHERE id=?", money, order["technician_id"])
	_, _ = dbx.Exec(a.DB, "INSERT INTO "+a.table("user_log")+"(user_id,type,money,income,create_time,payment,user_type,order_sn) VALUES (?,?,?,?,?,?,?,?)",
		order["user_id"], 2, money, 1, time.Now().Unix(), order["payment"], 1, sn)
	httpx.Msg(c.W, 1, "退款成功", "", 0)
}

func (a *Admin) TechCount(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	n1, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("technician")+" WHERE status=1")
	n2, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("technician")+" WHERE status=0")
	n3, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("technician")+" WHERE work_status=1")
	n4, _ := dbx.Count(a.DB, "SELECT COUNT(*) FROM "+a.table("technician")+" WHERE work_status=0")
	httpx.OK(c.W, map[string]any{"data": map[string]any{"num1": n1, "num2": n2, "num3": n3, "num4": n4}})
}

func (a *Admin) UpdateColumn(column string) httpx.Handler {
	return func(c *httpx.Ctx) {
		if _, ok := a.requireAuth(c); !ok {
			return
		}
		id := c.Raw("id")
		if id == "" {
			httpx.Fail(c.W, 411, "参数错误")
			return
		}
		_, err := dbx.Exec(a.DB, "UPDATE "+a.table("technician")+" SET "+column+"=? WHERE id=?", c.Raw(column), id)
		if err != nil {
			httpx.Fail(c.W, 500, err.Error())
			return
		}
		httpx.OK(c.W, map[string]any{"msg": "操作成功"})
	}
}

func (a *Admin) UpdateAuth(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	tid := c.Str("id")
	model := c.Str("model")
	status := c.Raw("status")
	table := map[string]string{
		"techniciandata": "technician_data", "technicianidentity": "technician_identity",
		"technicianqualifications": "technician_qualifications", "technicianhealthy": "technician_healthy",
		"techniciantcp": "technician_tcp", "technicianalbum": "technician_album", "auth": "technician",
	}[model]
	if table == "" || tid == "" {
		httpx.Fail(c.W, 411, "参数错误")
		return
	}
	switch model {
	case "techniciandata":
		row, _ := dbx.QueryOne(a.DB, "SELECT sex FROM "+a.table(table)+" WHERE technician_id=? LIMIT 1", tid)
		if row != nil {
			_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("technician")+" SET sex=? WHERE id=?", row["sex"], tid)
		}
		_, _ = dbx.Exec(a.DB, "UPDATE "+a.table(table)+" SET status=? WHERE technician_id=?", status, tid)
	case "technicianidentity":
		row, _ := dbx.QueryOne(a.DB, "SELECT realname FROM "+a.table(table)+" WHERE technician_id=? LIMIT 1", tid)
		if row != nil {
			_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("technician")+" SET realname=? WHERE id=?", row["realname"], tid)
		}
		_, _ = dbx.Exec(a.DB, "UPDATE "+a.table(table)+" SET status=? WHERE technician_id=?", status, tid)
	case "technicianalbum":
		row, _ := dbx.QueryOne(a.DB, "SELECT avatar,album FROM "+a.table(table)+" WHERE technician_id=? LIMIT 1", tid)
		if row != nil {
			_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("technician")+" SET avatar=?,album=? WHERE id=?", row["avatar"], row["album"], tid)
		}
		_, _ = dbx.Exec(a.DB, "UPDATE "+a.table(table)+" SET status=? WHERE technician_id=?", status, tid)
	case "auth":
		_, _ = dbx.Exec(a.DB, "UPDATE "+a.table("technician")+" SET status=? WHERE id=?", status, tid)
	default:
		_, _ = dbx.Exec(a.DB, "UPDATE "+a.table(table)+" SET status=? WHERE technician_id=?", status, tid)
	}
	httpx.OK(c.W, nil)
}

func (a *Admin) WithdrawCount(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	n1, _ := dbx.Scalar(a.DB, "SELECT IFNULL(SUM(left_money),0) FROM "+a.table("withdraw_apply")+" WHERE status=1")
	n2, _ := dbx.Scalar(a.DB, "SELECT IFNULL(SUM(left_money),0) FROM "+a.table("withdraw_apply"))
	n3, _ := dbx.Scalar(a.DB, "SELECT IFNULL(SUM(left_money),0) FROM "+a.table("withdraw_apply")+" WHERE status=2")
	n4, _ := dbx.Scalar(a.DB, "SELECT IFNULL(SUM(left_money),0) FROM "+a.table("withdraw_apply")+" WHERE status=3")
	httpx.OK(c.W, map[string]any{"data": map[string]any{"num1": n1, "num2": n2, "num3": n3, "num4": n4}})
}

func (a *Admin) AdminResetPwd(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	id := c.Raw("user_id")
	pwd := c.Raw("pwd")
	if id == "" {
		httpx.Fail(c.W, 411, "参数错误")
		return
	}
	if pwd == "" {
		httpx.Fail(c.W, 411, "密码不能为空")
		return
	}
	_, err := dbx.Exec(a.DB, "UPDATE "+a.table("admin_user")+" SET pwd=? WHERE user_id=?", auth.AdminPassword(pwd, a.Cfg.PasswordSecret), id)
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	httpx.OK(c.W, map[string]any{"msg": "操作成功"})
}

func (a *Admin) RoleOptions(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	rows, err := dbx.Query(a.DB, "SELECT role_id,name FROM "+a.table("role"))
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	httpx.OK(c.W, map[string]any{"data": map[string]any{"role_ids": pairOptions(rows, "role_id", "name")}})
}

func (a *Admin) ServiceOptions(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	rows, err := dbx.Query(a.DB, "SELECT id,name FROM "+a.table("service"))
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	httpx.OK(c.W, map[string]any{"data": map[string]any{"service_ids": pairOptions(rows, "id", "name")}})
}

func (a *Admin) CouponServices(c *httpx.Ctx) {
	if _, ok := a.requireAuth(c); !ok {
		return
	}
	rows, err := dbx.Query(a.DB, "SELECT name AS value,id FROM "+a.table("service"))
	if err != nil {
		httpx.Fail(c.W, 500, err.Error())
		return
	}
	httpx.OK(c.W, map[string]any{"data": rows})
}

func (a *Admin) ImportRows(res *Resource) httpx.Handler {
	return func(c *httpx.Ctx) {
		if _, ok := a.requireAuth(c); !ok {
			return
		}
		list, _ := c.In["__list"].([]any)
		for _, item := range list {
			m, ok := item.(map[string]any)
			if !ok {
				continue
			}
			cols := []string{}
			vals := []any{}
			for k, v := range m {
				if util.SafeIdent(k) {
					cols = append(cols, k)
					vals = append(vals, util.AsString(v))
				}
			}
			cols = append(cols, "create_time")
			vals = append(vals, time.Now().Unix())
			_, _ = dbx.Insert(a.DB, a.table(res.Table), cols, vals)
		}
		httpx.OK(c.W, nil)
	}
}

func dayBounds() (int64, int64) {
	return util.DayRange(time.Now().Format("2006-01-02"))
}
