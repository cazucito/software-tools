import { defineConfig } from 'astro/config';
import tailwind from '@astrojs/tailwind';

// https://astro.build/config
export default defineConfig({
  site: 'https://cazucito.github.io',
  base: '/software-tools',
  integrations: [tailwind()],
  output: 'static',
  markdown: {
    shikiConfig: {
      theme: 'github-dark'
    }
  }
});