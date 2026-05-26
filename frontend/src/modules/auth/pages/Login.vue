<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
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

const selectDemoAccount = (acc) => {
  email.value = acc.email
  password.value = '123456'
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
})
</script>

<template>
  <div class="grid min-h-screen w-full overflow-x-hidden lg:grid-cols-[450px_1fr] xl:grid-cols-[500px_1fr]">
    <div class="flex flex-col gap-4 p-4 sm:p-6 md:p-10 bg-background min-w-0">
      <div class="flex justify-center gap-2 md:justify-start">
        <a href="#" class="flex items-center gap-2 font-bold text-xl sm:text-2xl text-primary min-w-0">
          <div
            class="bg-primary text-primary-foreground flex size-8 sm:size-10 shrink-0 items-center justify-center rounded-lg shadow-lg"
          >
            <GraduationCap class="size-5 sm:size-6" />
          </div>
          <span class="truncate">CerdasBangsa</span>
        </a>
      </div>
      <div class="flex flex-1 items-center justify-center min-w-0 py-8">
        <div class="w-full max-w-sm">
          <form @submit.prevent="handleLogin" class="flex flex-col gap-5 sm:gap-6">
            <div class="flex flex-col items-center gap-2 text-center px-2">
              <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Sistem Informasi Sekolah</h1>
              <p class="text-muted-foreground text-xs sm:text-sm">
                Silakan masukkan akun Anda untuk mengelola data sekolah.
              </p>
            </div>

            <div class="grid gap-4">
              <div class="grid gap-2 text-left">
                <label
                  for="email"
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  >Email</label
                >
                <Input
                  id="email"
                  type="email"
                  v-model="email"
                  placeholder="Masukkan Email Anda"
                  required
                />
              </div>
              <div class="grid gap-2 text-left">
                <div class="flex flex-wrap items-center justify-between gap-2">
                  <label
                    for="password"
                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                    >Password</label
                  >
                  <a href="#" class="text-xs sm:text-sm underline-offset-4 hover:underline shrink-0">
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
                    class="pr-10"
                  />
                  <button
                    type="button"
                    @click="togglePassword"
                    class="absolute cursor-pointer inset-y-0 right-0 pr-3 flex items-center text-muted-foreground hover:text-foreground transition-colors shrink-0"
                  >
                    <Eye v-if="!showPassword" class="size-4" />
                    <EyeOff v-else class="size-4" />
                  </button>
                </div>
              </div>

              <!-- Akun Demo Quick Fill (boleh dihapus) -->
              <div class="grid gap-2 text-left">
                <label for="demo-account" class="text-sm font-medium leading-none text-muted-foreground">
                  Uji Coba dengan Akun Demo
                </label>
                <select
                  id="demo-account"
                  @change="(e) => {
                    const acc = demoAccounts.find(a => a.email === e.target.value);
                    if (acc) selectDemoAccount(acc);
                  }"
                  class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                >
                  <option value="" disabled selected>-- Pilih Akun Demo --</option>
                  <option v-for="acc in demoAccounts" :key="acc.email" :value="acc.email">
                    {{ acc.role }}
                  </option>
                </select>
              </div>

              <p v-if="error" class="text-sm text-destructive font-medium break-words">
                {{ error }}
              </p>

              <Button type="submit" class="w-full h-10 sm:h-11 text-sm sm:text-md font-semibold mt-2" :disabled="isLoading">
                {{ isLoading ? 'Memproses...' : 'Masuk Sekarang' }}
              </Button>
            </div>
          </form>
        </div>
      </div>
      <div class="text-center text-[10px] sm:text-xs text-muted-foreground mt-auto px-4">
        &copy; {{ new Date().getFullYear() }} CerdasBangsa - Sistem Manajemen Sekolah Terintegrasi
      </div>
    </div>
    <div class="bg-muted relative hidden lg:block border-l overflow-hidden min-w-0">
      <!-- Menggunakan gambar sekolah Indonesia yang hangat dan familiar -->
      <img
        src="@/assets/images/login-bg.png"
        alt="Suasana Sekolah Indonesia"
        class="absolute inset-0 h-full w-full object-cover"
      />
      <div
        class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/40 to-transparent flex items-end p-8 xl:p-12"
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
