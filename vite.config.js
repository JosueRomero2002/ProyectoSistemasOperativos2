import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    host: true,  // This is important
    port: 3001,
    //strictPort: true,
    //watch: {
      //usePolling: true,
   // }
   origin: 'http://elkyn.org',
   allowedHosts: ['.org', 'localhost', 'elkyn.org']
  }
})