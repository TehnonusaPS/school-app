<script setup>
import { ref } from 'vue'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'
import { Eye, EyeOff } from 'lucide-vue-next'

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
    const success = await auth.login(email.value, password.value)
    if (success) {
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

      <p v-if="error" class="text-sm text-destructive font-medium">
        {{ error }}
      </p>

      <Button type="submit" class="w-full h-11 text-md font-semibold" :disabled="isLoading">
        {{ isLoading ? 'Memproses...' : 'Masuk Sekarang' }}
      </Button>
    </div>
  </form>
</template>
