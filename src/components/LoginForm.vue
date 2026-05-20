<script setup>
import { ref } from 'vue'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'
import { Eye, EyeOff } from 'lucide-vue-next'
import { toast } from 'vue-sonner'

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
    const success = await auth.login(email.value, password.value)
    if (success) {
      toast.success(`Login Berhasil! Selamat datang kembali.`)
      router.push('/dashboard')
    } else {
      error.value = 'Email atau password salah.'
      toast.error('Login Gagal! Email atau password salah.')
    }
  } catch (e) {
    error.value = 'Terjadi kesalahan saat login.'
    toast.error('Login Gagal! Terjadi kesalahan sistem.')
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <form @submit.prevent="handleLogin" :class="cn('flex flex-col gap-6')">
    <div class="flex flex-col items-center gap-2 text-center">
      <h1 class="text-2xl font-bold tracking-tight">Sistem Informasi Sekolah</h1>
      <p class="text-muted-foreground text-sm">
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
        <Input id="email" type="email" v-model="email" placeholder="Masukkan Email Anda" required />
      </div>
      <div class="grid gap-2 text-left">
        <div class="flex items-center">
          <label
            for="password"
            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
            >Password</label
          >
          <a href="#" class="ml-auto text-sm underline-offset-4 hover:underline"> Lupa sandi? </a>
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
            class="absolute cursor-pointer inset-y-0 right-0 pr-3 flex items-center text-muted-foreground hover:text-foreground transition-colors"
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

      <p v-if="error" class="text-sm text-destructive font-medium">
        {{ error }}
      </p>

      <Button type="submit" class="w-full h-11 text-md font-semibold" :disabled="isLoading">
        {{ isLoading ? 'Memproses...' : 'Masuk Sekarang' }}
      </Button>
    </div>
  </form>
</template>
