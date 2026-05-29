<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { GraduationCap, Eye, EyeOff } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { useAuthStore } from '@/stores/authStore'

const auth = useAuthStore()
const router = useRouter()

// 🔥 Akun Demo Quick Fill (boleh dihapus)
const demoAccounts = [
  { role: 'Super Admin', email: 'superadmin@mail.com' },
  { role: 'Admin Yayasan', email: 'adminyayasan@mail.com' },
  { role: 'Kepala Sekolah', email: 'kepalasekolah@mail.com' },
  { role: 'Admin Sekolah', email: 'adminsekolah@mail.com' },
  { role: 'Tata Usaha', email: 'tatausaha@mail.com' },
  { role: 'Guru Pengajar', email: 'guru@mail.com' },
  { role: 'Wali Kelas', email: 'walikelas@mail.com' },
  { role: 'Siswa', email: 'siswa@mail.com' },
  { role: 'Orang Tua / Wali', email: 'orangtua@mail.com' }
]

const isDemoDropdownOpen = ref(false)
const selectedDemoRole = ref('')

const toggleDemoDropdown = () => {
  isDemoDropdownOpen.value = !isDemoDropdownOpen.value
}

const selectDemoAccount = (acc) => {
  email.value = acc.email
  password.value = '123456'
  selectedDemoRole.value = acc.role
  isDemoDropdownOpen.value = false
}

const email = ref('')
const password = ref('')
const showPassword = ref(false)
const isLoading = ref(false)
const error = ref('')

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const handleLogin = async () => {
  isLoading.value = true
  error.value = ''

  try {
    const user = await auth.login(email.value, password.value)
    if (user) {
      toast.success(`Selamat datang, ${user.role}!`, { cancel: { label: 'Tutup', onClick: () => {} } })
      router.push('/dashboard')
    } else {
      error.value = 'Email atau password salah.'
    }
  } catch (e) {
    error.value = 'Terjadi kesalahan saat login.'
  } finally {
    isLoading.value = false
  }
}

// Close dropdown if clicked outside
let handleOutsideClick = null

onMounted(() => {
  // Paksa halaman login selalu dalam light mode
  document.documentElement.classList.remove('dark')

  // Bersihkan class tema/background yang mungkin terbawa dari halaman lain
  document.body.classList.forEach(cls => {
    if (cls.startsWith('theme-') || ['bg-animated', 'bg-static_squares', 'bg-glass', 'bg-solid'].includes(cls)) {
      document.body.classList.remove(cls)
    }
  })

  // Pastikan tema bawaan (emerald/green) diterapkan
  document.body.classList.add('theme-emerald')

  handleOutsideClick = (e) => {
    const dropdownContainer = document.getElementById('demo-dropdown-container')
    if (dropdownContainer && !dropdownContainer.contains(e.target)) {
      isDemoDropdownOpen.value = false
    }
  }
  document.addEventListener('click', handleOutsideClick)
})

onUnmounted(() => {
  if (handleOutsideClick) {
    document.removeEventListener('click', handleOutsideClick)
  }
})
</script>

<template>
  <div class="grid min-h-screen w-full overflow-x-hidden lg:grid-cols-[450px_1fr] xl:grid-cols-[500px_1fr]">
    <!-- Left panel (Glassmorphism on mobile/tablet, clean minimal on desktop) -->
    <div class="flex flex-col gap-4 p-4 sm:p-6 md:p-10 bg-background relative min-w-0 overflow-hidden justify-between">
      <!-- Mobile/Tablet Background Image Overlay -->
      <div class="absolute inset-0 lg:hidden overflow-hidden z-0">
        <img
          src="@/assets/images/login-bg.png"
          alt="Suasana Sekolah Indonesia"
          class="absolute inset-0 h-full w-full object-cover"
        />
        <div class="absolute inset-0 bg-emerald-950/85 backdrop-blur-[3px]"></div>
      </div>

      <!-- Header Logo -->
      <div class="flex justify-center gap-2 md:justify-start z-10">
        <a href="#" class="flex items-center gap-2 font-bold text-xl sm:text-2xl text-white lg:text-primary min-w-0 transition-colors">
          <div
            class="bg-white/10 border border-white/20 lg:bg-primary lg:border-none text-white lg:text-primary-foreground flex size-8 sm:size-10 shrink-0 items-center justify-center rounded-lg shadow-lg"
          >
            <GraduationCap class="size-5 sm:size-6" />
          </div>
          <span class="truncate">CerdasBangsa</span>
        </a>
      </div>

      <!-- Main Login Card / Form Container -->
      <div class="flex flex-1 items-center justify-center min-w-0 py-6 sm:py-8 z-20">
        <div class="w-full max-w-sm p-6 sm:p-8 rounded-2xl border border-white/10 bg-white/10 backdrop-blur-md shadow-2xl lg:border-none lg:bg-transparent lg:backdrop-blur-none lg:shadow-none lg:p-0 transition-all duration-300">
          <form @submit.prevent="handleLogin" class="flex flex-col gap-5 sm:gap-6">
            <div class="flex flex-col items-center gap-2 text-center px-1 sm:px-2">
              <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white lg:text-foreground">Sistem Informasi Sekolah</h1>
              <p class="text-emerald-100/80 lg:text-muted-foreground text-xs sm:text-sm">
                Silakan masukkan akun Anda untuk mengelola data sekolah.
              </p>
            </div>

            <div class="grid gap-4">
              <div class="grid gap-2 text-left">
                <label
                  for="email"
                  class="text-sm font-medium leading-none text-white lg:text-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  >Email</label
                >
                <Input
                  id="email"
                  type="email"
                  v-model="email"
                  placeholder="Masukkan Email Anda"
                  required
                  class="bg-white/95 text-slate-900 border-white/20 focus:ring-emerald-500 lg:bg-background lg:text-foreground"
                />
              </div>
              <div class="grid gap-2 text-left">
                <div class="flex flex-wrap items-center justify-between gap-2">
                  <label
                    for="password"
                    class="text-sm font-medium leading-none text-white lg:text-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                    >Password</label
                  >
                  <a href="#" class="text-xs sm:text-sm text-emerald-200 hover:text-white lg:text-primary lg:hover:underline shrink-0">
                    Lupa sandi?
                  </a>
                </div>
                <div class="relative">
                  <Input
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    placeholder="Masukkan Password Anda"
                    v-model="password"
                    required
                    class="pr-10 bg-white/95 text-slate-900 border-white/20 focus:ring-emerald-500 lg:bg-background lg:text-foreground"
                  />
                  <button
                    type="button"
                    @click="togglePassword"
                    class="absolute cursor-pointer inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-800 lg:text-muted-foreground lg:hover:text-foreground transition-colors shrink-0"
                  >
                    <Eye v-if="!showPassword" class="size-4" />
                    <EyeOff v-else class="size-4" />
                  </button>
                </div>
              </div>

              <!-- Akun Demo Quick Fill -->
              <div id="demo-dropdown-container" class="grid gap-2 text-left relative">
                <label class="text-sm font-medium leading-none text-emerald-200/90 lg:text-muted-foreground">
                  Uji Coba dengan Akun Demo
                </label>
                <div class="relative">
                  <button
                    type="button"
                    @click="toggleDemoDropdown"
                    class="flex h-10 w-full items-center justify-between rounded-md border border-white/10 bg-white/95 text-slate-900 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:cursor-not-allowed disabled:opacity-50 lg:border-input lg:bg-background lg:text-foreground transition-all cursor-pointer shadow-sm"
                  >
                    <span class="truncate font-medium">{{ selectedDemoRole || '-- Pilih Akun Demo --' }}</span>
                    <span class="text-slate-500 pointer-events-none ml-2 text-xs">▼</span>
                  </button>
                  
                  <!-- Custom Dropdown Options Menu -->
                  <div
                    v-if="isDemoDropdownOpen"
                    class="absolute z-50 mt-1 max-h-60 w-full overflow-y-auto rounded-md border border-slate-200/80 bg-white shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none"
                  >
                    <div class="py-1">
                      <button
                        type="button"
                        v-for="acc in demoAccounts"
                        :key="acc.email"
                        @click="selectDemoAccount(acc)"
                        class="flex w-full items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-900 font-semibold transition-colors text-left cursor-pointer border-b border-slate-100 last:border-0"
                      >
                        {{ acc.role }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <p v-if="error" class="text-sm text-red-200 lg:text-destructive font-medium break-words">
                {{ error }}
              </p>

              <Button type="submit" class="w-full h-10 sm:h-11 text-sm sm:text-md font-semibold mt-2 bg-emerald-600 hover:bg-emerald-500 text-white lg:bg-primary lg:hover:bg-primary/95 lg:text-primary-foreground" :disabled="isLoading">
                {{ isLoading ? 'Memproses...' : 'Masuk Sekarang' }}
              </Button>
            </div>
          </form>
        </div>
      </div>

      <!-- Footer Copyright -->
      <div class="text-center text-[10px] sm:text-xs text-emerald-100/60 lg:text-muted-foreground mt-auto px-4 z-10">
        &copy; {{ new Date().getFullYear() }} CerdasBangsa - Sistem Manajemen Sekolah Terintegrasi
      </div>
    </div>

    <!-- Right panel for laptop/desktop screen (hidden on mobile/tablet) -->
    <div class="bg-muted relative hidden lg:block border-l overflow-hidden min-w-0">
      <!-- Menggunakan gambar sekolah Indonesia yang hangat dan familiar -->
      <img
        src="@/assets/images/login-bg.png"
        alt="Suasana Sekolah Indonesia"
        class="absolute inset-0 h-full w-full object-cover"
      />
      <div
        class="absolute inset-0 bg-gradient-to-t from-emerald-950/90 via-emerald-900/40 to-transparent flex items-end p-8 xl:p-12"
      >
        <div class="text-white max-w-lg min-w-0">
          <h2 class="text-3xl xl:text-4xl font-bold leading-tight drop-shadow-md">Membangun Masa Depan Melalui Teknologi</h2>
          <p class="mt-4 text-lg xl:text-xl text-white/90 drop-shadow-sm">
            Platform manajemen sekolah modern yang dirancang khusus untuk pendidikan di Indonesia.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
