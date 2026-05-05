<template>
  <div class="min-h-screen w-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-blue-900 p-4">
    <!-- Glassmorphism Card -->
    <div class="relative w-full max-w-md overflow-hidden rounded-2xl border border-white/30 bg-white/20 p-8 shadow-2xl backdrop-blur-md">
      
      <!-- Header -->
      <div class="mb-10 text-center">
        <h1 class="text-3xl font-bold text-slate-900">Selamat Datang</h1>
        <p class="mt-2 text-slate-700">Silakan login ke akun Anda</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-slate-900 mb-1">Email</label>
          <input 
            v-model="form.email"
            type="email" 
            placeholder="nama@email.com"
            class="w-full rounded-lg border border-white/40 bg-white/30 px-4 py-3 text-slate-900 placeholder-slate-500 outline-none transition focus:border-blue-800 focus:ring-1 focus:ring-blue-800"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-900 mb-1">Password</label>
          <input 
            v-model="form.password"
            type="password" 
            placeholder="••••••••"
            class="w-full rounded-lg border border-white/40 bg-white/30 px-4 py-3 text-slate-900 placeholder-slate-500 outline-none transition focus:border-blue-800 focus:ring-1 focus:ring-blue-800"
            required
          />
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center text-slate-800 cursor-pointer">
            <input type="checkbox" class="mr-2 rounded border-gray-300 text-blue-900 focus:ring-blue-800" />
            Ingat saya
          </label>
          <a href="#" class="font-semibold text-blue-900 hover:underline">Lupa password?</a>
        </div>

        <button 
          type="submit" 
          class="w-full rounded-lg bg-blue-900 py-3 font-semibold text-white shadow-lg transition hover:bg-blue-950 active:scale-[0.98]"
        >
          Masuk
        </button>
      </form>

      <!-- Footer -->
      <p class="mt-8 text-center text-sm text-slate-800">
        Belum punya akun? 
        <a href="#" class="font-bold text-blue-900 hover:underline">Daftar sekarang</a>
      </p>
    </div>

    <!-- Dekorasi Bulatan Aksesoris (Opsional) -->
    <div class="fixed -z-10 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl top-10 left-10"></div>
    <div class="fixed -z-10 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl bottom-10 right-10"></div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useAuthStore } from '../../../stores/authStore'
import { useRouter } from 'vue-router'

const form = reactive({
  email: '',
  password: ''
})

const auth = useAuthStore()
const router = useRouter()

const handleLogin = () => {
  const user = auth.login(form.email, form.password)

  if (!user) {
    alert('Login gagal')
    return
  }

  // 🔥 redirect berdasarkan role
  if (user.role === 'admin') {
    router.push('/admin/dashboard')
  } else if (user.role === 'guru') {
    router.push('/guru/dashboard')
  } else if (user.role === 'siswa') {
    router.push('/siswa/dashboard')
  }
}
</script>

<style scoped>
/* Transisi halus untuk hover effect */
input, button {
  transition: all 0.3s ease;
}
</style>