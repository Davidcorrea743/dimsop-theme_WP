// Extrae las cadenas traducibles del tema (dominio 'dimsop') a un .pot
// Uso: node make-pot.js <dir-tema> <salida.pot>
const fs = require('fs');
const path = require('path');

const root = process.argv[2];
const out = process.argv[3];

const FUNCS = {
    '__': ['s'], '_e': ['s'], 'esc_html__': ['s'], 'esc_html_e': ['s'], 'esc_attr__': ['s'], 'esc_attr_e': ['s'],
    '_x': ['s', 'c'], 'esc_html_x': ['s', 'c'], 'esc_attr_x': ['s', 'c'], '_ex': ['s', 'c'],
    '_n': ['s', 'p', null], '_nx': ['s', 'p', null, 'c'],
};

function walk(dir, acc) {
    for (const name of fs.readdirSync(dir)) {
        if (['node_modules', '.git', 'vendor', 'languages'].includes(name)) continue;
        const full = path.join(dir, name);
        const st = fs.statSync(full);
        if (st.isDirectory()) walk(full, acc);
        else if (name.endsWith('.php')) acc.push(full);
    }
    return acc;
}

// Lee un literal PHP entre comillas simples o dobles desde i; devuelve [valor, fin] o null
function readString(src, i) {
    const q = src[i];
    if (q !== "'" && q !== '"') return null;
    let j = i + 1, val = '';
    while (j < src.length) {
        const ch = src[j];
        if (ch === '\\') {
            const nx = src[j + 1];
            if (q === "'") { val += (nx === "'" || nx === '\\') ? nx : '\\' + nx; }
            else { val += ({ n: '\n', t: '\t', '"': '"', '\\': '\\', '$': '$' })[nx] ?? ('\\' + nx); }
            j += 2; continue;
        }
        if (ch === q) return [val, j + 1];
        if (q === '"' && ch === '$') return null; // interpolación: no traducible
        val += ch; j++;
    }
    return null;
}

function skipWs(src, i) {
    while (i < src.length && /\s/.test(src[i])) i++;
    return i;
}

// Lee los argumentos; los que no son literales quedan como null
function readArgs(src, i) {
    const args = [];
    i = skipWs(src, i);
    while (i < src.length) {
        const s = readString(src, i);
        let val = null;
        if (s) {
            val = s[0]; i = skipWs(src, s[1]);
            // concatenación de literales: 'a' . 'b'
            while (src[i] === '.') {
                const n = readString(src, skipWs(src, i + 1));
                if (!n) { val = null; break; }
                val += n[0]; i = skipWs(src, n[1]);
            }
        }
        // avanzar hasta la coma o el paréntesis de cierre del nivel actual
        let depth = 0;
        while (i < src.length) {
            const ch = src[i];
            if (ch === "'" || ch === '"') { const r = readString(src, i); i = r ? r[1] : i + 1; continue; }
            if (ch === '(' || ch === '[') depth++;
            else if (ch === ')' || ch === ']') { if (depth === 0) break; depth--; }
            else if (ch === ',' && depth === 0) break;
            if (s && val !== null && !/\s/.test(ch)) val = null; // había algo más que un literal
            i++;
        }
        args.push(val);
        if (src[i] === ')') return [args, i + 1];
        i = skipWs(src, i + 1);
    }
    return [args, i];
}

const entries = new Map(); // clave: ctx\u0004msgid
const files = walk(root, []).sort();
const fnRe = new RegExp('(?<![\\w>$:])(' + Object.keys(FUNCS).sort((a, b) => b.length - a.length).join('|') + ')\\s*\\(', 'g');

for (const file of files) {
    const src = fs.readFileSync(file, 'utf8');
    const rel = path.relative(root, file).split(path.sep).join('/');
    let m;
    fnRe.lastIndex = 0;
    while ((m = fnRe.exec(src))) {
        const spec = FUNCS[m[1]];
        const [args] = readArgs(src, m.index + m[0].length);
        const domain = args[spec.length];
        if (domain !== 'dimsop') continue;
        const msgid = args[0];
        if (msgid === null || msgid === undefined) continue;
        const ctx = spec.includes('c') ? args[spec.indexOf('c')] : null;
        const plural = spec.includes('p') ? args[spec.indexOf('p')] : null;
        const line = src.slice(0, m.index).split('\n').length;
        // comentario translators: en las 3 líneas anteriores
        const before = src.slice(0, m.index).split('\n').slice(-4).join('\n');
        const tc = before.match(/translators:\s*([^\n*]*?)\s*(\*\/|\n|$)/);
        const key = (ctx || '') + '\u0004' + msgid;
        if (!entries.has(key)) entries.set(key, { msgid, ctx, plural, refs: [], comments: new Set() });
        const e = entries.get(key);
        e.refs.push(rel + ':' + line);
        if (tc && tc[1]) e.comments.add(tc[1].trim());
        if (plural && !e.plural) e.plural = plural;
    }
}

function po(str) {
    return '"' + str.replace(/\\/g, '\\\\').replace(/"/g, '\\"').replace(/\n/g, '\\n').replace(/\t/g, '\\t') + '"';
}

const theme = fs.readFileSync(path.join(root, 'style.css'), 'utf8');
const version = (theme.match(/Version:\s*(.+)/) || [])[1] || '1.0.0';
let txt = `# Copyright (C) ${new Date().getFullYear()} Dimsop Technology
# This file is distributed under the same license as the Dimsop theme.
msgid ""
msgstr ""
"Project-Id-Version: Dimsop ${version.trim()}\\n"
"Report-Msgid-Bugs-To: \\n"
"POT-Creation-Date: ${new Date().toISOString().slice(0, 16).replace('T', ' ')}+0000\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"Language-Team: \\n"
"X-Domain: dimsop\\n"

`;
for (const e of entries.values()) {
    for (const c of e.comments) txt += `#. translators: ${c}\n`;
    txt += `#: ${e.refs.join(' ')}\n`;
    if (/%(\d+\$)?[sd]/.test(e.msgid)) txt += `#, php-format\n`;
    if (e.ctx) txt += `msgctxt ${po(e.ctx)}\n`;
    txt += `msgid ${po(e.msgid)}\n`;
    if (e.plural) txt += `msgid_plural ${po(e.plural)}\nmsgstr[0] ""\nmsgstr[1] ""\n\n`;
    else txt += `msgstr ""\n\n`;
}
fs.mkdirSync(path.dirname(out), { recursive: true });
fs.writeFileSync(out, txt);
// Lista simple para traducir: JSON con msgid (y plural)
fs.writeFileSync(process.argv[4] || out.replace(/\.pot$/, '.strings.json'), JSON.stringify([...entries.values()].map(e => e.plural ? [e.msgid, e.plural] : e.msgid), null, 1));
console.log('archivos:', files.length, 'cadenas únicas:', entries.size);
