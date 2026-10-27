package auth

import (
	"crypto/md5"
	"crypto/sha1"
	"encoding/hex"
	"net/url"
	"sort"
	"strings"

	"golang.org/x/crypto/bcrypt"
)

func AdminPassword(plain, secret string) string {
	sum := md5.Sum([]byte(plain + secret))
	return hex.EncodeToString(sum[:])
}

func HashUserPassword(plain string) (string, error) {
	b, err := bcrypt.GenerateFromPassword([]byte(plain), 13)
	if err != nil {
		return "", err
	}
	return string(b), nil
}

func CheckUserPassword(hash, plain string) bool {
	if hash == "" {
		return false
	}
	return bcrypt.CompareHashAndPassword([]byte(hash), []byte(plain)) == nil
}

// AuthSign 对齐 extend/yt/helper/SignHelper::authSign。
func AuthSign(data map[string]string) string {
	keys := make([]string, 0, len(data))
	for k := range data {
		keys = append(keys, k)
	}
	sort.Strings(keys)
	vals := url.Values{}
	for _, k := range keys {
		vals.Set(k, data[k])
	}
	query := vals.Encode()
	return sha1Hex(encryptValue(query))
}

func encryptValue(value string) string {
	return sha1Hex(sha1Hex("fun_") + md5Hex(value) + md5Hex("_encrypt") + sha1Hex(value))
}

func sha1Hex(s string) string {
	sum := sha1.Sum([]byte(s))
	return hex.EncodeToString(sum[:])
}

func md5Hex(s string) string {
	sum := md5.Sum([]byte(s))
	return hex.EncodeToString(sum[:])
}

func RandomToken() string {
	return md5Hex(strings.Join([]string{sha1Hex(timeNow()), AdminPassword(timeNow(), "token")}, ""))
}
