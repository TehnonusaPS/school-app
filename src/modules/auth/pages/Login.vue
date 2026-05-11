<script setup>
import { GalleryVerticalEnd, GraduationCap } from 'lucide-vue-next'
import LoginForm from '@/components/LoginForm.vue'
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
          <LoginForm />
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
