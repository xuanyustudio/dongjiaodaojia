package admin

func eqs(pairs ...string) []Search {
	var out []Search
	for i := 0; i+1 < len(pairs); i += 2 {
		out = append(out, Search{Column: pairs[i], Param: pairs[i+1]})
	}
	return out
}

func Resources() []*Resource {
	list := []*Resource{
		{
			Name: "Adminuser", Table: "admin_user", Alias: "adminuser", PK: "user_id",
			ListFields:   "user_id,name,user,note,status,create_time",
			InfoFields:   "user_id,name,user,role_id,note,status,create_time",
			Write:        []string{"name", "user", "pwd", "role_id", "note", "status", "create_time"},
			UpdateFields: []string{"name", "user", "role_id", "note", "status", "create_time"},
			Ext:          []string{"status"},
			Search:       eqs("user_id", "user_id", "adminuser.name", "name", "adminuser.user", "user", "adminuser.role_id", "role_id", "adminuser.status", "status"),
			Joins:        []Join{{Name: "role", Table: "role", Local: "role_id", Foreign: "role_id", Fields: []string{"name"}}},
			CreateNow:    []string{"create_time"}, UpdateParse: []string{"create_time"}, ProtectID: "1",
		},
		{
			Name: "Banner", Table: "banner", Alias: "banner",
			ListFields: "id,type,image,sort,create_time,update_time",
			Write:      []string{"type", "image", "sort", "create_time", "update_time"},
			Ext:        []string{"sort"},
			Search:     eqs("id", "id", "type", "type"),
			CreateNow:  []string{"create_time"}, UpdateNow: []string{"update_time"}, UpdateParse: []string{"create_time"}, AutoSort: true,
			Dump: &Dump{Header: "编号,类型,图片,排序,创建时间,更新时间", File: "轮播图配置", Times: []string{"create_time", "update_time"},
				Labels: map[string]string{"type": `[{"key":"用户端","val":"0"},{"key":"技术端","val":"1"}]`}},
		},
		{
			Name: "Role", Table: "role", Alias: "role", PK: "role_id",
			ListFields: "role_id,name,status,description",
			InfoFields: "role_id,name,status,description,access",
			Write:      []string{"name", "status", "description", "access"},
			Ext:        []string{"status"},
			Search:     eqs("role_id", "role_id", "name", "name", "status", "status"),
		},
		{
			Name: "Service", Table: "service", Alias: "service",
			ListFields: "id,name,cover,sales,sort,praise,show,original_price,price,discount,duration",
			InfoFields: "id,name,cover,banner,sales,introduce,update_time,sort,praise,show,original_price,price,discount,duration,label",
			Write:      []string{"name", "cover", "banner", "sales", "introduce", "update_time", "sort", "praise", "show", "original_price", "price", "discount", "duration", "label"},
			Ext:        []string{"sort", "show"},
			Search:     []Search{{Column: "id", Param: "id"}, {Column: "name", Param: "name", Op: "like"}, {Column: "show", Param: "show"}},
			AutoSort:   true, JSONFields: []string{"banner"}, SplitFields: []string{"label"}, UpdateNow: []string{"update_time"},
		},
		{
			Name: "Servicespec", Table: "service_spec", Alias: "servicespec",
			ListFields:   "id,name,price,duration,sort,technician_rate",
			InfoFields:   "id,name,price,duration,create_time,update_time,sort,service_id,technician_rate",
			DetailFields: "id,name,price,duration,sort",
			Write:        []string{"name", "price", "duration", "create_time", "update_time", "sort", "service_id", "technician_rate"},
			Ext:          []string{"sort"},
			CreateNow:    []string{"create_time", "update_time"}, UpdateNow: []string{"update_time"}, UpdateParse: []string{"create_time"},
			Search:       eqs("id", "id", "servicespec.price", "price", "servicespec.duration", "duration", "servicespec.service_id", "service_id"),
			Joins:        []Join{{Name: "service", Table: "service", Local: "service_id", Foreign: "id", Fields: []string{"id", "name"}}},
		},
		{
			Name: "User", Table: "user", Alias: "user",
			ListFields:   "id,pid,nickname,username,realname,id_card,real_status,sex,mobile,avatar,status,is_promoter,balance,is_vip,create_time,praise",
			InfoFields:   "id,nickname,username,realname,id_card,real_status,sex,mobile,avatar,status,balance,praise",
			DetailFields: "id,pid,nickname,username,realname,sex,mobile,avatar,status,is_promoter,balance,is_vip,create_time,praise",
			Write:        []string{"nickname", "username", "realname", "id_card", "real_status", "sex", "mobile", "avatar", "status", "balance", "praise"},
			Ext:          []string{},
			Search:       eqs("id", "id", "username", "username", "mobile", "mobile"),
			DumpFields:   "id,nickname,username,realname,sex,mobile,avatar,status,balance,praise",
			Dump: &Dump{Header: "编号,昵称,用户名,真名,性别,手机号码,头像,状态,余额,好评率", File: "用户列表",
				Labels: map[string]string{
					"sex":    `[{"key":"保密","val":"0"},{"key":"男","val":"1"},{"key":"女","val":"2"}]`,
					"status": `[{"val":"0","key":"禁止"},{"key":"正常","val":"1"}]`,
				}},
		},
		{
			Name: "Order", Table: "order", Alias: "order",
			ListFields:   "id,order_sn,pay_money,total_money,discount,technician_money,order_status,user_id,technician_id,service_id,spec_id,coupon_id,realname,mobile,province,city,district,address,create_time,appointment_time,duration,fare,refund,payment,brokerage,complete_time,cancel_type,lat,lng,cancel_reason,receiving_time,service_time",
			InfoFields:   "id,order_sn,pay_money,total_money,discount,technician_money,order_status,pay_status,user_id,technician_id,service_id,spec_id,coupon_id,realname,mobile,province,city,district,address,create_time,appointment_time,duration,fare,refund,payment,brokerage,complete_time,cancel_type,lat,lng,cancel_reason,receiving_time,service_time",
			DetailFields: "id,order_sn,pay_money,total_money,discount,technician_money,order_status,user_id,technician_id,service_id,spec_id,coupon_id,realname,mobile,province,city,district,address,create_time,appointment_time,duration,fare,refund,payment,brokerage,complete_time,cancel_type,lat,lng,cancel_reason,receiving_time,service_time,refreason,remark",
			Write:        []string{"order_sn", "pay_money", "total_money", "discount", "technician_money", "order_status", "pay_status", "user_id", "technician_id", "service_id", "spec_id", "coupon_id", "realname", "mobile", "province", "city", "district", "address", "create_time", "appointment_time", "duration", "fare", "refund", "payment", "brokerage", "complete_time", "cancel_type", "lat", "lng", "cancel_reason", "receiving_time", "service_time"},
			Ext:          []string{},
			Search: []Search{
				{Column: "id", Param: "id"},
				{Column: "order.order_sn", Param: "order_sn"},
				{Column: "order.order_status", Param: "order_status"},
				{Column: "order.create_time", Param: "create_time", Op: "between", Time: true},
				{Column: "order.payment", Param: "payment"},
				{Column: "order.complete_time", Param: "complete_time", Op: "between", Time: true},
			},
			Joins: []Join{
				{Name: "user", Table: "user", Local: "user_id", Foreign: "id", Fields: []string{"id", "nickname", "username", "realname", "mobile", "avatar"}},
				{Name: "technician", Table: "technician", Local: "technician_id", Foreign: "id", Fields: []string{"id", "nickname", "username", "realname", "mobile", "avatar"}},
				{Name: "service", Table: "service", Local: "service_id", Foreign: "id", Fields: []string{"id", "name", "cover"}},
				{Name: "servicespec", Table: "service_spec", Local: "spec_id", Foreign: "id", Fields: []string{"id", "name", "price", "duration"}},
				{Name: "coupon", Table: "coupon", Local: "coupon_id", Foreign: "id", Fields: []string{"id", "name", "money", "discount"}},
			},
			UpdateParse: []string{"create_time", "appointment_time", "complete_time", "receiving_time", "service_time"},
			DumpFields:  "id,order_sn,pay_money,total_money,discount,technician_money,order_status,pay_status,user_id,technician_id,service_id,spec_id,coupon_id,realname,mobile,province,city,district,address,create_time,appointment_time,duration,fare,refund,payment,brokerage,complete_time,cancel_type,lat,lng,cancel_reason,receiving_time,service_time",
			Dump: &Dump{Header: "编号,订单号,支付金额,总金额,优惠金额,技师收入,订单状态,支付状态,用户id,技师id,服务id,规格id,优惠券id,姓名,电话,省,市,区,详细地址,创建时间,预约时间,时长,车费,退款金额,支付方式,佣金,服务完成时间,取消类型,纬度,经度,取消原因,接单时间,开始服务时间", File: "订单列表",
				Times: []string{"create_time", "appointment_time", "complete_time", "receiving_time", "service_time"},
				Labels: map[string]string{
					"order_status": `[{"val":"0","key":"待付款"},{"val":"1","key":"待接单"},{"val":"2","key":"服务中"},{"val":"3","key":"订单完成"},{"val":"4","key":"已评价"},{"val":"5","key":"退款"},{"val":"6","key":"取消"}]`,
					"payment":      `[{"val":"1","key":"余额"},{"val":"2","key":"微信"},{"val":"3","key":"支付宝"}]`,
					"cancel_type":  `[{"key":"用户取消","val":"0"},{"key":"技师取消","val":"1"}]`,
				}},
		},
		tech(),
		simple("Callpolice", "call_police", "callpolice", "id,lat,lng,address,create_time,audio", "id,technician_id,lat,lng,province,city,district,address,create_time,audio",
			[]string{"technician_id", "lat", "lng", "province", "city", "district", "address", "create_time", "audio"}, eqs("id", "id"),
			[]Join{{Name: "technician", Table: "technician", Local: "technician_id", Foreign: "id", Fields: []string{"nickname", "mobile", "realname", "username"}}}),
		simple("Complaint", "complaint", "complaint", "id,order_id,technician_id,user_id,user_type,content,images,create_time,audio", "id,order_id,technician_id,user_id,user_type,content,images,create_time,audio",
			[]string{"order_id", "technician_id", "user_id", "user_type", "content", "images", "create_time", "audio"}, eqs("id", "id"),
			[]Join{{Name: "user", Table: "user", Local: "user_id", Foreign: "id", Fields: []string{"id", "nickname", "username", "realname", "mobile"}}, {Name: "technician", Table: "technician", Local: "technician_id", Foreign: "id", Fields: []string{"id", "nickname", "username", "realname", "mobile"}}}),
		{
			Name: "Coupon", Table: "coupon", Alias: "coupon",
			ListFields:   "id,name,exchange,type,money,discount,condition,start_time,end_time,status",
			InfoFields:   "id,primary_id,name,type,money,discount,condition,service_id,create_time,start_time,end_time",
			DetailFields: "id,name,exchange,type,money,discount,condition,start_time,end_time,status",
			Write:        []string{"primary_id", "name", "type", "money", "discount", "condition", "service_id", "create_time", "start_time", "end_time"},
			Ext:          []string{"status"}, Search: eqs("id", "id", "primary_id", "primary_id", "name", "name", "type", "type"),
		},
		{
			Name: "Couponlog", Table: "coupon", Alias: "couponlog",
			ListFields:   "id,name,money,discount,type,get_type,create_time,start_time,end_time,use,primary_id",
			InfoFields:   "id,primary_id,name,exchange,money,discount,condition,type,get_type,user_id,service_id,create_time,start_time,end_time,status,use",
			DetailFields: "id,name,money,discount,type,get_type,create_time,start_time,end_time,use",
			Write:        []string{"primary_id", "name", "exchange", "money", "discount", "condition", "type", "get_type", "user_id", "service_id", "create_time", "start_time", "end_time", "status", "use"},
			Search:       eqs("id", "id", "couponlog.type", "type", "couponlog.get_type", "get_type", "couponlog.use", "use"),
			Joins:        []Join{{Name: "user", Table: "user", Local: "user_id", Foreign: "id", Fields: []string{"id", "nickname", "username"}}},
			ExtraWhere:   "`couponlog`.`primary_id`>0",
		},
		simple("Member", "member", "member", "id,name,original_price,price,month,sort,status", "id,name,original_price,price,month,sort,status,update_time",
			[]string{"name", "original_price", "price", "month", "sort", "status", "update_time"}, eqs("id", "id", "status", "status"), nil),
		simple("Memberorder", "member_order", "memberorder", "id,order_sn,user_id,pay_money,pay_status,member_name,month,create_time,payment", "id,order_sn,user_id,pay_money,pay_status,member_name,month,create_time,update_time,payment",
			[]string{"order_sn", "user_id", "pay_money", "pay_status", "member_name", "month", "create_time", "update_time", "payment"}, eqs("id", "id", "user_id", "user_id", "payment", "payment"), nil),
		simple("Memberrights", "member_rights", "memberrights", "id,name,icon,profile,status,type,value", "id,name,icon,profile,status,type,value",
			[]string{"name", "icon", "profile", "status", "type", "value"}, eqs("id", "id", "status", "status", "type", "type"), nil),
		{
			Name: "Opinion", Table: "opinion", Alias: "opinion",
			ListFields: "id,content,mobile,images,user_type,type,create_time,reply",
			InfoFields: "id,content,reply,mobile,images,technician_id,user_id,user_type,type,create_time",
			Write:      []string{"content", "reply", "mobile", "images", "technician_id", "user_id", "user_type", "type", "create_time"},
			Search:     eqs("id", "id", "opinion.user_type", "user_type", "opinion.type", "type"),
			Joins: []Join{
				{Name: "user", Table: "user", Local: "user_id", Foreign: "id", Fields: []string{"nickname", "username", "realname", "id"}},
				{Name: "technician", Table: "technician", Local: "technician_id", Foreign: "id", Fields: []string{"username", "realname", "nickname", "id", "mobile"}},
			},
		},
		{
			Name: "Log", Table: "log", Alias: "log",
			ListFields:   "id,application_name,username,url,ip,create_time,type",
			DetailFields: "id,application_name,username,url,ip,useragent,content,errmsg,create_time,type",
			Search:       eqs("id", "id", "username", "username", "type", "type"),
			DumpFields:   "id,application_name,username,url,ip,useragent,content,errmsg,create_time,type",
			Dump: &Dump{Header: "编号,应用名,用户名,请求url,客户端ip,浏览器信息,请求内容,异常信息,创建时间,类型", File: "日志管理", Times: []string{"create_time"},
				Labels: map[string]string{"type": `[{"key":"登录日志","val":"1"},{"key":"操作日志","val":"2"},{"key":"异常日志","val":"3"}]`}},
		},
		simple("Technicianalbum", "technician_album", "technicianalbum", "id,avatar,album,technician_id,status,create_time", "id,avatar,album,technician_id,status,create_time",
			[]string{"avatar", "album", "technician_id", "status", "create_time"}, eqs("id", "id"), nil),
		simple("Technicianali", "technician_ali", "technicianali", "id,technician_id,realname,mobile,create_time", "id,technician_id,realname,mobile,create_time",
			[]string{"technician_id", "realname", "mobile", "create_time"}, eqs("id", "id"), nil),
		simple("Technicianapply", "technician_apply", "technicianapply", "id,realname,sex,mobile,age,avatar,create_time,city,user_id,status", "id,realname,sex,mobile,age,avatar,create_time,city,user_id,status",
			[]string{"realname", "sex", "mobile", "age", "avatar", "create_time", "update_time", "city", "user_id", "status"}, eqs("id", "id", "realname", "realname", "mobile", "mobile", "status", "status"), nil),
		simple("Techniciandata", "technician_data", "techniciandata", "id,height,birthday,profile,technician_id,status,create_time,practice", "id,height,birthday,profile,technician_id,status,create_time,practice",
			[]string{"height", "birthday", "profile", "technician_id", "status", "create_time", "practice"}, eqs("id", "id"), nil),
		simple("Technicianhealthy", "technician_healthy", "technicianhealthy", "id,certificates,technician_id,status,create_time", "id,certificates,technician_id,status,create_time",
			[]string{"certificates", "technician_id", "status", "create_time"}, eqs("id", "id"), nil),
		simple("Technicianidentity", "technician_identity", "technicianidentity", "id,front,back,technician_id,status,create_time,realname,cardsn", "id,front,back,technician_id,status,create_time,realname,cardsn",
			[]string{"front", "back", "technician_id", "status", "create_time", "realname", "cardsn"}, eqs("id", "id"), nil),
		simple("Technicianqualifications", "technician_qualifications", "technicianqualifications", "id,certificates,technician_id,status,create_time", "id,certificates,technician_id,status,create_time",
			[]string{"certificates", "technician_id", "status", "create_time"}, eqs("id", "id"), nil),
		simple("Techniciantcp", "technician_tcp", "techniciantcp", "id,sign,technician_id,status,create_time", "id,sign,technician_id,status,create_time",
			[]string{"sign", "technician_id", "status", "create_time"}, eqs("id", "id"), nil),
		simple("Uploadconfig", "upload_config", "uploadconfig", "id,title,upload_replace,thumb_status,thumb_width,thumb_height,thumb_type", "id,title,upload_replace,thumb_status,thumb_width,thumb_height,thumb_type",
			[]string{"title", "upload_replace", "thumb_status", "thumb_width", "thumb_height", "thumb_type"}, eqs("id", "id", "upload_replace", "upload_replace", "thumb_status", "thumb_status", "thumb_type", "thumb_type"), nil),
		{
			Name: "Userlog", Table: "user_log", Alias: "userlog",
			ListFields: "id,user_id,technician_id,user_type,order_sn,money,type,income,payment,create_time,update_time",
			Write:      []string{"user_id", "technician_id", "user_type", "order_sn", "money", "type", "income", "payment", "create_time", "update_time"},
			Search:     []Search{{Column: "id", Param: "id"}, {Column: "type", Op: "=", Fixed: "4"}},
			Joins:      []Join{{Name: "user", Table: "user", Local: "user_id", Foreign: "id", Fields: []string{"id", "nickname", "username", "realname", "mobile"}}},
		},
		{
			Name: "Withdrawapply", Table: "withdraw_apply", Alias: "withdrawapply",
			ListFields:   "id,sn,user_id,real_name,account,type,user_type,money,left_money,poundage,status,create_time",
			InfoFields:   "*",
			DetailFields: "id,sn,user_id,real_name,account,type,user_type,money,left_money,poundage,status,create_time",
			Write:        []string{"sn", "user_id", "real_name", "account", "type", "user_type", "money", "left_money", "money_qr_code", "poundage", "status", "create_time", "update_time", "payment_no", "payment_time", "deion", "transfer_voucher", "transfer_time", "bank", "subbank"},
			Search:       eqs("id", "id", "withdrawapply.type", "type", "withdrawapply.user_type", "user_type", "withdrawapply.left_money", "left_money", "withdrawapply.status", "status", "withdrawapply.description", "deion"),
			Joins: []Join{
				{Name: "user", Table: "user", Local: "user_id", Foreign: "id", Fields: []string{"id", "nickname", "username", "realname", "mobile"}},
				{Name: "technician", Table: "technician", Local: "user_id", Foreign: "id", Fields: []string{"id", "nickname", "username", "realname", "mobile"}},
			},
		},
	}
	return withExt(list)
}

func withExt(list []*Resource) []*Resource {
	ext := map[string][]string{
		"Adminuser": {"status"}, "Banner": {"sort"}, "Role": {"status"}, "Service": {"sort", "show"},
		"Servicespec": {"sort"}, "Coupon": {"status"}, "Member": {"sort", "status"}, "Memberrights": {"status"},
		"Uploadconfig": {"upload_replace", "thumb_status"},
	}
	for _, r := range list {
		if v, ok := ext[r.Name]; ok {
			r.Ext = v
		}
	}
	return list
}

func tech() *Resource {
	fields := "id,nickname,username,realname,service,week,start_time,end_time,sex,openid,last_login,login_num,last_ip,mobile,avatar,album,status,balance,praise,credit,composite,lat,lng,create_time,update_time,sales,work_status,address,province,city,district,label,unionid,subscribe,hframe,hrocket,hhot,fquantity,tgrade"
	return &Resource{
		Name: "Technician", Table: "technician", Alias: "technician",
		ListFields:   "id,nickname,username,realname,start_time,end_time,sex,mobile,avatar,album,status,balance,praise,credit,composite,work_status,service,hframe,hrocket,hhot,fquantity,tgrade",
		InfoFields:   fields,
		DetailFields: "id,nickname,username,realname,start_time,end_time,sex,mobile,avatar,album,status,balance,praise,credit,composite,work_status,sales,service,week,tgrade",
		Write:        stringsSplit(fields),
		JSONFields:   []string{"album"},
		Search:       eqs("id", "id", "technician.realname", "realname", "technician.mobile", "mobile", "technician.status", "status", "technician.work_status", "work_status"),
		Joins: []Join{
			{Name: "technicianalbum", Table: "technician_album", Local: "id", Foreign: "technician_id", Fields: []string{"id", "avatar", "album", "technician_id", "status", "create_time"}},
			{Name: "technicianali", Table: "technician_ali", Local: "id", Foreign: "technician_id", Fields: []string{"id", "technician_id", "realname", "mobile", "create_time"}},
			{Name: "techniciandata", Table: "technician_data", Local: "id", Foreign: "technician_id", Fields: []string{"id", "height", "birthday", "profile", "technician_id", "status", "create_time", "practice"}},
			{Name: "technicianhealthy", Table: "technician_healthy", Local: "id", Foreign: "technician_id", Fields: []string{"id", "certificates", "technician_id", "status", "create_time"}},
			{Name: "technicianidentity", Table: "technician_identity", Local: "id", Foreign: "technician_id", Fields: []string{"id", "front", "back", "technician_id", "status", "create_time", "realname", "cardsn"}},
			{Name: "technicianqualifications", Table: "technician_qualifications", Local: "id", Foreign: "technician_id", Fields: []string{"id", "certificates", "technician_id", "status", "create_time"}},
			{Name: "techniciantcp", Table: "technician_tcp", Local: "id", Foreign: "technician_id", Fields: []string{"id", "sign", "technician_id", "status", "create_time"}},
		},
	}
}

func stringsSplit(s string) []string {
	parts := make([]string, 0)
	for _, p := range splitComma(s) {
		if p != "" {
			parts = append(parts, p)
		}
	}
	return parts
}

func splitComma(s string) []string {
	out := []string{}
	cur := ""
	for _, r := range s {
		if r == ',' {
			out = append(out, cur)
			cur = ""
			continue
		}
		cur += string(r)
	}
	out = append(out, cur)
	return out
}

func simple(name, table, alias, list, info string, write []string, search []Search, joins []Join) *Resource {
	return &Resource{Name: name, Table: table, Alias: alias, ListFields: list, InfoFields: info, DetailFields: list, Write: write, Search: search, Joins: joins}
}
