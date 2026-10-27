package dbx

import (
	"database/sql"
	"fmt"
	"math"
	"strings"

	"dongjiao/server/internal/util"
)

type Page struct {
	Total       int              `json:"total"`
	PerPage     int              `json:"per_page"`
	CurrentPage int              `json:"current_page"`
	LastPage    int              `json:"last_page"`
	Data        []map[string]any `json:"data"`
}

func Query(db *sql.DB, q string, args ...any) ([]map[string]any, error) {
	rows, err := db.Query(q, args...)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	return scan(rows)
}

func QueryOne(db *sql.DB, q string, args ...any) (map[string]any, error) {
	list, err := Query(db, q, args...)
	if err != nil {
		return nil, err
	}
	if len(list) == 0 {
		return nil, nil
	}
	return list[0], nil
}

func Exec(db *sql.DB, q string, args ...any) (int64, error) {
	res, err := db.Exec(q, args...)
	if err != nil {
		return 0, err
	}
	id, _ := res.LastInsertId()
	if id == 0 {
		n, _ := res.RowsAffected()
		return n, nil
	}
	return id, nil
}

func Count(db *sql.DB, q string, args ...any) (int, error) {
	var n sql.NullInt64
	err := db.QueryRow(q, args...).Scan(&n)
	if err != nil {
		return 0, err
	}
	return int(n.Int64), nil
}

func Scalar(db *sql.DB, q string, args ...any) (any, error) {
	var v any
	err := db.QueryRow(q, args...).Scan(&v)
	if err == sql.ErrNoRows {
		return nil, nil
	}
	if err != nil {
		return nil, err
	}
	return norm(v), nil
}

func Paginate(db *sql.DB, table, fields, where, order string, args []any, page, limit int) (Page, error) {
	if page < 1 {
		page = 1
	}
	if limit < 1 {
		limit = 20
	}
	total, err := Count(db, "SELECT COUNT(*) FROM "+table+" WHERE "+where, args...)
	if err != nil {
		return Page{}, err
	}
	off := (page - 1) * limit
	list, err := Query(db, "SELECT "+fields+" FROM "+table+" WHERE "+where+" ORDER BY "+order+" LIMIT ? OFFSET ?", append(args, limit, off)...)
	if err != nil {
		return Page{}, err
	}
	last := 0
	if limit > 0 && total > 0 {
		last = int(math.Ceil(float64(total) / float64(limit)))
	}
	return Page{Total: total, PerPage: limit, CurrentPage: page, LastPage: last, Data: list}, nil
}

func Insert(db *sql.DB, table string, cols []string, vals []any) (int64, error) {
	ph := make([]string, len(cols))
	for i := range cols {
		ph[i] = "?"
	}
	q := fmt.Sprintf("INSERT INTO %s (`%s`) VALUES (%s)", table, strings.Join(cols, "`,`"), strings.Join(ph, ","))
	return Exec(db, q, vals...)
}

func Update(db *sql.DB, table, pk string, id any, cols []string, vals []any) error {
	sets := make([]string, len(cols))
	for i, c := range cols {
		sets[i] = "`" + c + "`=?"
	}
	q := fmt.Sprintf("UPDATE %s SET %s WHERE `%s`=?", table, strings.Join(sets, ","), pk)
	_, err := Exec(db, q, append(vals, id)...)
	return err
}

func DeleteIn(db *sql.DB, table, pk string, ids []string) error {
	if len(ids) == 0 {
		return nil
	}
	ph := make([]string, len(ids))
	args := make([]any, len(ids))
	for i, id := range ids {
		ph[i] = "?"
		args[i] = id
	}
	_, err := Exec(db, fmt.Sprintf("DELETE FROM %s WHERE `%s` IN (%s)", table, pk, strings.Join(ph, ",")), args...)
	return err
}

type Cond struct {
	Column string
	Op     string
	Val    any
}

func Where(conds []Cond) (string, []any) {
	parts := []string{"1=1"}
	var args []any
	for _, c := range conds {
		if !util.SafeIdent(c.Column) {
			continue
		}
		col := quoteColumn(c.Column)
		switch strings.ToLower(c.Op) {
		case "=":
			if util.Empty(c.Val) {
				continue
			}
			parts = append(parts, col+" = ?")
			args = append(args, util.AsString(c.Val))
		case "like":
			s := util.SearchIn(util.AsString(c.Val))
			if s == "" {
				continue
			}
			parts = append(parts, col+" LIKE ?")
			args = append(args, "%"+s+"%")
		case ">":
			if util.Empty(c.Val) {
				continue
			}
			parts = append(parts, col+" > ?")
			args = append(args, util.AsString(c.Val))
		case "in":
			ids := splitIDs(c.Val)
			if len(ids) == 0 {
				continue
			}
			ph := make([]string, len(ids))
			for i, id := range ids {
				ph[i] = "?"
				args = append(args, id)
			}
			parts = append(parts, col+" IN ("+strings.Join(ph, ",")+")")
		case "between":
			a, b, ok := util.Pair(c.Val)
			if !ok || strings.TrimSpace(a) == "" {
				continue
			}
			parts = append(parts, col+" BETWEEN ? AND ?")
			args = append(args, a, b)
		}
	}
	return strings.Join(parts, " AND "), args
}

func quoteColumn(col string) string {
	parts := strings.Split(col, ".")
	for i, p := range parts {
		parts[i] = "`" + strings.ReplaceAll(p, "`", "") + "`"
	}
	return strings.Join(parts, ".")
}

func splitIDs(v any) []string {
	switch t := v.(type) {
	case []any:
		var out []string
		for _, item := range t {
			s := util.AsString(item)
			if s != "" {
				out = append(out, s)
			}
		}
		return out
	default:
		s := util.AsString(v)
		if s == "" {
			return nil
		}
		return strings.Split(s, ",")
	}
}

func scan(rows *sql.Rows) ([]map[string]any, error) {
	cols, err := rows.Columns()
	if err != nil {
		return nil, err
	}
	out := make([]map[string]any, 0)
	for rows.Next() {
		vals := make([]any, len(cols))
		ptrs := make([]any, len(cols))
		for i := range vals {
			ptrs[i] = &vals[i]
		}
		if err := rows.Scan(ptrs...); err != nil {
			return nil, err
		}
		row := make(map[string]any, len(cols))
		for i, c := range cols {
			row[c] = norm(vals[i])
		}
		out = append(out, nest(row))
	}
	return out, rows.Err()
}

func norm(v any) any {
	switch t := v.(type) {
	case nil:
		return nil
	case []byte:
		return string(t)
	default:
		return t
	}
}

func nest(row map[string]any) map[string]any {
	out := map[string]any{}
	nested := map[string]map[string]any{}
	for k, v := range row {
		if i := strings.Index(k, "__"); i > 0 {
			name := k[:i]
			field := k[i+2:]
			if nested[name] == nil {
				nested[name] = map[string]any{}
			}
			nested[name][field] = v
			continue
		}
		out[k] = v
	}
	for name, rel := range nested {
		empty := true
		for _, v := range rel {
			if v != nil && util.AsString(v) != "" {
				empty = false
				break
			}
		}
		if empty {
			out[name] = nil
		} else {
			out[name] = rel
		}
	}
	return out
}
