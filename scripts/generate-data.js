#!/usr/bin/env node
/**
 * Script para generar datos consolidados desde archivos Markdown
 * Lee /src/content/tools/*.md y genera /src/data/tools.json
 */

import fs from 'fs/promises';
import path from 'path';
import { fileURLToPath } from 'url';
import matter from 'gray-matter';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT_DIR = path.join(__dirname, '..');
const CONTENT_DIR = path.join(ROOT_DIR, 'src', 'content', 'tools');
const DATA_DIR = path.join(ROOT_DIR, 'src', 'data');
const OUTPUT_FILE = path.join(DATA_DIR, 'tools.json');

function getDecade(year) {
  const decade = Math.floor(year / 10) * 10;
  return `${decade}s`;
}

async function ensureDir(dir) {
  try {
    await fs.access(dir);
  } catch {
    await fs.mkdir(dir, { recursive: true });
  }
}

async function generateData() {
  console.log('🔧 Generando datos desde Markdown...\n');

  await ensureDir(DATA_DIR);

  let files;
  try {
    files = await fs.readdir(CONTENT_DIR);
  } catch (error) {
    console.log('⚠️  No se encontró directorio de herramientas, creando estructura vacía');
    await ensureDir(CONTENT_DIR);
    files = [];
  }

  const mdFiles = files.filter(f => f.endsWith('.md'));
  console.log(`📄 Encontrados ${mdFiles.length} archivos Markdown`);

  const tools = [];
  const tagsIndex = {};
  const timeline = {};
  const categories = {};

  for (const filename of mdFiles) {
    const filePath = path.join(CONTENT_DIR, filename);
    const content = await fs.readFile(filePath, 'utf-8');
    const { data } = matter(content);

    // Validación básica
    if (!data.name || !data.year || !data.category) {
      console.warn(`⚠️  ${filename}: Faltan campos requeridos (name, year, category)`);
      continue;
    }

    const year = Number(data.year);
    const decade = data.decade || getDecade(year);
    const slug = data.slug || filename.replace('.md', '');

    const tool = {
      name: data.name,
      slug,
      year,
      decade,
      category: data.category,
      tags: data.tags || [],
      image: data.image || null,
      context: data.context || null,
      usedUntil: data.usedUntil || null,
      successor: data.successor || null,
      successorSlug: data.successorSlug || null,
      related: data.related || [],
      published: data.published !== false,
    };

    tools.push(tool);

    // Indexar tags
    for (const tag of tool.tags) {
      if (!tagsIndex[tag]) {
        tagsIndex[tag] = { count: 0, tools: [] };
      }
      tagsIndex[tag].count++;
      tagsIndex[tag].tools.push(slug);
    }

    // Indexar timeline
    if (!timeline[decade]) {
      timeline[decade] = [];
    }
    timeline[decade].push(slug);

    // Indexar categorías
    if (!categories[tool.category]) {
      categories[tool.category] = [];
    }
    categories[tool.category].push(slug);
  }

  // Ordenar tools por año descendente
  tools.sort((a, b) => b.year - a.year);

  // Ordenar timeline
  for (const decade of Object.keys(timeline)) {
    timeline[decade].sort();
  }

  const data = {
    tools,
    tags: tagsIndex,
    timeline,
    categories,
    meta: {
      generatedAt: new Date().toISOString(),
      totalTools: tools.length,
      totalPublished: tools.filter(t => t.published).length,
    },
  };

  await fs.writeFile(OUTPUT_FILE, JSON.stringify(data, null, 2));

  console.log('\n✅ Datos generados exitosamente');
  console.log(`   📊 Total herramientas: ${data.meta.totalTools}`);
  console.log(`   📊 Publicadas: ${data.meta.totalPublished}`);
  console.log(`   🏷️  Tags únicos: ${Object.keys(tagsIndex).length}`);
  console.log(`   📅 Décadas: ${Object.keys(timeline).sort().join(', ')}`);
  console.log(`   📁 Categorías: ${Object.keys(categories).join(', ')}`);
  console.log(`   💾 Guardado en: ${path.relative(ROOT_DIR, OUTPUT_FILE)}`);
}

generateData().catch(error => {
  console.error('❌ Error generando datos:', error);
  process.exit(1);
});