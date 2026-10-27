package server

import (
	"net/http"
	"os"
	"path/filepath"
	"strings"

	"dongjiao/server/internal/httpx"
)

type Server struct {
	Deps     *httpx.Deps
	handlers map[string]httpx.Handler
}

func New(d *httpx.Deps) *Server {
	return &Server{Deps: d, handlers: map[string]httpx.Handler{}}
}

func (s *Server) Handle(appName, controller, action string, h httpx.Handler) {
	s.handlers[key(appName, controller, action)] = h
}

func (s *Server) ServeHTTP(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Access-Control-Allow-Origin", "*")
	allowHeaders := "Authorization, Content-Type, token, Accept, Origin, X-Requested-With, Access-Control-Allow-Methods"
	if requested := r.Header.Get("Access-Control-Request-Headers"); requested != "" {
		allowHeaders = requested
	}
	w.Header().Set("Access-Control-Allow-Headers", allowHeaders)
	w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, PATCH, DELETE, OPTIONS")
	if r.Method == http.MethodOptions {
		w.WriteHeader(http.StatusNoContent)
		return
	}
	if r.URL.Path == "/health" {
		httpx.JSON(w, map[string]any{"status": 200, "msg": "ok"})
		return
	}
	if s.Deps != nil && serveUpload(w, r, s.Deps.Cfg.UploadRoot) {
		return
	}
	c := &httpx.Ctx{W: w, R: r, Deps: s.Deps, In: httpx.Parse(r)}
	h, ok := s.handlers[routeKey(r.URL.Path)]
	if !ok {
		if strings.HasPrefix(strings.ToLower(strings.Trim(r.URL.Path, "/")), "api") || strings.Contains(r.URL.Path, "/api/") || strings.Contains(r.URL.Path, "/technician/") {
			httpx.Msg(w, 0, "方法不存在", "", 0)
			return
		}
		httpx.Fail(w, 404, "方法不存在")
		return
	}
	h(c)
}

func routeKey(path string) string {
	path = strings.TrimSuffix(path, ".html")
	path = strings.Trim(path, "/")
	for _, prefix := range []string{"public/index.php/", "index.php/"} {
		path = strings.TrimPrefix(path, prefix)
	}
	parts := strings.Split(path, "/")
	if len(parts) < 2 {
		return ""
	}
	appName := parts[0]
	rest := parts[1:]
	var controller, action string
	switch len(rest) {
	case 1:
		controller, action = rest[0], "index"
	case 2:
		controller, action = rest[0], rest[1]
	default:
		if rest[0] == "my" || rest[0] == "My" || rest[0] == "Sys" || rest[0] == "sys" {
			controller = rest[0] + "." + rest[1]
			action = rest[2]
		} else {
			controller, action = rest[0], rest[len(rest)-1]
		}
	}
	return key(appName, controller, action)
}

func serveUpload(w http.ResponseWriter, r *http.Request, root string) bool {
	if r.Method != http.MethodGet && r.Method != http.MethodHead {
		return false
	}
	path := r.URL.Path
	var rel string
	switch {
	case strings.HasPrefix(path, "/public/uploads/"):
		rel = strings.TrimPrefix(path, "/public/uploads/")
	case strings.HasPrefix(path, "/uploads/"):
		rel = strings.TrimPrefix(path, "/uploads/")
	default:
		return false
	}
	if strings.Contains(path, "..") {
		http.NotFound(w, r)
		return true
	}
	rel = filepath.Clean("/" + rel)
	rel = strings.TrimPrefix(rel, string(filepath.Separator))
	rel = strings.TrimPrefix(rel, "/")
	if rel == "" || rel == "." || strings.HasPrefix(rel, "..") {
		http.NotFound(w, r)
		return true
	}
	rootAbs, err := filepath.Abs(root)
	if err != nil {
		http.NotFound(w, r)
		return true
	}
	fullAbs, err := filepath.Abs(filepath.Join(rootAbs, rel))
	if err != nil || !strings.HasPrefix(strings.ToLower(fullAbs), strings.ToLower(rootAbs+string(os.PathSeparator))) {
		http.NotFound(w, r)
		return true
	}
	http.ServeFile(w, r, fullAbs)
	return true
}

func key(appName, controller, action string) string {
	controller = strings.ToLower(strings.ReplaceAll(controller, "/", "."))
	return strings.ToLower(appName) + "/" + controller + "/" + strings.ToLower(action)
}
