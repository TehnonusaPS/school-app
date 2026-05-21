<script setup>
import { School, MapPin, Phone, Mail, ChevronRight } from 'lucide-vue-next'

defineProps({
  brandName: {
    type: String,
    default: 'Sekolahku'
  },
  year: {
    type: [String, Number],
    default: new Date().getFullYear()
  }
})
</script>

<template>
  <footer id="kontak" class="relative overflow-hidden bg-emerald-950 pt-20 pb-8">
    <!-- Decorative Background Elements -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#10b98110_1px,transparent_1px),linear-gradient(to_bottom,#10b98110_1px,transparent_1px)] bg-[size:24px_24px]"></div>
    <div class="absolute left-0 top-0 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-emerald-500/20 rounded-full blur-[100px]"></div>
    <div class="absolute right-0 bottom-0 translate-x-1/3 translate-y-1/3 w-96 h-96 bg-emerald-400/10 rounded-full blur-[100px]"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
        <!-- Kolom 1: Brand & Deskripsi -->
        <div class="space-y-6">
          <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-lg shadow-emerald-900/50 p-2">
              <School class="h-8 w-8" />
            </div>
            <div class="font-bold text-xl tracking-tight leading-tight text-white">
              {{ brandName }}<br/><span class="text-emerald-400">App</span>
            </div>
          </div>
          <p class="text-emerald-100/70 text-sm leading-relaxed max-w-sm font-medium">
            Platform manajemen sekolah modern yang mengintegrasikan seluruh kegiatan akademik, administrasi, dan komunikasi dalam satu pintu secara profesional.
          </p>
        </div>

        <!-- Kolom 2: Tautan Cepat -->
        <div>
          <h3 class="text-white font-bold mb-8 uppercase tracking-widest text-sm">Konten</h3>
          <ul class="space-y-4">
            <li v-for="(link, index) in [
              { label: 'Fitur', href: '#fitur' },
              { label: 'Modul', href: '#modul' },
              { label: 'Harga', href: '#harga' },
              { label: 'Testimoni', href: '#testimoni' }
            ]" :key="index">
              <a :href="link.href" class="text-emerald-100/70 hover:text-emerald-300 transition-colors text-sm font-medium flex items-center gap-2 group">
                <ChevronRight class="w-4 h-4 text-emerald-500 group-hover:text-emerald-300 group-hover:translate-x-1 transition-all" />
                {{ link.label }}
              </a>
            </li>
          </ul>
        </div>

        <!-- Kolom 3: Layanan / Modul -->
        <div>
          <h3 class="text-white font-bold mb-8 uppercase tracking-widest text-sm">Modul Utama</h3>
          <ul class="space-y-4">
            <li v-for="(modul, index) in ['Sistem Akademik', 'Manajemen Keuangan', 'Penerimaan Siswa', 'Portal Pegawai']" :key="index" class="text-emerald-100/70 text-sm font-medium flex items-center gap-2">
              <ChevronRight class="w-4 h-4 text-emerald-500" />
              {{ modul }}
            </li>
          </ul>
        </div>

        <!-- Kolom 4: Hubungi Kami -->
        <div>
          <h3 class="text-white font-bold mb-8 uppercase tracking-widest text-sm">Hubungi Kami</h3>
          <ul class="space-y-5">
            <li class="flex items-start gap-3 group">
              <div class="w-8 h-8 rounded-lg bg-emerald-800/50 border border-emerald-700/50 flex items-center justify-center shrink-0 group-hover:bg-emerald-500/20 transition-colors">
                <MapPin class="w-4 h-4 text-emerald-400" />
              </div>
              <p class="text-emerald-100/70 text-[13px] font-medium leading-relaxed">
                Jl. Pendidikan No. 123<br/>Jakarta Selatan, Indonesia
              </p>
            </li>
            <li class="flex items-center gap-3 group">
              <div class="w-8 h-8 rounded-lg bg-emerald-800/50 border border-emerald-700/50 flex items-center justify-center shrink-0 group-hover:bg-emerald-500/20 transition-colors">
                <Phone class="w-4 h-4 text-emerald-400" />
              </div>
              <a href="tel:02112345678" class="text-emerald-100/70 hover:text-emerald-300 transition-colors text-[13px] font-medium">021-1234 5678</a>
            </li>
            <li class="flex items-center gap-3 group">
              <div class="w-8 h-8 rounded-lg bg-emerald-800/50 border border-emerald-700/50 flex items-center justify-center shrink-0 group-hover:bg-emerald-500/20 transition-colors">
                <Mail class="w-4 h-4 text-emerald-400" />
              </div>
              <a href="mailto:halo@sekolahku.id" class="text-emerald-100/70 hover:text-emerald-300 transition-colors text-[13px] font-medium">halo@sekolahku.id</a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="pt-8 border-t border-emerald-800 text-emerald-100/50 text-xs flex flex-col md:flex-row justify-between items-center gap-4">
        <p>&copy; {{ year }} <span class="text-emerald-400 font-medium">{{ brandName }}</span>. Hak Cipta Dilindungi.</p>
        <div class="flex gap-6">
          <a href="#" class="hover:text-emerald-300 transition-colors">Kebijakan Privasi</a>
          <a href="#" class="hover:text-emerald-300 transition-colors">Syarat & Ketentuan</a>
        </div>
      </div>
    </div>
  </footer>
</template>
