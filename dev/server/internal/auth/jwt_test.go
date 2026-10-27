package auth

import (
	"testing"
	"time"
)

const sample = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJjbGllbnQudnVlYWRtaW4iLCJhdWQiOiJzZXJ2ZXIudnVlYWRtaW4iLCJpYXQiOjE2MjI2NDYzNzMsImV4cCI6MTYyMjczMjc3MywidWlkIjoie1widXNlcl9pZFwiOjEsXCJuYW1lXCI6XCJcXHU1YmQyXFx1NTg1OFxcdTUxYjdcXHU2NzA4XCIsXCJ1c2VybmFtZVwiOlwiYWRtaW5cIixcInN0YXR1c1wiOjEsXCJ1c2VyX3JvbGVfaWRzXCI6MSxcInJvbGVfaWRcIjoxLFwicm9sZV9uYW1lXCI6XCJcXHU4ZDg1XFx1N2VhN1xcdTdiYTFcXHU3NDA2XFx1NTQ1OFwiLFwicm9sZV9zdGF0dXNcIjoxLFwidGltZVwiOjE2MjI2NDYzNzN9In0.V1QvHJp-uhYuC6pXPS4TtpZ1g_L65zvDxB9oNys3NsM"

func TestParseSampleToken(t *testing.T) {
	now := time.Unix(1622646373, 0)
	c, err := Parse(sample, "QoCCfOVKwqTNKArT", "client.vueadmin", "server.vueadmin", now)
	if err != nil {
		t.Fatal(err)
	}
	if c.Iss != "client.vueadmin" || c.UID == "" {
		t.Fatalf("claims %+v", c)
	}
}

func TestSignRoundTrip(t *testing.T) {
	now := time.Unix(1700000000, 0)
	tok, err := Sign("QoCCfOVKwqTNKArT", "client.vueadmin", "server.vueadmin", `{"user_id":1}`, now, time.Hour)
	if err != nil {
		t.Fatal(err)
	}
	c, err := Parse(tok, "QoCCfOVKwqTNKArT", "client.vueadmin", "server.vueadmin", now.Add(time.Minute))
	if err != nil {
		t.Fatal(err)
	}
	if c.UID != `{"user_id":1}` {
		t.Fatal(c.UID)
	}
	if _, err := Parse(tok, "QoCCfOVKwqTNKArT", "client.vueadmin", "server.vueadmin", now.Add(2*time.Hour)); err == nil {
		t.Fatal("expected expired")
	}
}

func TestAdminPassword(t *testing.T) {
	if got := AdminPassword("admin", "vueadmin"); len(got) != 32 {
		t.Fatal(got)
	}
}

func TestAuthSignStable(t *testing.T) {
	a := AuthSign(map[string]string{"b": "2", "a": "1"})
	b := AuthSign(map[string]string{"a": "1", "b": "2"})
	if a == "" || a != b {
		t.Fatalf("%s %s", a, b)
	}
}
