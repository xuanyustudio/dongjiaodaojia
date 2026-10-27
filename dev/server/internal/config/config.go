package config

import (
	"os"
	"strconv"
	"time"
)

type Config struct {
	HTTPAddr       string
	DBHost         string
	DBPort         string
	DBUser         string
	DBPass         string
	DBName         string
	DBPrefix       string
	UploadRoot     string
	PublicDomain   string
	PasswordSecret string
	JWTSecret      string
	JWTIss         string
	JWTAud         string
	JWTExpire      time.Duration
	MultipleLogin  bool
	VerifyStatus   bool
	ShowHomeChats  bool
	ShowHomeMenu   bool
	CheckFileExist bool
	DumpLimit      int
	DumpExt        string
}

func Load() Config {
	return Config{
		HTTPAddr:       env("DJ_HTTP_ADDR", ":8088"),
		DBHost:         env("DJ_DB_HOST", "127.0.0.1"),
		DBPort:         env("DJ_DB_PORT", "3306"),
		DBUser:         env("DJ_DB_USER", "root"),
		DBPass:         env("DJ_DB_PASS", "root"),
		DBName:         env("DJ_DB_NAME", "smam_sql"),
		DBPrefix:       env("DJ_DB_PREFIX", "cd_"),
		UploadRoot:     env("DJ_UPLOAD_ROOT", "../../backend/public/uploads"),
		PublicDomain:   env("DJ_PUBLIC_DOMAIN", ""),
		PasswordSecret: env("DJ_PASSWORD_SECRET", "vueadmin"),
		JWTSecret:      env("DJ_JWT_SECRET", "QoCCfOVKwqTNKArT"),
		JWTIss:         "client.vueadmin",
		JWTAud:         "server.vueadmin",
		JWTExpire:      24 * time.Hour,
		MultipleLogin:  envBool("DJ_MULTIPLE_LOGIN", true),
		VerifyStatus:   true,
		ShowHomeChats:  true,
		ShowHomeMenu:   false,
		CheckFileExist: true,
		DumpLimit:      1000,
		DumpExt:        "xlsx",
	}
}

func (c Config) DSN() string {
	return c.DBUser + ":" + c.DBPass + "@tcp(" + c.DBHost + ":" + c.DBPort + ")/" + c.DBName + "?charset=utf8mb4&parseTime=false&loc=Local&multiStatements=true"
}

func env(key, def string) string {
	if v := os.Getenv(key); v != "" {
		return v
	}
	return def
}

func envBool(key string, def bool) bool {
	v := os.Getenv(key)
	if v == "" {
		return def
	}
	b, err := strconv.ParseBool(v)
	if err != nil {
		return def
	}
	return b
}
