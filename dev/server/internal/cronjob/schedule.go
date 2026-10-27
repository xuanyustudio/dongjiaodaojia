package cronjob

import (
	"database/sql"
	"strconv"
	"strings"
	"time"

	"dongjiao/server/internal/dbx"
	"dongjiao/server/internal/util"
)

func Match(expr string, now time.Time) bool {
	parts := strings.Fields(expr)
	if len(parts) != 5 {
		return false
	}
	return field(parts[0], now.Minute()) &&
		field(parts[1], now.Hour()) &&
		field(parts[2], now.Day()) &&
		field(parts[3], int(now.Month())) &&
		field(parts[4], int(now.Weekday()))
}

func field(spec string, value int) bool {
	if spec == "*" {
		return true
	}
	for _, item := range strings.Split(spec, ",") {
		if n, err := strconv.Atoi(item); err == nil && n == value {
			return true
		}
	}
	return false
}

func Tick(db *sql.DB, prefix string, now time.Time) {
	rows, err := dbx.Query(db, "SELECT id,command,expression,last_time FROM "+prefix+"dev_crontab WHERE status=1")
	if err != nil {
		return
	}
	start := now.Truncate(time.Minute).Unix()
	for _, row := range rows {
		if util.AsString(row["command"]) != "coupon" || !Match(util.AsString(row["expression"]), now) {
			continue
		}
		if int64(util.AsInt(row["last_time"], 0)) >= start {
			continue
		}
		err := Run(db, prefix)
		msg := ""
		status := 1
		if err != nil {
			msg = err.Error()
			status = 3
		}
		_, _ = dbx.Exec(db, "UPDATE "+prefix+"dev_crontab SET last_time=?, error=?, status=? WHERE id=?", now.Unix(), msg, status, row["id"])
	}
}
