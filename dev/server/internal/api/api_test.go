package api

import (
	"bytes"
	"database/sql"
	"encoding/json"
	"net/http"
	"net/http/httptest"
	"testing"

	"dongjiao/server/internal/config"
	"dongjiao/server/internal/httpx"
	"dongjiao/server/internal/server"
	"dongjiao/server/internal/tokenstore"

	_ "github.com/go-sql-driver/mysql"
)

func TestServiceList(t *testing.T) {
	cfg := config.Load()
	db, err := sql.Open("mysql", cfg.DSN())
	if err != nil {
		t.Fatal(err)
	}
	defer db.Close()
	if err := db.Ping(); err != nil {
		t.Skip(err)
	}
	deps := &httpx.Deps{DB: db, Cfg: cfg, Tokens: tokenstore.New()}
	srv := server.New(deps)
	New(deps).Register(srv)
	rec := httptest.NewRecorder()
	req := httptest.NewRequest(http.MethodPost, "/api/index/service", bytes.NewReader([]byte(`{}`)))
	req.Header.Set("Content-Type", "application/json")
	srv.ServeHTTP(rec, req)
	var body map[string]any
	if err := json.Unmarshal(rec.Body.Bytes(), &body); err != nil {
		t.Fatal(err, rec.Body.String())
	}
	if _, ok := body["code"]; !ok {
		t.Fatalf("%s", rec.Body.String())
	}
}

func TestRegisterLoginAddress(t *testing.T) {
	cfg := config.Load()
	db, err := sql.Open("mysql", cfg.DSN())
	if err != nil {
		t.Fatal(err)
	}
	defer db.Close()
	if err := db.Ping(); err != nil {
		t.Skip(err)
	}
	mobile := "19900001111"
	_, _ = db.Exec("DELETE FROM cd_address WHERE user_id IN (SELECT id FROM cd_user WHERE mobile=?)", mobile)
	_, _ = db.Exec("DELETE FROM cd_user WHERE mobile=?", mobile)
	t.Cleanup(func() {
		_, _ = db.Exec("DELETE FROM cd_address WHERE user_id IN (SELECT id FROM cd_user WHERE mobile=?)", mobile)
		_, _ = db.Exec("DELETE FROM cd_user WHERE mobile=?", mobile)
	})
	deps := &httpx.Deps{DB: db, Cfg: cfg, Tokens: tokenstore.New()}
	srv := server.New(deps)
	New(deps).Register(srv)

	post := func(path, token string, body []byte) map[string]any {
		t.Helper()
		rec := httptest.NewRecorder()
		req := httptest.NewRequest(http.MethodPost, path, bytes.NewReader(body))
		req.Header.Set("Content-Type", "application/json")
		if token != "" {
			req.Header.Set("token", token)
		}
		srv.ServeHTTP(rec, req)
		var out map[string]any
		if err := json.Unmarshal(rec.Body.Bytes(), &out); err != nil {
			t.Fatalf("%s %s", path, rec.Body.String())
		}
		return out
	}
	reg := post("/api/user/register", "", []byte(`{"mobile":"19900001111","password":"abc12345"}`))
	if utilCode(reg["code"]) != 1 {
		t.Fatalf("register %#v", reg)
	}
	login := post("/api/user/login", "", []byte(`{"username":"19900001111","password":"abc12345"}`))
	if utilCode(login["code"]) != 1 {
		t.Fatalf("login %#v", login)
	}
	token, _ := login["data"].(string)
	edit := post("/api/address/editAddress", token, []byte(`{"realname":"测试","mobile":"19900001111","province":"广东省","city":"深圳市","district":"南山区","address":"科技园","is_default":1}`))
	if utilCode(edit["code"]) != 1 {
		t.Fatalf("address %#v", edit)
	}
	list := post("/api/address/index", token, []byte(`{}`))
	if utilCode(list["code"]) != 1 {
		t.Fatalf("list %#v", list)
	}
}

func utilCode(v any) int {
	switch n := v.(type) {
	case float64:
		return int(n)
	case string:
		if n == "1" {
			return 1
		}
	}
	return -1
}

func TestNeedLogin(t *testing.T) {
	cfg := config.Load()
	db, err := sql.Open("mysql", cfg.DSN())
	if err != nil {
		t.Fatal(err)
	}
	defer db.Close()
	if err := db.Ping(); err != nil {
		t.Skip(err)
	}
	deps := &httpx.Deps{DB: db, Cfg: cfg, Tokens: tokenstore.New()}
	srv := server.New(deps)
	New(deps).Register(srv)
	rec := httptest.NewRecorder()
	req := httptest.NewRequest(http.MethodPost, "/api/address/index", bytes.NewReader([]byte(`{}`)))
	srv.ServeHTTP(rec, req)
	if !bytes.Contains(rec.Body.Bytes(), []byte("请先登录")) {
		t.Fatal(rec.Body.String())
	}
}
