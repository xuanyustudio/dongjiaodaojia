package admin

import "dongjiao/server/internal/httpx"

type RouteRegistrar interface {
	Handle(appName, controller, action string, h httpx.Handler)
}

func (a *Admin) Register(r RouteRegistrar) {
	r.Handle("admin", "Login", "index", a.Login)
	r.Handle("admin", "Login", "logout", a.Logout)
	r.Handle("admin", "Login", "verify", a.Verify)
	r.Handle("admin", "Login", "getUserInfo", a.GetUserInfo)
	r.Handle("admin", "Home", "index", a.HomeIndex)
	r.Handle("admin", "Home", "getNotice", a.GetNotice)
	r.Handle("admin", "Base", "fileList", a.FileList)
	r.Handle("admin", "Base", "deleteFile", a.DeleteFile)
	r.Handle("admin", "Base", "resetPwd", a.ResetPwd)
	r.Handle("admin", "Base", "clearCache", a.ClearCache)
	r.Handle("admin", "Base", "getRoleMenus", a.GetRoleMenus)
	r.Handle("admin", "Baseconfig", "index", a.BaseconfigIndex)
	r.Handle("admin", "Baseconfig", "getInfo", a.BaseconfigInfo)
	r.Handle("admin", "Valuation", "index", a.ValuationIndex)
	r.Handle("admin", "Valuation", "getInfo", a.ValuationInfo)
	r.Handle("admin", "Upload", "upload", a.Upload)
	r.Handle("admin", "Upload", "createFile", a.CreateFile)

	skip := map[string]bool{
		"User/add": true, "User/delete": true, "User/importData": true,
		"Log/add": true, "Log/update": true, "Log/getUpdateInfo": true, "Log/importData": true,
		"Order/importData": true,
	}
	for _, res := range Resources() {
		res := res
		if !skip[res.Name+"/index"] {
			r.Handle("admin", res.Name, "index", a.Index(res))
		}
		if !skip[res.Name+"/updateExt"] {
			r.Handle("admin", res.Name, "updateExt", a.UpdateExt(res))
		}
		if !skip[res.Name+"/add"] {
			r.Handle("admin", res.Name, "add", a.Add(res))
		}
		if !skip[res.Name+"/update"] {
			r.Handle("admin", res.Name, "update", a.Update(res))
		}
		if !skip[res.Name+"/getUpdateInfo"] {
			r.Handle("admin", res.Name, "getUpdateInfo", a.GetUpdateInfo(res))
		}
		if !skip[res.Name+"/delete"] {
			r.Handle("admin", res.Name, "delete", a.Delete(res))
		}
		if !skip[res.Name+"/detail"] {
			r.Handle("admin", res.Name, "detail", a.Detail(res))
		}
		if res.Dump != nil {
			r.Handle("admin", res.Name, "dumpdata", a.Dump(res))
		}
	}
	r.Handle("admin", "Service", "add", a.ServiceAdd)
	r.Handle("admin", "Service", "update", a.ServiceUpdate)
	r.Handle("admin", "Service", "getUpdateInfo", a.ServiceInfo)
	r.Handle("admin", "Order", "count", a.OrderCount)
	r.Handle("admin", "Order", "operateRefund", a.OperateRefund)
	r.Handle("admin", "Technician", "count", a.TechCount)
	r.Handle("admin", "Technician", "updateTgrade", a.UpdateColumn("tgrade"))
	r.Handle("admin", "Technician", "updateFquantity", a.UpdateColumn("fquantity"))
	r.Handle("admin", "Technician", "updateWork", a.UpdateColumn("work_status"))
	r.Handle("admin", "Technician", "updateAuth", a.UpdateAuth)
	r.Handle("admin", "Withdrawapply", "count", a.WithdrawCount)
	r.Handle("admin", "Adminuser", "resetPwd", a.AdminResetPwd)
	r.Handle("admin", "Adminuser", "getFieldList", a.RoleOptions)
	r.Handle("admin", "Servicespec", "getFieldList", a.ServiceOptions)
	r.Handle("admin", "Coupon", "getService", a.CouponServices)
	r.Handle("admin", "Sys.Base", "applicationList", a.ApplicationList)
	r.Handle("admin", "Sys.Base", "getApplicationInfo", a.ApplicationInfo)
	r.Handle("admin", "Sys.Base", "createApplication", a.ApplicationSave)
	r.Handle("admin", "Sys.Base", "updateApplication", a.ApplicationSave)
	r.Handle("admin", "Sys.Base", "deleteApplication", a.ApplicationDelete)
	r.Handle("admin", "Sys.Base", "getMenuList", a.MenuList)
	r.Handle("admin", "Sys.Base", "getMenuInfo", a.MenuInfo)
	r.Handle("admin", "Sys.Base", "updateMenuExt", a.MenuUpdateExt)
	r.Handle("admin", "Sys.Base", "getTables", a.MenuTables)
	r.Handle("admin", "Sys.Base", "create", a.LocalOnly)
	r.Handle("admin", "Sys.Base", "buildApplication", a.LocalOnly)
	r.Handle("admin", "Sys.Base", "createByTable", a.LocalOnly)
	for _, res := range Resources() {
		if res.Name == "Banner" {
			r.Handle("admin", res.Name, "importData", a.ImportRows(res))
		}
	}
}
