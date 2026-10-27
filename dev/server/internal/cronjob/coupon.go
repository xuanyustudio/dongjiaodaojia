package cronjob

import (
	"database/sql"
	"time"

	"dongjiao/server/internal/dbx"
	"dongjiao/server/internal/util"
)

func Run(db *sql.DB, prefix string) error {
	rights, err := dbx.QueryOne(db, "SELECT * FROM "+prefix+"member_rights WHERE id=1 LIMIT 1")
	if err != nil || rights == nil || !util.Truthy(rights["status"]) || util.AsString(rights["value"]) == "" {
		return err
	}
	coupon, err := dbx.QueryOne(db, "SELECT * FROM "+prefix+"coupon WHERE id=? LIMIT 1", rights["value"])
	if err != nil || coupon == nil {
		return err
	}
	users, err := dbx.Query(db, "SELECT id FROM "+prefix+"user WHERE is_vip=1")
	if err != nil {
		return err
	}
	now := time.Now().Unix()
	for _, user := range users {
		_, _ = dbx.Exec(db, "INSERT INTO "+prefix+"coupon(primary_id,user_id,create_time) VALUES (?,?,?)", coupon["id"], user["id"], now)
	}
	return nil
}
