#!/usr/bin/env node
/**
 * Script para generar índice de búsqueda para Fuse.js
 * Lee /src/data/tools.json y genera /public/search-index.json
 */

import fs from 'fs/promises';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT_DIR = path.join(__dirname, '..');
const DATA_FILE = path.join(ROOT_DIR, 'src', 'data', 'tools.json');
const OUTPUT_FILE = path.join(ROOT_DIR, 'public', 'search-index.json');

async function ensureDir(dir) {
  try {
    await fs.access(dir);
  } catch {
    await fs.mkdir(dir, { recursive: true });
  }
}

async function generateSearchIndex() {
  console.log('🔍 Generando índice de búsqueda...\n');

  try {
    const data = await fs.readFile(DATA_FILE, 'utf-8');
    const { tools } = JSON.parse(data);

    // Crear índice optimizado para Fuse.js
    const searchIndex = tools
      .filter(tool => tool.published)
      .map(tool => ({
        name: tool.name,
        slug: tool.slug,
        year: tool.year,
        decade: tool.decade,
        category: tool.category,
        tags: tool.tags,
        context: tool.context || '',
      }));

    await ensureDir(path.dirname(OUTPUT_FILE));
    await fs.writeFile(OUTPUT_FILE, JSON.stringify(searchIndex, null, 2));

    console.log('✅ Índice de búsqueda generado');
    console.log(`   📊 ${searchIndex.length} herramientas indexadas`);
    console.log(`   💾 Guardado en: ${path.relative(ROOT_DIR, OUTPUT_FILE)}`);
  } catch (error) {
    console.error('❌ Error generando índice:', error.message);
    process.exit(1);
  }
}

generateSearchIndex();