package main

import (
	"database/sql"
	"log"
	"net/http"
	"time"

	"dongjiao/server/internal/admin"
	"dongjiao/server/internal/api"
	"dongjiao/server/internal/config"
	"dongjiao/server/internal/cronjob"
	"dongjiao/server/internal/httpx"
	"dongjiao/server/internal/server"
	"dongjiao/server/internal/tech"
	"dongjiao/server/internal/tokenstore"

	_ "github.com/go-sql-driver/mysql"
)

func main() {
	loc, err := time.LoadLocation("Asia/Shanghai")
	if err == nil {
		time.Local = loc
	}
	cfg := config.Load()
	db, err := sql.Open("mysql", cfg.DSN())
	if err != nil {
		log.Fatal(err)
	}
	db.SetMaxOpenConns(50)
	db.SetMaxIdleConns(10)
	db.SetConnMaxLifetime(30 * time.Minute)
	if err := db.Ping(); err != nil {
		log.Fatal(err)
	}
	deps := &httpx.Deps{DB: db, Cfg: cfg, Tokens: tokenstore.New()}
	srv := server.New(deps)
	admin.New(deps).Register(srv)
	api.New(deps).Register(srv)
	tech.New(deps).Register(srv)
	go func() {
		cronjob.Tick(db, cfg.DBPrefix, time.Now())
		ticker := time.NewTicker(time.Minute)
		defer ticker.Stop()
		for now := range ticker.C {
			cronjob.Tick(db, cfg.DBPrefix, now)
		}
	}()
	log.Println("dongjiao go server listening", cfg.HTTPAddr)
	if err := http.ListenAndServe(cfg.HTTPAddr, srv); err != nil {
		log.Fatal(err)
	}
}
