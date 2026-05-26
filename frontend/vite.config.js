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
    // Target browser modern yang support backdrop-filter tanpa prefix
    // Ini mencegah Lightning CSS menghapus backdrop-filter standar
    target: ['chrome111', 'firefox113', 'safari16', 'edge111']
  },
  css: {
    // Pastikan kedua versi (prefixed & unprefixed) tetap ada di output
    lightningcss: {
      targets: {
        chrome: (111 << 16),
        firefox: (113 << 16),
        safari: (16 << 16),
        edge: (111 << 16)
      }
    }
  }
})

