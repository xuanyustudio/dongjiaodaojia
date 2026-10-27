package tokenstore

import (
	"sync"
	"time"
)

type entry struct {
	ID     int64
	Expire time.Time
}

type Store struct {
	mu   sync.Mutex
	data map[string]entry
}

func New() *Store {
	return &Store{data: map[string]entry{}}
}

func (s *Store) Set(prefix, token string, id int64, ttl time.Duration) {
	s.mu.Lock()
	defer s.mu.Unlock()
	s.data[prefix+token] = entry{ID: id, Expire: time.Now().Add(ttl)}
}

func (s *Store) Get(prefix, token string) (int64, bool) {
	s.mu.Lock()
	defer s.mu.Unlock()
	e, ok := s.data[prefix+token]
	if !ok || time.Now().After(e.Expire) || e.ID == 0 {
		return 0, false
	}
	return e.ID, true
}

func (s *Store) Del(prefix, token string) {
	s.mu.Lock()
	defer s.mu.Unlock()
	delete(s.data, prefix+token)
}
