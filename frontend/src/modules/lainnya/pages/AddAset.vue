<script setup>
import { useRouter } from 'vue-router'
import { useAsetStore } from '@/stores/asetStore'
import { toast } from 'vue-sonner'
import AsetForm from '@/modules/lainnya/components/AsetForm.vue'

const router = useRouter()
const store = useAsetStore()

function handleSubmit(formData) {
  store.add(formData)
  toast.success('Aset berhasil ditambahkan!')
  router.push('/lainnya/aset')
}

function handleCancel() {
  router.push('/lainnya/aset')
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-foreground to-foreground/80 bg-clip-text">
        Tambah Aset Sekolah
      </h1>
      <p class="text-xs sm:text-sm text-muted-foreground leading-relaxed">
        Lengkapi formulir dibawah ini untuk menambahkan aset sekolah
      </p>
    </div>
    <AsetForm @submit="handleSubmit" @cancel="handleCancel" />
  </div>
</template>
