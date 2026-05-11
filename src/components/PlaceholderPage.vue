<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="border-b pb-4">
      <h1 class="text-2xl font-bold tracking-tight">{{ title }}</h1>
      <p class="text-muted-foreground mt-1">{{ description }}</p>
    </div>

    <!-- Placeholder Content -->
    <div
      class="rounded-xl border border-dashed border-muted-foreground/30 bg-muted/30 p-12 text-center"
    >
      <div class="mx-auto flex max-w-md flex-col items-center gap-3">
        <div class="rounded-full bg-muted p-4">
          <component :is="icon" class="size-8 text-muted-foreground" />
        </div>
        <h2 class="text-lg font-semibold">{{ title }}</h2>
        <p class="text-sm text-muted-foreground">
          Halaman ini sedang dalam tahap pengembangan. Konten untuk
          <strong>{{ title }}</strong> akan segera tersedia.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Construction } from 'lucide-vue-next'
import { useRoute } from 'vue-router'
import { computed } from 'vue'

const route = useRoute()

const props = defineProps({
  icon: { default: () => Construction }
})

const title = computed(() => route.meta.title || 'Halaman')
const description = computed(() => route.meta.description || 'Selamat datang di halaman ini.')
</script>
