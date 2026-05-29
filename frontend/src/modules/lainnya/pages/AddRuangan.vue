<script setup>
import { useRouter } from 'vue-router'
import { useRuanganStore } from '@/stores/ruanganStore'
import { toast } from 'vue-sonner'
import RuanganForm from '@/modules/lainnya/components/RuanganForm.vue'

const router = useRouter()
const store = useRuanganStore()

function handleSubmit(formData) {
  store.add(formData)
  toast.success('Ruangan berhasil ditambahkan!')
  router.push('/lainnya/ruangan')
}

function handleCancel() {
  router.push('/lainnya/ruangan')
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-foreground to-foreground/80 bg-clip-text">
        Tambah Ruangan Sekolah
      </h1>
      <p class="text-xs sm:text-sm text-muted-foreground leading-relaxed">
        Lengkapi formulir dibawah ini untuk menambahkan data ruangan baru
      </p>
    </div>
    <RuanganForm @submit="handleSubmit" @cancel="handleCancel" />
  </div>
</template>
