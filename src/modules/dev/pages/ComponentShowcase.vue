<script setup>
import { ref } from 'vue'
import ShowcasePart1 from '../components/ShowcasePart1.vue'
import ShowcasePart2 from '../components/ShowcasePart2.vue'
import ShowcasePart3 from '../components/ShowcasePart3.vue'
import ShowcasePart4 from '../components/ShowcasePart4.vue'
import ShowcasePart5 from '../components/ShowcasePart5.vue'
import ShowcasePart6 from '../components/ShowcasePart6.vue'
import { Input } from '@/components/ui/input'
import { Search } from 'lucide-vue-next'

const searchQuery = ref('')
</script>

<template>
  <div class="space-y-12 pb-24">
    <div>
      <h1 class="text-3xl font-bold tracking-tight text-foreground">UI Components Reference</h1>
      <p class="text-muted-foreground mt-2 max-w-3xl">
        Kumpulan dokumentasi lengkap seluruh komponen UI yang ada pada aplikasi ini. Terdapat 60
        komponen yang siap digunakan.
      </p>
      <div class="mt-4 relative max-w-md">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
        <Input
          v-model="searchQuery"
          placeholder="Cari komponen (contoh: accordion, button, card)..."
          class="pl-9"
        />
      </div>
    </div>

    <ShowcasePart1 :search-query="searchQuery" />
    <ShowcasePart2 :search-query="searchQuery" />
    <ShowcasePart3 :search-query="searchQuery" />
    <ShowcasePart4 :search-query="searchQuery" />
    <ShowcasePart5 :search-query="searchQuery" />
    <ShowcasePart6 :search-query="searchQuery" />
  </div>
</template>
