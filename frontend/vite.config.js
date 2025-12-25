import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  // project root that contains index.html
  root: './',
  plugins: [react()],
  server: {
    port: 8080,
    host: true,
    cors: true
  },
  build: {
    // outDir is relative to the root option
    outDir: 'weave/static',
    emptyOutDir: true,
    minify: false,
    rollupOptions: {
      input: 'assets/js/main.jsx',
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name].js',
        assetFileNames: ({ name }) => {
          if (name && name.endsWith('.css')) return 'css/[name].[ext]';
          return 'assets/[name].[ext]';
        }
      }
    }
  }
});
