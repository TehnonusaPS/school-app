<script setup>
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'
import { Button } from '@/components/ui/button'
import { LogOut, LayoutDashboard, User } from 'lucide-vue-next'

const auth = useAuthStore()
const router = useRouter()

const handleLogout = () => {
  auth.logout()
  // Tetap di landing page, UI akan reaktif berubah jadi tombol Login
}
</script>

<template>
  <div class="min-h-screen bg-background flex flex-col items-center justify-center p-4 relative overflow-hidden">
    <!-- Dekorasi latar belakang (Aesthetic premium) -->
    <div class="absolute inset-0 z-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-primary/20 via-background to-background"></div>
    
    <div class="z-10 max-w-3xl text-center space-y-8 p-10 rounded-3xl bg-card/60 backdrop-blur-md border border-white/10 shadow-2xl">
      <div class="space-y-4">
        <div class="inline-block px-3 py-1 mb-4 rounded-full bg-primary/10 text-primary text-sm font-semibold tracking-wide">
          Halaman Landing Page
        </div>
        <h1 class="text-5xl font-extrabold tracking-tight text-foreground">
          Sistem Informasi <span class="text-primary">Sekolah</span>
        </h1>
        <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
          Platform digital terintegrasi untuk mendukung kegiatan administrasi, komunikasi, dan akademik secara cerdas dan efisien.
        </p>
      </div>

      <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-6">
        <template v-if="!auth.user">
          <Button size="lg" class="w-full sm:w-auto text-lg px-10 h-14 rounded-xl shadow-lg hover:shadow-xl transition-all" @click="router.push('/login')">
            Masuk / Login
          </Button>
        </template>
        <template v-else>
          <div class="flex flex-col sm:flex-row items-center gap-3 w-full justify-center">
            <Button size="lg" variant="default" class="w-full sm:w-auto gap-2 h-12 rounded-xl shadow-md" @click="router.push('/dashboard')">
              <LayoutDashboard class="w-5 h-5" />
              Ke Dashboard
            </Button>
            <Button size="lg" variant="secondary" class="w-full sm:w-auto gap-2 h-12 rounded-xl pointer-events-none">
              <User class="w-5 h-5" />
              {{ auth.user.name }} ({{ auth.user.role }})
            </Button>
            <Button size="lg" variant="destructive" class="w-full sm:w-auto gap-2 h-12 rounded-xl shadow-md" @click="handleLogout">
              <LogOut class="w-5 h-5" />
              Logout
            </Button>
          </div>
        </template>
      </div>
    </div>
    
    <div class="absolute bottom-6 text-sm text-muted-foreground z-10 font-medium">
      &copy; {{ new Date().getFullYear() }} CerdasBangsa. Semua hak cipta dilindungi.
    </div>
  </div>
</template>
