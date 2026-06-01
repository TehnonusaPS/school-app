<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useRuanganStore } from '@/stores/ruanganStore'
import { toast } from 'vue-sonner'
import RuanganForm from '@/modules/lainnya/components/RuanganForm.vue'

const router = useRouter()
const route = useRoute()
const store = useRuanganStore()

const ruanganData = ref(null)
const isLoading = ref(true)

onMounted(() => {
  const targetId = route.params.id
  
  setTimeout(() => {
    const foundData = store.getById(targetId)
    
    if (foundData) {
      ruanganData.value = foundData
    } else {
      toast.error('Data ruangan tidak ditemukan')
      router.push('/lainnya/ruangan')
    }
    
    isLoading.value = false
  }, 500)
})

function handleSubmit(formData) {
  store.update(route.params.id, formData)
  toast.success('Perubahan ruangan berhasil disimpan!')
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
        Edit Ruangan Sekolah
      </h1>
      <p class="text-xs sm:text-sm text-muted-foreground leading-relaxed">
        Ubah informasi ruangan kelas, lab, atau fasilitas di bawah ini
      </p>
    </div>
    
    <div v-if="isLoading" class="flex justify-center items-center py-20">
      <p class="text-muted-foreground font-medium animate-pulse">Memuat data ruangan...</p>
    </div>
    
    <RuanganForm 
      v-else
      :initial-data="ruanganData" 
      is-edit
      @submit="handleSubmit" 
      @cancel="handleCancel" 
    />
  </div>
</template>
