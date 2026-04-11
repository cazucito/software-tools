/**
 * Tipos para el modelo de datos de software-tools
 */

export interface Tool {
  /** Nombre de la herramienta */
  name: string;
  /** Identificador único (kebab-case) */
  slug: string;
  /** Año de primer uso */
  year: number;
  /** Década auto-generada */
  decade: string;
  /** Categoría principal */
  category: Category;
  /** Tags para clasificación */
  tags: string[];
  /** Ruta a imagen (opcional) */
  image?: string;
  /** Historia personal de uso (markdown) */
  content: string;
  /** Contexto personal breve */
  context?: string;
  /** Año de último uso */
  usedUntil?: number;
  /** Nombre de herramienta sucesora */
  successor?: string;
  /** Slug de sucesora */
  successorSlug?: string;
  /** Slugs de herramientas relacionadas */
  related?: string[];
  /** Publicar o mantener como draft */
  published: boolean;
}

export type Category = 
  | 'editor' 
  | 'lenguaje' 
  | 'database' 
  | 'infra' 
  | 'framework'
  | 'tool'
  | 'os'
  | 'other';

export interface ToolsData {
  /** Lista completa de herramientas */
  tools: Tool[];
  /** Índice de tags: tag -> {count, slugs} */
  tags: Record<string, { count: number; tools: string[] }>;
  /** Timeline por década */
  timeline: Record<string, string[]>;
  /** Índice por categoría */
  categories: Record<string, string[]>;
  /** Metadata */
  meta: {
    generatedAt: string;
    totalTools: number;
    totalPublished: number;
  };
}

export interface SearchIndex {
  /** Herramientas para búsqueda Fuse.js */
  tools: Array<{
    name: string;
    slug: string;
    year: number;
    category: string;
    tags: string[];
    context?: string;
  }>;
}