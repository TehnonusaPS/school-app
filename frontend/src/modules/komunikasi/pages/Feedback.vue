<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import ParentFeedback from './orang-tua/feedback/index.vue'
import PrincipalFeedback from './kepala-sekolah/feedback/index.vue'
import AdminFeedback from './admin-sekolah/feedback/index.vue'

const auth = useAuthStore()

const currentRole = computed(() => auth.user?.role)
</script>

<template>
  <ParentFeedback v-if="currentRole === 'orang_tua'" />
  <AdminFeedback v-else-if="currentRole === 'admin_sekolah'" />
  <PrincipalFeedback v-else />
</template>
