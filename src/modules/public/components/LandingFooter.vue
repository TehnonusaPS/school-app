<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { GraduationCap } from 'lucide-vue-next'

const emit = defineEmits(['scrollTo'])
const auth = useAuthStore()
const router = useRouter()

const scrollTo = (id) => emit('scrollTo', id)
const handleLogout = () => auth.logout()

const navLinks = [
  ['fitur', 'Fitur'],
  ['solusi', 'Solusi'],
  ['tentang', 'Tentang'],
  ['kontak', 'Kontak'],
]
</script>

<template>
  <footer class="bg-blue-950 text-white py-10">
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid md:grid-cols-3 gap-8 mb-8">

        <!-- Brand -->
        <div>
          <div class="flex items-center gap-2 mb-3">
            <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
              <GraduationCap class="w-5 h-5 text-white" />
            </div>
            <div class="font-extrabold text-lg">CerdasBangsa</div>
          </div>
          <p class="text-blue-300 text-sm leading-relaxed">
            Platform manajemen pendidikan untuk yayasan. Daftarkan yayasan Anda dan kelola semua unit sekolah di bawahnya dalam satu sistem.
          </p>
        </div>

        <!-- Navigasi -->
        <div>
          <div class="font-semibold text-sm mb-3 text-blue-200">Navigasi</div>
          <div class="flex flex-col gap-2">
            <button v-for="[id, label] in navLinks" :key="id" @click="scrollTo(id)"
              class="text-left text-blue-400 text-sm hover:text-white transition-colors w-fit">
              {{ label }}
            </button>
          </div>
        </div>

        <!-- Akun -->
        <div>
          <div class="font-semibold text-sm mb-3 text-blue-200">Akun</div>
          <template v-if="!auth.user">
            <button @click="router.push('/login')" class="text-sm text-blue-400 hover:text-white transition-colors block mb-1">
              Login
            </button>
          </template>
          <template v-else>
            <button @click="router.push('/dashboard')" class="text-sm text-blue-400 hover:text-white transition-colors block mb-1">
              Dashboard
            </button>
            <button @click="handleLogout" class="text-sm text-red-400 hover:text-red-300 transition-colors block">
              Logout
            </button>
          </template>
        </div>

      </div>

      <!-- Bottom bar -->
      <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-blue-400">
        <div>&copy; {{ new Date().getFullYear() }} CerdasBangsa. Semua hak cipta dilindungi.</div>
        <div class="flex gap-4">
          <a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a>
          <a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a>
        </div>
      </div>
    </div>
  </footer>
</template>
