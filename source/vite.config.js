import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  // project root that contains source files
  root: './',
  plugins: [react()],
  build: {
    // outDir is relative to the root option
    outDir: '../weave/static',
    emptyOutDir: true,
    // library mode: produce only JS bundle (no index.html)
    lib: {
      entry: 'src/main.jsx',
      name: 'WeaveApp',
      formats: ['es'],
      fileName: () => 'js/app.js'
    },
    rollupOptions: {
      // bundle React into the build by default (remove from external)
      // control asset file names (CSS/images)
      output: {
        assetFileNames: ({ name }) => {
          if (name && name.endsWith('.css')) return 'css/[name].[ext]'
          return 'assets/[name].[ext]'
        }
      }
    }
  }
})
