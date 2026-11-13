// Genera <locale>.po y <locale>.mo a partir del .pot y un JSON {msgid: traducción | [singular, plural]}
// Uso: node make-po.js <dimsop.pot> <traducciones.json> <locale> <dir-salida>
const fs = require('fs');
const path = require('path');

const [potFile, jsonFile, locale, outDir] = process.argv.slice(2);
const tr = JSON.parse(fs.readFileSync(jsonFile, 'utf8'));
const pot = fs.readFileSync(potFile, 'utf8');

function unq(s) {
    return s.slice(1, -1).replace(/\\(.)/g, (m, c) => ({ n: '\n', t: '\t', '"': '"', '\\': '\\' })[c] ?? m);
}
function q(s) {
    return '"' + s.replace(/\\/g, '\\\\').replace(/"/g, '\\"').replace(/\n/g, '\\n').replace(/\t/g, '\\t') + '"';
}
const ph = (s) => (s.match(/%(\d+\$)?[sd]/g) || []).sort().join(',');

// Parseo simple del .pot: bloques separados por línea en blanco
const blocks = pot.trim().split(/\n\s*\n/);
const header = blocks.shift();
const entries = [];
const missing = [], badPh = [];
for (const b of blocks) {
    const lines = b.split('\n');
    const e = { comments: [], ctx: null, id: null, plural: null };
    for (const l of lines) {
        if (l.startsWith('#')) e.comments.push(l);
        else if (l.startsWith('msgctxt ')) e.ctx = unq(l.slice(8));
        else if (l.startsWith('msgid_plural ')) e.plural = unq(l.slice(13));
        else if (l.startsWith('msgid ')) e.id = unq(l.slice(6));
    }
    const t = tr[e.id];
    if (t === undefined) missing.push(e.id);
    e.str = t === undefined ? (e.plural ? ['', ''] : '') : t;
    const check = Array.isArray(e.str) ? e.str : [e.str];
    for (const s of check) if (s && ph(s) !== ph(e.id)) badPh.push(e.id + ' → ' + s);
    entries.push(e);
}
const unused = Object.keys(tr).filter(k => !entries.some(e => e.id === k));
if (missing.length) { console.error('FALTAN:', missing); }
if (badPh.length) { console.error('MARCADORES DISTINTOS:', badPh); }
if (unused.length) { console.error('SOBRAN (no están en el .pot):', unused); }
if (missing.length || badPh.length) process.exit(1);

// .po
const langName = locale.split('_')[0];
let po = header
    .replace('"Language-Team: \\n"', `"Language-Team: \\n"\n"Language: ${locale}\\n"\n"Plural-Forms: nplurals=2; plural=(n != 1);\\n"\n"PO-Revision-Date: ${new Date().toISOString().slice(0, 16).replace('T', ' ')}+0000\\n"`)
    .replace(/^# Copyright.*\n# This file.*$/m, `# ${langName === 'en' ? 'English' : locale} translation of the Dimsop theme.\n# This file is distributed under the same license as the Dimsop theme.`) + '\n\n';
for (const e of entries) {
    po += e.comments.join('\n') + '\n';
    if (e.ctx) po += `msgctxt ${q(e.ctx)}\n`;
    po += `msgid ${q(e.id)}\n`;
    if (e.plural) {
        po += `msgid_plural ${q(e.plural)}\n`;
        e.str.forEach((s, i) => { po += `msgstr[${i}] ${q(s)}\n`; });
    } else po += `msgstr ${q(e.str)}\n`;
    po += '\n';
}
fs.writeFileSync(path.join(outDir, locale + '.po'), po);

// .mo (formato GNU gettext, little endian)
const headerStr = `Project-Id-Version: Dimsop\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit\nLanguage: ${locale}\nPlural-Forms: nplurals=2; plural=(n != 1);\nX-Domain: dimsop\n`;
const pairs = [['', headerStr]];
for (const e of entries) {
    const key = (e.ctx ? e.ctx + '\u0004' : '') + e.id + (e.plural ? '\u0000' + e.plural : '');
    const val = Array.isArray(e.str) ? e.str.join('\u0000') : e.str;
    if (val) pairs.push([key, val]);
}
pairs.sort((a, b) => Buffer.compare(Buffer.from(a[0]), Buffer.from(b[0])));
const N = pairs.length;
const ids = pairs.map(p => Buffer.from(p[0], 'utf8'));
const strs = pairs.map(p => Buffer.from(p[1], 'utf8'));
const headSize = 28, tableSize = N * 8;
let offset = headSize + tableSize * 2;
const buf = [];
const head = Buffer.alloc(headSize);
head.writeUInt32LE(0x950412de, 0); head.writeUInt32LE(0, 4); head.writeUInt32LE(N, 8);
head.writeUInt32LE(headSize, 12); head.writeUInt32LE(headSize + tableSize, 16); head.writeUInt32LE(0, 20); head.writeUInt32LE(headSize + tableSize * 2, 24); // tabla hash vacía justo tras las tablas
const idTable = Buffer.alloc(tableSize), strTable = Buffer.alloc(tableSize);
const data = [];
ids.forEach((b, i) => { idTable.writeUInt32LE(b.length, i * 8); idTable.writeUInt32LE(offset, i * 8 + 4); data.push(b, Buffer.from([0])); offset += b.length + 1; });
strs.forEach((b, i) => { strTable.writeUInt32LE(b.length, i * 8); strTable.writeUInt32LE(offset, i * 8 + 4); data.push(b, Buffer.from([0])); offset += b.length + 1; });
fs.writeFileSync(path.join(outDir, locale + '.mo'), Buffer.concat([head, idTable, strTable, ...data]));
console.log(`${locale}: ${entries.length} cadenas, ${N - 1} traducidas en el .mo`);
