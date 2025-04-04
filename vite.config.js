import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0',  // This is important
    port: 3001,
    strictPort: true,
    watch: {
      usePolling: true,
    }
  }
})