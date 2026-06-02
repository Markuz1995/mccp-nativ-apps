import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0',     // Escuchar en todas las interfaces (necesario en Docker)
    port: 5173,
    watch: {
      usePolling: true,  // Required for hot reload in Docker with volumes
      interval: 1000,
    },
    proxy: {
      // En desarrollo, /api se proxea al backend via nginx
      '/api': {
        target: 'http://nginx:80',
        changeOrigin: true,
        secure: false,
      },
    },
  },
})
