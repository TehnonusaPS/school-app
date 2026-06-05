<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import StudentRekap from '../components/StudentRekap.vue'
import ParentRekap from '../components/ParentRekap.vue'
import AdminRekap from '../components/AdminRekap.vue'

const auth = useAuthStore()
const isStudent = computed(() => auth.user?.role === 'siswa')
const isParent = computed(() => auth.user?.role === 'orang_tua')
</script>

<template>
  <div class="space-y-6">
    <StudentRekap v-if="isStudent" />
    <ParentRekap v-else-if="isParent" />
    <AdminRekap v-else />
  </div>
</template>
