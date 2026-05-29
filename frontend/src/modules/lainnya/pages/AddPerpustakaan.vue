<script setup>
import { useRouter } from 'vue-router'
import { usePerpustakaanStore } from '@/stores/perpustakaanStore'
import { toast } from 'vue-sonner'
import PerpustakaanForm from '@/modules/lainnya/components/PerpustakaanForm.vue'

const router = useRouter()
const store = usePerpustakaanStore()

function handleSubmit(formData) {
  store.add(formData)
  toast.success('Buku berhasil ditambahkan!')
  router.push('/lainnya/perpustakaan')
}

function handleCancel() {
  router.push('/lainnya/perpustakaan')
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-foreground to-foreground/80 bg-clip-text">
        Tambah Buku Perpustakaan
      </h1>
      <p class="text-xs sm:text-sm text-muted-foreground leading-relaxed">
        Lengkapi formulir dibawah ini untuk menambahkan data buku baru
      </p>
    </div>
    <PerpustakaanForm @submit="handleSubmit" @cancel="handleCancel" />
  </div>
</template>
