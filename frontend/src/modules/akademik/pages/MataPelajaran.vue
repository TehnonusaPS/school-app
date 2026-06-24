<script setup>
import { useAuthStore } from '@/stores/authStore'
import SiswaMataPelajaran from '../components/SiswaMataPelajaran.vue'
import GuruMataPelajaran from '../components/GuruMataPelajaran.vue';

const auth = useAuthStore()
</script>

<template>
  <!-- Siswa Mata Pelajaran -->
  <div v-if="auth.user?.role === 'siswa'" class="space-y-6">
    <SiswaMataPelajaran />
  </div>

  <div v-else class="space-y-6">
    <GuruMataPelajaran/>
  </div>
</template>
