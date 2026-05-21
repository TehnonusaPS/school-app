<script setup>
import 'vue-sonner/style.css'
import { Toaster } from '@/components/ui/sonner'
</script>

<template>
  <router-view />
  <Toaster position="top-right" />
</template>

<!-- <template>
  <div class="bg-red-500 text-white p-10 text-2xl">
    TAILWIND HIDUP 🔥
  </div>
</template> -->
