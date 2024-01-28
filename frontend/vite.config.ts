import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from "url";
import dotenv from "dotenv";
dotenv.config({path: ".env"});


// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", import.meta.url)),
    },
  },  
  server:{    
    host: process.env.VITE_SERVER,
    port: Number(process.env.VITE_PORT),    
    proxy:{
      "/api": {
        target: process.env.VITE_API_SERVER,
        secure: false,
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, "")
      }
    }
  }
});

