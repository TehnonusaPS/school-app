import path from 'node:path'
import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue(), tailwindcss()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src')
    }
  },
  build: {
    // Target diturunkan ke Safari 14 agar Lightning CSS tidak menghapus
    // prefix -webkit-backdrop-filter (dibutuhkan oleh banyak pengguna iOS)
    target: ['chrome90', 'firefox90', 'safari14', 'edge90']
  },
  css: {
    lightningcss: {
      targets: {
        chrome: (90 << 16),
        firefox: (90 << 16),
        safari: (14 << 16),
        edge: (90 << 16)
      }
    }
  }
})

