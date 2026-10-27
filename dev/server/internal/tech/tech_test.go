package tech

import (
	"bytes"
	"database/sql"
	"net/http"
	"net/http/httptest"
	"testing"

	"dongjiao/server/internal/config"
	"dongjiao/server/internal/httpx"
	"dongjiao/server/internal/server"
	"dongjiao/server/internal/tokenstore"

	_ "github.com/go-sql-driver/mysql"
)

func TestNewsAndAuth(t *testing.T) {
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
	req := httptest.NewRequest(http.MethodPost, "/technician/news/index", bytes.NewReader([]byte(`{}`)))
	srv.ServeHTTP(rec, req)
	if rec.Code != 200 || !bytes.Contains(rec.Body.Bytes(), []byte("code")) {
		t.Fatal(rec.Body.String())
	}
	rec = httptest.NewRecorder()
	req = httptest.NewRequest(http.MethodPost, "/technician/order/orderList", bytes.NewReader([]byte(`{}`)))
	srv.ServeHTTP(rec, req)
	if !bytes.Contains(rec.Body.Bytes(), []byte("请先登录")) {
		t.Fatal(rec.Body.String())
	}
}
