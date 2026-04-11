/**
 * Utilidades para cargar colecciones de contenido
 */
import fs from 'fs/promises';
import path from 'path';
import matter from 'gray-matter';
import type { Tool } from '../types';

const CONTENT_DIR = './src/content/tools';

function getDecade(year: number): string {
  const decade = Math.floor(year / 10) * 10;
  return `${decade}s`;
}

export async function getCollection(collectionName: string): Promise<Tool[]> {
  if (collectionName !== 'tools') {
    throw new Error(`Collection "${collectionName}" not supported`);
  }

  try {
    const files = await fs.readdir(CONTENT_DIR);
    const mdFiles = files.filter(f => f.endsWith('.md'));

    const tools: Tool[] = await Promise.all(
      mdFiles.map(async (filename) => {
        const filePath = path.join(CONTENT_DIR, filename);
        const content = await fs.readFile(filePath, 'utf-8');
        const { data, content: body } = matter(content);

        const year = Number(data.year);

        return {
          name: data.name,
          slug: data.slug || filename.replace('.md', ''),
          year,
          decade: data.decade || getDecade(year),
          category: data.category,
          tags: data.tags || [],
          image: data.image,
          content: body,
          context: data.context,
          usedUntil: data.usedUntil,
          successor: data.successor,
          successorSlug: data.successorSlug,
          related: data.related || [],
          published: data.published ?? true,
        };
      })
    );

    return tools.sort((a, b) => b.year - a.year);
  } catch (error) {
    console.warn('No tools found or error reading collection:', error);
    return [];
  }
}

export async function getToolBySlug(slug: string): Promise<Tool | null> {
  const tools = await getCollection('tools');
  return tools.find(t => t.slug === slug) || null;
}