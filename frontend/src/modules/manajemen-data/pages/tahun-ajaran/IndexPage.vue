<script setup>
import { useAuthStore } from '@/stores/authStore'
import RosterPage from './RosterPage.vue'
import CrudPage from './CrudPage.vue'

const auth = useAuthStore()
</script>

<template>
  <RosterPage v-if="auth.user?.role === 'kepala_sekolah'" />
  <CrudPage v-else />
</template>
