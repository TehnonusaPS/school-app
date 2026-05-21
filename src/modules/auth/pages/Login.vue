<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { GalleryVerticalEnd, GraduationCap, Eye, EyeOff } from 'lucide-vue-next'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { useAuthStore } from '@/stores/authStore'

const auth = useAuthStore()
const router = useRouter()
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
</script>

<template>
  <div class="grid min-h-svh lg:grid-cols-[35%_1fr]">
    <div class="flex flex-col gap-4 p-6 md:p-10 bg-background">
      <div class="flex justify-center gap-2 md:justify-start">
        <a href="#" class="flex items-center gap-2 font-bold text-2xl text-primary">
          <div
            class="bg-primary text-primary-foreground flex size-10 items-center justify-center rounded-lg shadow-lg"
          >
            <GraduationCap class="size-6" />
          </div>
          CerdasBangsa
        </a>
      </div>
      <div class="flex flex-1 items-center justify-center">
        <div class="w-full max-w-sm">
          <form @submit.prevent="handleLogin" class="flex flex-col gap-6">
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
                <Input
                  id="email"
                  type="email"
                  v-model="email"
                  placeholder="Masukkan Email Anda"
                  required
                />
              </div>
              <div class="grid gap-2 text-left">
                <div class="flex items-center">
                  <label
                    for="password"
                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                    >Password</label
                  >
                  <a href="#" class="ml-auto text-sm underline-offset-4 hover:underline">
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
                    class="absolute cursor-pointer inset-y-0 right-0 pr-3 flex items-center text-muted-foreground hover:text-foreground transition-colors"
                  >
                    <Eye v-if="!showPassword" class="size-4" />
                    <EyeOff v-else class="size-4" />
                  </button>
                </div>
              </div>

              <p v-if="error" class="text-sm text-destructive font-medium">
                {{ error }}
              </p>

              <Button type="submit" class="w-full h-11 text-md font-semibold" :disabled="isLoading">
                {{ isLoading ? 'Memproses...' : 'Masuk Sekarang' }}
              </Button>
            </div>
          </form>
        </div>
      </div>
      <div class="text-center text-xs text-muted-foreground">
        &copy; {{ new Date().getFullYear() }} CerdasBangsa - Sistem Manajemen Sekolah Terintegrasi
      </div>
    </div>
    <div class="bg-muted relative hidden lg:block border-l overflow-hidden">
      <!-- Menggunakan gambar sekolah Indonesia yang hangat dan familiar -->
      <img
        src="@/assets/images/login-bg.png"
        alt="Suasana Sekolah Indonesia"
        class="absolute inset-0 h-full w-full object-cover"
      />
      <div
        class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent flex items-end p-12"
      >
        <div class="text-white max-w-lg">
          <h2 class="text-4xl font-bold leading-tight">Membangun Masa Depan Melalui Teknologi</h2>
          <p class="mt-4 text-xl text-white/90">
            Platform manajemen sekolah modern yang dirancang khusus untuk pendidikan di Indonesia.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
