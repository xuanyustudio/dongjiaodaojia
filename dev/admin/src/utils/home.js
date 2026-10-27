import store from '@/store'

function firstLeaf(list) {
  for (const item of list || []) {
    if (item.children && item.children.length) {
      const child = firstLeaf(item.children)
      if (child) return child
    } else if (item.path) {
      return item.path
    }
  }
  return ''
}

export function homePath() {
  const menu = store.getters.menu || []
  const titled = menu.find(item => (item.title || (item.meta && item.meta.title)) === '首页' && item.path)
  return (titled && titled.path) || firstLeaf(menu) || '/public/index.php/admin/Home/index.html'
}
