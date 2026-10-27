package server

import (
	"net/http"
	"net/http/httptest"
	"net/url"
	"os"
	"path/filepath"
	"strings"
	"testing"

	"dongjiao/server/internal/config"
	"dongjiao/server/internal/httpx"
)

func TestRouteKey(t *testing.T) {
	cases := map[string]string{
		"/admin/Login/index":                        "admin/login/index",
		"/public/index.php/admin/Banner/index.html": "admin/banner/index",
		"/api/user/login":                           "api/user/login",
		"/technician/my.Index/setAlbum":             "technician/my.index/setalbum",
		"/technician/my/Index/index":                "technician/my.index/index",
		"/admin/Sys.Base/createMenu":                "admin/sys.base/createmenu",
	}
	for in, want := range cases {
		if got := routeKey(in); got != want {
			t.Fatalf("%s got %s want %s", in, got, want)
		}
	}
}

func TestServeUpload(t *testing.T) {
	dir := t.TempDir()
	root := filepath.Join(dir, "uploads")
	if err := os.MkdirAll(filepath.Join(root, "admin"), 0o755); err != nil {
		t.Fatal(err)
	}
	if err := os.WriteFile(filepath.Join(root, "admin", "a.png"), []byte("png"), 0o644); err != nil {
		t.Fatal(err)
	}
	if err := os.WriteFile(filepath.Join(dir, "secret.txt"), []byte("secret"), 0o644); err != nil {
		t.Fatal(err)
	}
	srv := New(&httpx.Deps{Cfg: config.Config{UploadRoot: root}})
	ok := httptest.NewRecorder()
	srv.ServeHTTP(ok, httptest.NewRequest(http.MethodGet, "/public/uploads/admin/a.png", nil))
	if ok.Code != 200 || ok.Body.String() != "png" {
		t.Fatalf("upload file got %d %q", ok.Code, ok.Body.String())
	}
	bad := httptest.NewRecorder()
	req := &http.Request{Method: http.MethodGet, URL: &url.URL{Path: "/public/uploads/../secret.txt"}}
	if !serveUpload(bad, req, root) {
		t.Fatal("traversal path was not treated as an upload path")
	}
	if bad.Code != http.StatusNotFound || strings.Contains(bad.Body.String(), "secret") {
		t.Fatalf("traversal got %d %q", bad.Code, bad.Body.String())
	}
}
