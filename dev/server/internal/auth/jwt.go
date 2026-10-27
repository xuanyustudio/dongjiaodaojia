package auth

import (
	"crypto/hmac"
	"crypto/sha256"
	"encoding/base64"
	"encoding/json"
	"errors"
	"strings"
	"time"
)

type Claims struct {
	Iss string `json:"iss"`
	Aud string `json:"aud"`
	Iat int64  `json:"iat"`
	Exp int64  `json:"exp"`
	UID string `json:"uid"`
}

func Sign(secret, iss, aud, uid string, now time.Time, ttl time.Duration) (string, error) {
	header := base64.RawURLEncoding.EncodeToString([]byte(`{"typ":"JWT","alg":"HS256"}`))
	payload, err := json.Marshal(Claims{
		Iss: iss,
		Aud: aud,
		Iat: now.Unix(),
		Exp: now.Add(ttl).Unix(),
		UID: uid,
	})
	if err != nil {
		return "", err
	}
	body := header + "." + base64.RawURLEncoding.EncodeToString(payload)
	mac := hmac.New(sha256.New, []byte(secret))
	_, _ = mac.Write([]byte(body))
	return body + "." + base64.RawURLEncoding.EncodeToString(mac.Sum(nil)), nil
}

func Parse(token, secret, iss, aud string, now time.Time) (Claims, error) {
	parts := strings.Split(token, ".")
	if len(parts) != 3 {
		return Claims{}, errors.New("token格式错误")
	}
	mac := hmac.New(sha256.New, []byte(secret))
	_, _ = mac.Write([]byte(parts[0] + "." + parts[1]))
	sig := base64.RawURLEncoding.EncodeToString(mac.Sum(nil))
	if !hmac.Equal([]byte(sig), []byte(parts[2])) {
		return Claims{}, errors.New("token失效")
	}
	raw, err := base64.RawURLEncoding.DecodeString(parts[1])
	if err != nil {
		return Claims{}, errors.New("token失效")
	}
	var c Claims
	if err := json.Unmarshal(raw, &c); err != nil {
		return Claims{}, errors.New("token失效")
	}
	if c.Iss != iss || c.Aud != aud {
		return Claims{}, errors.New("token失效")
	}
	if c.Iat > now.Unix() || c.Exp < now.Unix() {
		return Claims{}, errors.New("登录状态已过期，请重新登录")
	}
	return c, nil
}
