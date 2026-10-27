import fs from 'fs'
import path from 'path'

function walk(dir, acc = []) {
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, entry.name)
    if (entry.isDirectory()) {
      if (entry.name === 'node_modules') continue
      walk(full, acc)
    } else if (/\.(vue|js)$/.test(entry.name)) {
      acc.push(full)
    }
  }
  return acc
}

function resolveImport(fromFile, spec) {
  let base
  if (spec.startsWith('@/')) base = path.join('src', spec.slice(2))
  else if (spec.startsWith('.')) base = path.resolve(path.dirname(fromFile), spec)
  else return null
  const candidates = [base, `${base}.js`, `${base}.vue`, path.join(base, 'index.js')]
  return candidates.find((item) => fs.existsSync(item) && fs.statSync(item).isFile()) || null
}

function exportsOf(code) {
  const names = new Set()
  for (const match of code.matchAll(/export\s+(?:async\s+)?(?:function|const|let|var|class)\s+([A-Za-z0-9_]+)/g)) {
    names.add(match[1])
  }
  for (const match of code.matchAll(/export\s*\{([^}]+)\}/g)) {
    for (const part of match[1].split(',')) {
      const trimmed = part.trim()
      if (!trimmed) continue
      const pieces = trimmed.split(/\s+as\s+/)
      names.add((pieces[1] || pieces[0]).trim())
    }
  }
  return names
}

const missing = new Map()
for (const file of walk('src')) {
  const code = fs.readFileSync(file, 'utf8')
  for (const match of code.matchAll(/import\s*\{([^}]+)\}\s*from\s*['"]([^'"]+)['"]/g)) {
    const target = resolveImport(file, match[2])
    if (!target || !target.endsWith('.js')) continue
    const names = exportsOf(fs.readFileSync(target, 'utf8'))
    for (const part of match[1].split(',')) {
      const trimmed = part.trim()
      if (!trimmed) continue
      const name = trimmed.split(/\s+as\s+/)[0].trim()
      if (!name || names.has(name)) continue
      if (!missing.has(target)) missing.set(target, new Set())
      missing.get(target).add(name)
    }
  }
}

for (const [target, names] of missing) {
  const controller = path.basename(target, '.js')
  const pascal = controller.charAt(0).toUpperCase() + controller.slice(1)
  const lines = ['']
  for (const name of names) {
    lines.push(`export function ${name}(params) {`)
    lines.push(`  return request({ url: '/${pascal}/${name}', method: 'post', data: params })`)
    lines.push('}')
  }
  const current = fs.readFileSync(target, 'utf8')
  if (!current.includes("from '@/api/request'") && !current.includes('from "@/api/request"')) {
    console.log('SKIP no request import', target, [...names].join(','))
    continue
  }
  fs.appendFileSync(target, lines.join('\n') + '\n')
  console.log('shimmed', target, [...names].join(','))
}
