package auth

import (
	"crypto/rand"
	"encoding/hex"
	"strconv"
	"time"
)

func timeNow() string {
	return strconv.FormatInt(time.Now().UnixNano(), 10)
}

func RandomHex(n int) string {
	b := make([]byte, n)
	_, _ = rand.Read(b)
	return hex.EncodeToString(b)
}
