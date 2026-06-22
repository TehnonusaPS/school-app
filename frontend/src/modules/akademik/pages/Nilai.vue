<script setup>
import { useAuthStore } from '@/stores/authStore'
import SiswaNilai from '../components/SiswaNilai.vue'
import OrangTuaNilai from '../components/OrangTuaNilai.vue'

const auth = useAuthStore()
</script>

<template>
  <div class="space-y-6">
    <OrangTuaNilai v-if="auth.user?.role === 'orang_tua'" />
    <SiswaNilai v-else />
  </div>
</template>
