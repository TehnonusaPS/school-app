<script setup>
import 'vue-sonner/style.css'
import { Toaster } from '@/components/ui/sonner'
</script>

<template>
  <ul class="background-squares">
    <li></li><li></li><li></li><li></li><li></li>
    <li></li><li></li><li></li><li></li><li></li>
    <li></li><li></li><li></li><li></li><li></li>
    <li></li><li></li><li></li><li></li><li></li>
  </ul>
  <ul class="background-squares-static">
    <li></li><li></li><li></li><li></li><li></li>
    <li></li><li></li><li></li><li></li><li></li>
    <li></li><li></li><li></li><li></li><li></li>
    <li></li><li></li><li></li><li></li><li></li>
  </ul>
  <div class="apple-glass-background"></div>
  <div class="school-background"></div>
  <router-view />
  <Toaster position="top-center" />
</template>

<!-- <template>
  <div class="bg-red-500 text-white p-10 text-2xl">
    TAILWIND HIDUP 🔥
  </div>
</template> -->
