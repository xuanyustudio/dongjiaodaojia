import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
    extensions: ['.mjs', '.js', '.mts', '.ts', '.jsx', '.tsx', '.json', '.vue']
  },
  optimizeDeps: {
    entries: ['index.html']
  },
  build: {
    rollupOptions: {
      shimMissingExports: true
    }
  },
  server: {
    host: '127.0.0.1',
    port: 5173,
    proxy: {
      '/public': {
        target: 'http://127.0.0.1:8088',
        changeOrigin: true
      }
    }
  }
})
