package httpx

import (
	"net/http"
	"net/http/httptest"
	"strings"
	"testing"
)

func TestParseNestedForm(t *testing.T) {
	body := "id=16&original_price=619.00&price=419.00&banner[0][name]=a.jpg&banner[0][url]=http://x/a.jpg&label[]=热门&label[]=推荐"
	req := httptest.NewRequest(http.MethodPost, "/x", strings.NewReader(body))
	req.Header.Set("Content-Type", "application/x-www-form-urlencoded")
	got := Parse(req)
	if got["original_price"] != "619.00" || got["price"] != "419.00" || got["id"] != "16" {
		t.Fatalf("scalars: %#v", got)
	}
	banner, ok := got["banner"].([]any)
	if !ok || len(banner) != 1 {
		t.Fatalf("banner: %#v", got["banner"])
	}
	item, ok := banner[0].(map[string]any)
	if !ok || item["name"] != "a.jpg" || item["url"] != "http://x/a.jpg" {
		t.Fatalf("banner item: %#v", banner[0])
	}
	label, ok := got["label"].([]any)
	if !ok || len(label) != 2 || label[0] != "热门" || label[1] != "推荐" {
		t.Fatalf("label: %#v", got["label"])
	}
}
