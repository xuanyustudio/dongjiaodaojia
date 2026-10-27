package httpx

import (
	"database/sql"
	"encoding/json"
	"errors"
	"io"
	"mime"
	"net"
	"net/http"
	"net/url"
	"strings"

	"dongjiao/server/internal/config"
	"dongjiao/server/internal/tokenstore"
	"dongjiao/server/internal/util"
)

type Deps struct {
	DB     *sql.DB
	Cfg    config.Config
	Tokens *tokenstore.Store
}

type Ctx struct {
	W    http.ResponseWriter
	R    *http.Request
	Deps *Deps
	In   map[string]any
}

type Handler func(*Ctx)

func (c *Ctx) Str(key string) string {
	return util.SearchIn(util.AsString(c.In[key]))
}

func (c *Ctx) Raw(key string) string {
	return strings.TrimSpace(util.AsString(c.In[key]))
}

func (c *Ctx) Int(key string, def int) int {
	if _, ok := c.In[key]; !ok {
		return def
	}
	return util.AsInt(c.In[key], def)
}

func (c *Ctx) Any(key string) any {
	return c.In[key]
}

func (c *Ctx) IP() string {
	if x := c.R.Header.Get("X-Forwarded-For"); x != "" {
		return strings.TrimSpace(strings.Split(x, ",")[0])
	}
	host, _, err := net.SplitHostPort(c.R.RemoteAddr)
	if err != nil {
		return c.R.RemoteAddr
	}
	return host
}

func (c *Ctx) HeaderToken(name string) string {
	v := strings.Trim(c.R.Header.Get(name), `"`)
	return strings.TrimSpace(v)
}

func JSON(w http.ResponseWriter, v any) {
	w.Header().Set("Content-Type", "application/json; charset=utf-8")
	enc := json.NewEncoder(w)
	enc.SetEscapeHTML(false)
	_ = enc.Encode(v)
}

func OK(w http.ResponseWriter, extra map[string]any) {
	body := map[string]any{"status": 200}
	for k, v := range extra {
		body[k] = v
	}
	JSON(w, body)
}

func Fail(w http.ResponseWriter, status int, msg string) {
	JSON(w, map[string]any{"status": status, "msg": msg})
}

func Msg(w http.ResponseWriter, code any, msg string, data any, count int) {
	if data == nil {
		data = ""
	}
	JSON(w, map[string]any{"code": code, "msg": msg, "data": data, "count": count})
}

func Parse(r *http.Request) map[string]any {
	out := map[string]any{}
	mergeQuery(out, r.URL.Query())
	ct := r.Header.Get("Content-Type")
	media, _, _ := mime.ParseMediaType(ct)
	body, _ := io.ReadAll(io.LimitReader(r.Body, 20<<20))
	switch media {
	case "application/json":
		if len(body) > 0 {
			dec := json.NewDecoder(strings.NewReader(string(body)))
			dec.UseNumber()
			var v any
			if err := dec.Decode(&v); err == nil {
				if _, ok := v.([]any); ok {
					out["__list"] = normalize(v)
				} else {
					mergeJSON(out, v)
				}
			}
		}
	default:
		if len(body) > 0 {
			if vals, err := url.ParseQuery(string(body)); err == nil {
				mergeQuery(out, vals)
			}
		}
		if media == "multipart/form-data" {
			r.Body = io.NopCloser(strings.NewReader(string(body)))
			_ = r.ParseMultipartForm(20 << 20)
			if r.MultipartForm != nil {
				for k, vs := range r.MultipartForm.Value {
					assignKey(out, k, vs)
				}
			}
		}
	}
	return out
}

func mergeJSON(out map[string]any, v any) {
	m, ok := v.(map[string]any)
	if !ok {
		return
	}
	for k, val := range m {
		out[k] = normalize(val)
	}
}

func normalize(v any) any {
	switch t := v.(type) {
	case json.Number:
		return t.String()
	case float64:
		return util.AsString(t)
	case []any:
		arr := make([]any, len(t))
		for i, item := range t {
			arr[i] = normalize(item)
		}
		return arr
	case map[string]any:
		m := map[string]any{}
		for k, item := range t {
			m[k] = normalize(item)
		}
		return m
	default:
		return t
	}
}

func mergeQuery(out map[string]any, vals url.Values) {
	for k, vs := range vals {
		assignKey(out, k, vs)
	}
}

func assignKey(out map[string]any, key string, vs []string) {
	tokens := splitFormKey(key)
	if len(tokens) == 1 {
		if len(vs) == 1 {
			out[key] = vs[0]
			return
		}
		arr := make([]any, len(vs))
		for i, v := range vs {
			arr[i] = v
		}
		out[key] = arr
		return
	}
	for _, v := range vs {
		putMap(out, tokens, v)
	}
}

func splitFormKey(key string) []string {
	if !strings.Contains(key, "[") {
		return []string{key}
	}
	var tokens []string
	rest := key
	for {
		i := strings.Index(rest, "[")
		if i < 0 {
			if rest != "" {
				tokens = append(tokens, rest)
			}
			return tokens
		}
		if i > 0 {
			tokens = append(tokens, rest[:i])
		}
		rest = rest[i+1:]
		j := strings.Index(rest, "]")
		if j < 0 {
			tokens = append(tokens, rest)
			return tokens
		}
		tokens = append(tokens, rest[:j])
		rest = rest[j+1:]
	}
}

func putMap(m map[string]any, tokens []string, value string) {
	if len(tokens) == 0 {
		return
	}
	tok := tokens[0]
	if len(tokens) == 1 {
		if tok != "" {
			m[tok] = value
		}
		return
	}
	if tokens[1] == "" || isDigits(tokens[1]) {
		cur, _ := m[tok].([]any)
		m[tok] = putSlice(cur, tokens[1:], value)
		return
	}
	child, _ := m[tok].(map[string]any)
	if child == nil {
		child = map[string]any{}
	}
	putMap(child, tokens[1:], value)
	m[tok] = child
}

func putSlice(s []any, tokens []string, value string) []any {
	if len(tokens) == 0 {
		return s
	}
	tok := tokens[0]
	idx := len(s)
	if tok == "" {
		s = append(s, nil)
	} else {
		n, err := parseIndex(tok)
		if err != nil {
			return s
		}
		idx = n
		for len(s) <= idx {
			s = append(s, nil)
		}
	}
	if len(tokens) == 1 {
		s[idx] = value
		return s
	}
	if tokens[1] == "" || isDigits(tokens[1]) {
		child, _ := s[idx].([]any)
		s[idx] = putSlice(child, tokens[1:], value)
		return s
	}
	child, _ := s[idx].(map[string]any)
	if child == nil {
		child = map[string]any{}
	}
	putMap(child, tokens[1:], value)
	s[idx] = child
	return s
}

func isDigits(s string) bool {
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

func parseIndex(s string) (int, error) {
	n := 0
	if s == "" {
		return 0, errIndex
	}
	for _, r := range s {
		if r < '0' || r > '9' {
			return 0, errIndex
		}
		n = n*10 + int(r-'0')
	}
	return n, nil
}

var errIndex = errors.New("index")
