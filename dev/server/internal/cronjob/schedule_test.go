package cronjob

import (
	"testing"
	"time"
)

func TestMatchCouponExpression(t *testing.T) {
	hit := time.Date(2026, 2, 1, 1, 45, 10, 0, time.Local)
	if !Match("45 1 1 * *", hit) {
		t.Fatal("expected match")
	}
	miss := time.Date(2026, 2, 1, 1, 46, 0, 0, time.Local)
	if Match("45 1 1 * *", miss) {
		t.Fatal("expected miss")
	}
}
