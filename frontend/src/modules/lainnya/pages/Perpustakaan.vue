<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'
import { Lock } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

import PageHeader from '@/components/page-header/PageHeader.vue'
import PerpustakaanStatCards from '../components/PerpustakaanStatCards.vue'
import PerpustakaanTable from '../components/PerpustakaanTable.vue'
import { usePerpustakaanStore } from '@/stores/perpustakaanStore'
import { computed } from 'vue'
import { toast } from 'vue-sonner'

const auth = useAuthStore()
const router = useRouter()
const store = usePerpustakaanStore()

const books = computed(() => store.items)

function handleDelete(id) {
  store.delete(id)
  toast.success('Buku berhasil dihapus!')
}
</script>

<template>
  <div v-if="auth.user?.role === 'admin_sekolah'" class="space-y-6">
    <PageHeader 
      title="Manajemen Buku Perpustakaan" 
      description="Kelola koleksi buku yang ada di perpustakaan sekolah" 
    />

    <!-- Stats Cards -->
    <PerpustakaanStatCards />

    <!-- Data Table & Filters -->
    <PerpustakaanTable :items="books" @delete="handleDelete" />
  </div>
  
  <div v-else class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="bg-card border border-border rounded-2xl p-8 max-w-md w-full shadow-sm flex flex-col items-center">
      <div class="size-16 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mb-6 border border-primary/20">
        <Lock class="size-8" />
      </div>
      <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-foreground mb-2">Akses Dibatasi</h2>
      <p class="text-sm text-muted-foreground leading-relaxed mb-8">
        Halaman Manajemen Perpustakaan untuk role Anda (Kepala Sekolah / Admin Yayasan) belum tersedia dan sedang dalam tahap pengembangan.
      </p>
      <Button @click="router.push('/dashboard')" size="lg" class="w-full font-semibold rounded-xl transition-all active:scale-[0.98]">
        Kembali ke Dashboard
      </Button>
    </div>
  </div>
</template>
