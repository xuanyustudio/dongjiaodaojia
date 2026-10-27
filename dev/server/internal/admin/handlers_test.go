package admin

import (
	"bytes"
	"database/sql"
	"encoding/json"
	"net/http"
	"net/http/httptest"
	"testing"

	"dongjiao/server/internal/auth"
	"dongjiao/server/internal/config"
	"dongjiao/server/internal/httpx"
	"dongjiao/server/internal/server"
	"dongjiao/server/internal/tokenstore"

	_ "github.com/go-sql-driver/mysql"
)

func testServer(t *testing.T) *server.Server {
	t.Helper()
	cfg := config.Load()
	db, err := sql.Open("mysql", cfg.DSN())
	if err != nil {
		t.Fatal(err)
	}
	t.Cleanup(func() { db.Close() })
	if err := db.Ping(); err != nil {
		t.Skip(err)
	}
	deps := &httpx.Deps{DB: db, Cfg: cfg, Tokens: tokenstore.New()}
	srv := server.New(deps)
	New(deps).Register(srv)
	return srv
}

func TestLoginAndBannerList(t *testing.T) {
	srv := testServer(t)
	cfg := config.Load()
	db, err := sql.Open("mysql", cfg.DSN())
	if err != nil {
		t.Fatal(err)
	}
	defer db.Close()
	pwd := "gotest-pass"
	hash := auth.AdminPassword(pwd, cfg.PasswordSecret)
	res, err := db.Exec("INSERT INTO cd_admin_user(name,user,pwd,role_id,status,create_time) VALUES (?,?,?,?,?,?)", "go测试", "go_test_admin", hash, 1, 1, 1)
	if err != nil {
		t.Fatal(err)
	}
	uid, _ := res.LastInsertId()
	t.Cleanup(func() {
		_, _ = db.Exec("DELETE FROM cd_admin_user WHERE user_id=?", uid)
		_, _ = db.Exec("DELETE FROM cd_log WHERE username=?", "go_test_admin")
		_, _ = db.Exec("DELETE FROM cd_admin_token WHERE username=?", "go_test_admin")
	})
	body := []byte(`{"username":"go_test_admin","password":"gotest-pass"}`)
	rec := httptest.NewRecorder()
	req := httptest.NewRequest(http.MethodPost, "/admin/Login/index", bytes.NewReader(body))
	req.Header.Set("Content-Type", "application/json")
	srv.ServeHTTP(rec, req)
	var login map[string]any
	if err := json.Unmarshal(rec.Body.Bytes(), &login); err != nil {
		t.Fatal(err, rec.Body.String())
	}
	if int(login["status"].(float64)) != 200 {
		t.Fatalf("login body %s", rec.Body.String())
	}
	token, _ := login["token"].(string)
	if token == "" {
		t.Fatal("empty token")
	}
	t.Cleanup(func() {
		db, _ := sql.Open("mysql", config.Load().DSN())
		defer db.Close()
		_, _ = db.Exec("DELETE FROM cd_admin_token WHERE token=?", token)
	})

	rec = httptest.NewRecorder()
	req = httptest.NewRequest(http.MethodPost, "/public/index.php/admin/Banner/index.html", bytes.NewReader([]byte(`{"page":1,"limit":5}`)))
	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Authorization", token)
	srv.ServeHTTP(rec, req)
	var list map[string]any
	if err := json.Unmarshal(rec.Body.Bytes(), &list); err != nil {
		t.Fatal(err, rec.Body.String())
	}
	if int(list["status"].(float64)) != 200 {
		t.Fatalf("banner %s", rec.Body.String())
	}
	data, _ := list["data"].(map[string]any)
	if data["data"] == nil {
		t.Fatalf("missing page data %s", rec.Body.String())
	}

	rec = httptest.NewRecorder()
	req = httptest.NewRequest(http.MethodPost, "/admin/Login/getUserInfo", nil)
	req.Header.Set("Authorization", token)
	srv.ServeHTTP(rec, req)
	var info map[string]any
	if err := json.Unmarshal(rec.Body.Bytes(), &info); err != nil {
		t.Fatal(err)
	}
	if int(info["status"].(float64)) != 200 || info["menu"] == nil {
		t.Fatalf("userinfo %s", rec.Body.String())
	}
}

func TestAdminModules(t *testing.T) {
	srv, db, token := loginAdmin(t)
	paths := []string{
		"/admin/Home/index",
		"/admin/Baseconfig/getInfo",
		"/admin/Valuation/getInfo",
		"/admin/Order/count",
		"/admin/Technician/count",
		"/admin/Withdrawapply/count",
	}
	for _, name := range []string{
		"Adminuser", "Banner", "Role", "Service", "Servicespec", "User", "Order", "Technician",
		"Technicianalbum", "Technicianali", "Techniciandata", "Technicianhealthy", "Technicianidentity",
		"Technicianqualifications", "Techniciantcp", "Technicianapply", "Callpolice", "Complaint",
		"Coupon", "Couponlog", "Member", "Memberorder", "Memberrights", "Opinion", "Log",
		"Uploadconfig", "Userlog", "Withdrawapply",
	} {
		paths = append(paths, "/public/index.php/admin/"+name+"/index.html")
	}
	for _, path := range paths {
		rec := httptest.NewRecorder()
		req := httptest.NewRequest(http.MethodPost, path, bytes.NewReader([]byte(`{"page":1,"limit":5}`)))
		req.Header.Set("Content-Type", "application/json")
		req.Header.Set("Authorization", token)
		srv.ServeHTTP(rec, req)
		var body map[string]any
		if err := json.Unmarshal(rec.Body.Bytes(), &body); err != nil {
			t.Fatalf("%s %s", path, rec.Body.String())
		}
		status, _ := body["status"].(float64)
		if int(status) != 200 {
			t.Fatalf("%s %s", path, rec.Body.String())
		}
	}
	_ = db
}

func loginAdmin(t *testing.T) (*server.Server, *sql.DB, string) {
	t.Helper()
	srv := testServer(t)
	cfg := config.Load()
	db, err := sql.Open("mysql", cfg.DSN())
	if err != nil {
		t.Fatal(err)
	}
	t.Cleanup(func() { db.Close() })
	hash := auth.AdminPassword("gotest-pass", cfg.PasswordSecret)
	res, err := db.Exec("INSERT INTO cd_admin_user(name,user,pwd,role_id,status,create_time) VALUES (?,?,?,?,?,?)", "go测试", "go_mod_"+t.Name(), hash, 1, 1, 1)
	if err != nil {
		t.Fatal(err)
	}
	uid, _ := res.LastInsertId()
	user := "go_mod_" + t.Name()
	t.Cleanup(func() {
		_, _ = db.Exec("DELETE FROM cd_admin_user WHERE user_id=?", uid)
		_, _ = db.Exec("DELETE FROM cd_log WHERE username=?", user)
		_, _ = db.Exec("DELETE FROM cd_admin_token WHERE username=?", user)
	})
	rec := httptest.NewRecorder()
	req := httptest.NewRequest(http.MethodPost, "/admin/Login/index", bytes.NewReader([]byte(`{"username":"`+user+`","password":"gotest-pass"}`)))
	req.Header.Set("Content-Type", "application/json")
	srv.ServeHTTP(rec, req)
	var login map[string]any
	if err := json.Unmarshal(rec.Body.Bytes(), &login); err != nil {
		t.Fatal(err, rec.Body.String())
	}
	if int(login["status"].(float64)) != 200 {
		t.Fatalf("login %s", rec.Body.String())
	}
	token, _ := login["token"].(string)
	return srv, db, token
}

func TestAuthRequired(t *testing.T) {
	srv := testServer(t)
	rec := httptest.NewRecorder()
	req := httptest.NewRequest(http.MethodPost, "/admin/Order/index", bytes.NewReader([]byte(`{}`)))
	req.Header.Set("Content-Type", "application/json")
	srv.ServeHTTP(rec, req)
	var body map[string]any
	_ = json.Unmarshal(rec.Body.Bytes(), &body)
	if int(body["status"].(float64)) != 101 {
		t.Fatalf("%s", rec.Body.String())
	}
}
