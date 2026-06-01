<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { usePerpustakaanStore } from '@/stores/perpustakaanStore'
import { toast } from 'vue-sonner'
import PerpustakaanForm from '@/modules/lainnya/components/PerpustakaanForm.vue'

const router = useRouter()
const route = useRoute()
const store = usePerpustakaanStore()

const bookData = ref(null)
const isLoading = ref(true)

onMounted(() => {
  const targetId = route.params.id
  
  setTimeout(() => {
    const foundData = store.getById(targetId)
    
    if (foundData) {
      bookData.value = foundData
    } else {
      toast.error('Data buku tidak ditemukan')
      router.push('/lainnya/perpustakaan')
    }
    
    isLoading.value = false
  }, 500)
})

function handleSubmit(formData) {
  store.update(route.params.id, formData)
  toast.success('Perubahan buku berhasil disimpan!')
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
        Edit Buku Perpustakaan
      </h1>
      <p class="text-xs sm:text-sm text-muted-foreground leading-relaxed">
        Ubah informasi data buku di bawah ini
      </p>
    </div>
    
    <div v-if="isLoading" class="flex justify-center items-center py-20">
      <p class="text-muted-foreground font-medium animate-pulse">Memuat data buku...</p>
    </div>
    
    <PerpustakaanForm 
      v-else
      :initial-data="bookData" 
      is-edit
      @submit="handleSubmit" 
      @cancel="handleCancel" 
    />
  </div>
</template>
