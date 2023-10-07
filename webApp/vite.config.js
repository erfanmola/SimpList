import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  envDir: '../backend',
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          lottie: ['vue3-lottie', 'lottie-colorify'],
        }
      }
    }
  }
});
