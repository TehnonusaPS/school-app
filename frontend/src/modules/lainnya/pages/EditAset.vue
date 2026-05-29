<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAsetStore } from '@/stores/asetStore'
import { toast } from 'vue-sonner'
import AsetForm from '@/modules/lainnya/components/AsetForm.vue'

const router = useRouter()
const route = useRoute()
const store = useAsetStore()

const asetData = ref(null)
const isLoading = ref(true)

onMounted(() => {
  const targetId = route.params.id
  
  setTimeout(() => {
    const foundData = store.getById(targetId)
    
    if (foundData) {
      asetData.value = foundData
    } else {
      toast.error('Data aset tidak ditemukan')
      router.push('/lainnya/aset')
    }
    
    isLoading.value = false
  }, 500)
})

function handleSubmit(formData) {
  store.update(route.params.id, formData)
  toast.success('Perubahan berhasil disimpan!')
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
        Edit Aset Sekolah
      </h1>
      <p class="text-xs sm:text-sm text-muted-foreground leading-relaxed">
        Ubah informasi aset sekolah di bawah ini
      </p>
    </div>
    
    <div v-if="isLoading" class="flex justify-center items-center py-20">
      <p class="text-muted-foreground font-medium animate-pulse">Memuat data aset...</p>
    </div>
    
    <AsetForm 
      v-else
      :initial-data="asetData" 
      is-edit
      @submit="handleSubmit" 
      @cancel="handleCancel" 
    />
  </div>
</template>
