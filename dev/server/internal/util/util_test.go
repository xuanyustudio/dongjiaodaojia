package util

import "testing"

func TestSearchIn(t *testing.T) {
	if got := SearchIn("1' or 1=1"); stringsContainsDanger(got) {
		t.Fatal(got)
	}
	if SearchIn("13800138000") != "13800138000" {
		t.Fatal(SearchIn("13800138000"))
	}
}

func stringsContainsDanger(s string) bool {
	return containsAny(s, "'", "or ", "union")
}

func containsAny(s string, parts ...string) bool {
	for _, p := range parts {
		if len(p) > 0 && (len(s) >= len(p) && (s == p || len(s) > 0 && contains(s, p))) {
			return true
		}
	}
	return false
}

func contains(s, sub string) bool {
	return len(sub) == 0 || (len(s) >= len(sub) && (s == sub || len(s) > 0 && indexOf(s, sub) >= 0))
}

func indexOf(s, sub string) int {
	for i := 0; i+len(sub) <= len(s); i++ {
		if s[i:i+len(sub)] == sub {
			return i
		}
	}
	return -1
}

func TestCreateCode(t *testing.T) {
	if CreateCode(1) == "" {
		t.Fatal("empty code")
	}
}

func TestDaysOfMonth(t *testing.T) {
	days := DaysOfMonth("2024-02-01")
	if len(days) != 29 {
		t.Fatalf("leap feb %d", len(days))
	}
	if len(DaysOfMonth("2023-02-01")) != 28 {
		t.Fatal("feb")
	}
	if len(DaysOfMonth("2023-04-01")) != 30 {
		t.Fatal("apr")
	}
}

func TestItemVal(t *testing.T) {
	cfg := `[{"key":"用户端","val":"0"},{"key":"技术端","val":"1"}]`
	if ItemVal("0", cfg) != "用户端" {
		t.Fatal(ItemVal("0", cfg))
	}
	if ValByKey("用户端", cfg) != "0" {
		t.Fatal(ValByKey("用户端", cfg))
	}
}

func TestUnix(t *testing.T) {
	if Unix("2022-03-05 16:38:54") <= 0 {
		t.Fatal("parse")
	}
	if Unix("") != 0 {
		t.Fatal("empty")
	}
}
