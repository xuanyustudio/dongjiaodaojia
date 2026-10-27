package util

import (
	"encoding/json"
	"math"
	"regexp"
	"strconv"
	"strings"
	"time"
	"unicode/utf8"
)

var searchRe = regexp.MustCompile(`(?i)select[\s]+|insert[\s]+|and[\s]+|or[\s]+|create[\s]+|update[\s]+|delete[\s]+|alter[\s]+|count[\s]+|'|/\*|\*|\.\./|\./|union|into|load_file|outfile`)

func SearchIn(s string) string {
	return strings.TrimSpace(searchRe.ReplaceAllString(s, ""))
}

func AsString(v any) string {
	switch t := v.(type) {
	case nil:
		return ""
	case string:
		return t
	case json.Number:
		return t.String()
	case float64:
		if t == math.Trunc(t) {
			return strconv.FormatInt(int64(t), 10)
		}
		return strconv.FormatFloat(t, 'f', -1, 64)
	case int:
		return strconv.Itoa(t)
	case int64:
		return strconv.FormatInt(t, 10)
	case bool:
		if t {
			return "1"
		}
		return "0"
	default:
		b, err := json.Marshal(t)
		if err != nil {
			return ""
		}
		return string(b)
	}
}

func AsInt(v any, def int) int {
	s := strings.TrimSpace(AsString(v))
	if s == "" {
		return def
	}
	n, err := strconv.Atoi(s)
	if err != nil {
		f, err2 := strconv.ParseFloat(s, 64)
		if err2 != nil {
			return def
		}
		return int(f)
	}
	return n
}

func AsFloat(v any) float64 {
	f, _ := strconv.ParseFloat(AsString(v), 64)
	return f
}

func Empty(v any) bool {
	if v == nil {
		return true
	}
	s := AsString(v)
	return s == "" || s == "[]" || s == "null"
}

func Pair(v any) (string, string, bool) {
	switch t := v.(type) {
	case []any:
		if len(t) < 2 {
			return "", "", false
		}
		return AsString(t[0]), AsString(t[1]), true
	case []string:
		if len(t) < 2 {
			return "", "", false
		}
		return t[0], t[1], true
	default:
		return "", "", false
	}
}

func Unix(v string) int64 {
	v = strings.TrimSpace(v)
	if v == "" {
		return 0
	}
	if n, err := strconv.ParseInt(v, 10, 64); err == nil && n > 1000000000 {
		return n
	}
	layouts := []string{"2006-01-02 15:04:05", "2006-01-02 15:04", "2006-01-02", "2006/01/02 15:04:05", "2006/01/02 15:04", "2006/01/02"}
	for _, layout := range layouts {
		if tm, err := time.ParseInLocation(layout, v, time.Local); err == nil {
			return tm.Unix()
		}
	}
	return 0
}

func FormatUnix(v any) string {
	n := AsInt(v, 0)
	if n <= 0 {
		return ""
	}
	return time.Unix(int64(n), 0).Format("2006-01-02 15:04:05")
}

func ItemVal(val, itemConfig string) string {
	if val == "" {
		return ""
	}
	var items []map[string]any
	if err := json.Unmarshal([]byte(itemConfig), &items); err != nil {
		return ""
	}
	var parts []string
	for _, v := range strings.Split(val, ",") {
		for _, m := range items {
			if AsString(m["val"]) == v {
				parts = append(parts, AsString(m["key"]))
			}
		}
	}
	return strings.Join(parts, ",")
}

func ValByKey(val, itemConfig string) string {
	if val == "" {
		return ""
	}
	var items []map[string]any
	if err := json.Unmarshal([]byte(itemConfig), &items); err != nil {
		return ""
	}
	var parts []string
	for _, v := range strings.Split(val, ",") {
		for _, m := range items {
			if AsString(m["key"]) == v {
				parts = append(parts, AsString(m["val"]))
			}
		}
	}
	return strings.Join(parts, ",")
}

func ItemData(v any) string {
	arr, ok := v.([]any)
	if !ok || len(arr) == 0 {
		if s := AsString(v); s != "" && s != "[]" && s != "null" {
			return s
		}
		return ""
	}
	clean := make([]any, 0, len(arr))
	for _, item := range arr {
		if m, ok := item.(map[string]any); ok && len(m) == 0 {
			continue
		}
		clean = append(clean, item)
	}
	if len(clean) == 0 {
		return ""
	}
	b, _ := json.Marshal(clean)
	return string(b)
}

func DaysOfMonth(date string) []string {
	tm, err := time.ParseInLocation("2006-01-02", date, time.Local)
	if err != nil {
		tm = time.Now()
	}
	y, m, _ := tm.Date()
	last := time.Date(y, m+1, 0, 0, 0, 0, 0, time.Local).Day()
	out := make([]string, 0, last)
	for i := 1; i <= last; i++ {
		out = append(out, strconv.Itoa(y)+"-"+strconv.Itoa(int(m))+"-"+strconv.Itoa(i))
	}
	return out
}

func DayRange(day string) (int64, int64) {
	tm, err := time.ParseInLocation("2006-01-02", day, time.Local)
	if err != nil {
		now := time.Now()
		tm = time.Date(now.Year(), now.Month(), now.Day(), 0, 0, 0, 0, time.Local)
	}
	return tm.Unix(), tm.Add(24 * time.Hour).Unix()
}

const codeSource = "J2RSTUV67MWX89KLYZE5FCDG3HQA4B1NOPI"

func CreateCode(userID int) string {
	num := userID + 9999
	code := ""
	for num > 0 {
		mod := num % 35
		num = (num - mod) / 35
		code = string(codeSource[mod]) + code
	}
	if utf8.RuneCountInString(code) < 4 {
		code = strings.Repeat("0", 4-utf8.RuneCountInString(code)) + code
	}
	return code
}

func OrderSN(kind string) string {
	now := time.Now()
	return now.Format("20060102150405") + kind + now.Format("000") + strconv.Itoa(now.Nanosecond()%100)
}

func SafeIdent(s string) bool {
	if s == "" {
		return false
	}
	for _, r := range s {
		if (r >= 'a' && r <= 'z') || (r >= 'A' && r <= 'Z') || (r >= '0' && r <= '9') || r == '_' || r == '.' {
			continue
		}
		return false
	}
	return true
}

func GenerateListTree(rows []map[string]any, pid string, idKey, pidKey string) []map[string]any {
	var tree []map[string]any
	for _, row := range rows {
		if AsString(row[pidKey]) == pid {
			child := copyMap(row)
			child["children"] = GenerateListTree(rows, AsString(row[idKey]), idKey, pidKey)
			tree = append(tree, child)
		}
	}
	if tree == nil {
		tree = []map[string]any{}
	}
	return tree
}

func copyMap(in map[string]any) map[string]any {
	out := make(map[string]any, len(in))
	for k, v := range in {
		out[k] = v
	}
	return out
}

func Truthy(v any) bool {
	s := AsString(v)
	return s != "" && s != "0" && s != "false" && s != "null"
}
