import fs from 'fs'
import path from 'path'

const srcRoot = path.resolve('D:/work/dongjiaodaojia/backend/ui/src')
const destRoot = path.resolve('D:/work/dongjiaodaojia/backend/ui-next/src')

function walk(dir, out = []) {
  for (const name of fs.readdirSync(dir)) {
    const full = path.join(dir, name)
    const stat = fs.statSync(full)
    if (stat.isDirectory()) walk(full, out)
    else out.push(full)
  }
  return out
}

function findTagEnd(html, start) {
  let quote = ''
  for (let i = start; i < html.length; i++) {
    const ch = html[i]
    if (quote) {
      if (ch === quote) quote = ''
      continue
    }
    if (ch === '"' || ch === "'") {
      quote = ch
      continue
    }
    if (ch === '>') return i
  }
  return html.length - 1
}

function findMatchingClose(html, from, name) {
  const openRe = new RegExp(`^<${name}(?=[\\s>/])`, 'i')
  const closeRe = new RegExp(`^</${name}\\s*>`, 'i')
  let depth = 1
  let i = from
  while (i < html.length) {
    const lt = html.indexOf('<', i)
    if (lt === -1) break
    if (html.startsWith('<!--', lt)) {
      i = html.indexOf('-->', lt) + 3
      continue
    }
    if (html.startsWith('</', lt)) {
      if (closeRe.test(html.slice(lt))) {
        depth--
        const end = html.indexOf('>', lt) + 1
        if (depth === 0) return { start: lt, end }
        i = end
        continue
      }
      i = html.indexOf('>', lt) + 1
      continue
    }
    if (openRe.test(html.slice(lt))) {
      const end = findTagEnd(html, lt)
      const selfClose = /\/\s*>$/.test(html.slice(lt, end + 1))
      if (!selfClose) depth++
      i = end + 1
      continue
    }
    i = lt + 1
  }
  return { start: html.length, end: html.length }
}

function transformTemplate(html) {
  let i = 0
  let out = ''
  while (i < html.length) {
    if (html[i] !== '<') {
      out += html[i++]
      continue
    }
    if (html.startsWith('<!--', i)) {
      const end = html.indexOf('-->', i)
      const stop = end === -1 ? html.length : end + 3
      out += html.slice(i, stop)
      i = stop
      continue
    }
    if (html.startsWith('</', i)) {
      const end = html.indexOf('>', i)
      out += html.slice(i, end + 1)
      i = end + 1
      continue
    }
    const end = findTagEnd(html, i)
    let tag = html.slice(i, end + 1)
    const nameMatch = tag.match(/^<([A-Za-z][\w-]*)/)
    if (!nameMatch) {
      out += tag
      i = end + 1
      continue
    }
    const name = nameMatch[1]
    const slot = tag.match(/\sslot="([^"]*)"/)
    const slotScope = tag.match(/\sslot-scope="([^"]*)"/)
    const selfClosing = /\/\s*>$/.test(tag)
    if (slot || slotScope) {
      tag = tag.replace(/\sslot="[^"]*"/, '').replace(/\sslot-scope="[^"]*"/, '')
      const directive = slot && slotScope
        ? `#${slot[1]}="${slotScope[1]}"`
        : slotScope
          ? `#default="${slotScope[1]}"`
          : `#${slot[1]}`
      if (name.toLowerCase() === 'template') {
        tag = tag.replace(/^<template\b/, `<template ${directive}`)
        out += tag
        i = end + 1
        continue
      }
      if (selfClosing) {
        out += `<template ${directive}>${tag}</template>`
        i = end + 1
        continue
      }
      const close = findMatchingClose(html, end + 1, name)
      const inner = transformTemplate(html.slice(end + 1, close.start))
      out += `<template ${directive}>${tag}${inner}</${name}></template>`
      i = close.end
      continue
    }
    out += tag
    i = end + 1
  }
  return out
}

function commonReplacements(content) {
  let out = content
  out = out.replace(/:visible\.sync=/g, 'v-model=')
  out = out.replace(/:([A-Za-z_][\w-]*)\.sync=/g, 'v-model:$1=')
  out = out.replace(/\.native\b/g, '')
  out = out.replace(/size="mini"/g, 'size="small"')
  out = out.replace(/size:\s*'mini'/g, "size: 'small'")
  out = out.replace(/size:\s*"mini"/g, 'size: "small"')
  out = out.replace(/\bbeforeDestroy\b/g, 'beforeUnmount')
  out = out.replace(/\bdestroyed\s*\(/g, 'unmounted(')
  out = out.replace(/\bdestroyed\s*:/g, 'unmounted:')
  out = out.replace(/el-submenu/g, 'el-sub-menu')
  out = out.replace(/from ['"]element-ui['"]/g, "from 'element-plus'")
  out = out.replace(/import\s*\{\s*Message\s*,\s*MessageBox\s*\}\s*from\s*'element-plus'/g, "import { ElMessage as Message, ElMessageBox as MessageBox } from 'element-plus'")
  out = out.replace(/from ['"]@riophae\/vue-treeselect['"]/g, "from 'vue3-treeselect'")
  out = out.replace(/@riophae\/vue-treeselect\/dist\/vue-treeselect\.css/g, 'vue3-treeselect/dist/vue3-treeselect.css')
  out = out.replace(/import XLSX from ['"]xlsx['"]/g, "import * as XLSX from 'xlsx'")
  out = out.replace(/import echarts from ['"]echarts['"]/g, "import * as echarts from 'echarts'")
  out = out.replace(/::v-deep\s*([^{;\n]+)/g, (_, sel) => `:deep(${sel.trim()})`)
  out = out.replace(/\/deep\/\s*([^{;\n]+)/g, (_, sel) => `:deep(${sel.trim()})`)
  out = out.replace(/>>>\s*([^{;\n]+)/g, (_, sel) => `:deep(${sel.trim()})`)
  out = out.replace(/this\.\$set\(\s*([^,]+?)\s*,\s*([^,]+?)\s*,\s*([\s\S]*?)\)/g, '($1[$2] = $3)')
  out = out.replace(/<el-button(\s[^>]*?)\sicon="(el-icon-[^"]+)"([^>]*?)>/g, (full, a, icon, b) => {
    if (full.endsWith('/>')) return full
    return `<el-button${a}${b}><i class="${icon}"></i>`
  })
  return out
}

function transformVue(content) {
  const next = commonReplacements(content)
  const start = next.indexOf('<template')
  if (start === -1) return next
  const openEnd = findTagEnd(next, start)
  const close = findMatchingClose(next, openEnd + 1, 'template')
  if (close.start >= next.length) return next
  const inner = transformTemplate(next.slice(openEnd + 1, close.start))
  return next.slice(0, openEnd + 1) + inner + next.slice(close.start)
}

fs.mkdirSync(destRoot, { recursive: true })
for (const file of walk(srcRoot)) {
  const rel = path.relative(srcRoot, file)
  const target = path.join(destRoot, rel)
  fs.mkdirSync(path.dirname(target), { recursive: true })
  const ext = path.extname(file).toLowerCase()
  if (ext === '.vue') {
    fs.writeFileSync(target, transformVue(fs.readFileSync(file, 'utf8')))
  } else if (ext === '.js') {
    fs.writeFileSync(target, commonReplacements(fs.readFileSync(file, 'utf8')))
  } else {
    fs.copyFileSync(file, target)
  }
}
console.log('migrated', destRoot)
