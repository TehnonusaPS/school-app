<script setup>
import { computed, ref } from 'vue'
import { useLandingEditorStore } from '@/stores/landingEditorStore'
import { Eye } from 'lucide-vue-next'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button'

import { toast } from 'vue-sonner'

const store = useLandingEditorStore()

const isPublished = computed(() => store.landingPage?.is_published || false)

const slugUrl = computed(() => {
  if (!store.landingPage?.slug) return '#'
  return `/s/${store.landingPage.slug}`
})

const showPublishDialog = ref(false)

function confirmTogglePublish() {
  showPublishDialog.value = true
}

async function handleTogglePublish() {
  try {
    const res = await store.togglePublishStatus()
    toast.success(res.message)
    showPublishDialog.value = false
  } catch (err) {
    toast.error(err.message)
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Akses Builder Nonaktif dari Konfigurasi Admin -->
    <div
      v-if="!store.loading && store.landingPage && !store.landingPage.landing_page_enabled"
      class="glass-mini rounded-2xl border border-amber-500/30 p-10 text-center space-y-4 max-w-xl mx-auto my-12 shadow-sm bg-amber-500/5"
    >
      <div class="w-14 h-14 rounded-full bg-amber-500/10 text-amber-500 border border-amber-500/20 flex items-center justify-center mx-auto text-2xl font-bold">🔒</div>
      <h3 class="text-lg font-bold text-foreground">Akses Website Builder Dinonaktifkan</h3>
      <p class="text-xs text-muted-foreground leading-relaxed">
        Fitur pengelolaan website dan landing page untuk sekolah / yayasan Anda saat ini <strong>dinonaktifkan</strong> dalam konfigurasi sistem. Silakan hubungi <strong>Super Admin</strong> untuk mengaktifkan Akses Builder Anda.
      </p>
    </div>

    <!-- Akses Builder Aktif -->
    <template v-else>
      <!-- Top Action Banner (Untuk Publish Status) -->
      <div
        class="glass-mini rounded-xl border border-white/20 p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4"
      >
        <div class="flex items-center gap-3">
          <span
            class="flex h-3 w-3 rounded-full"
            :class="isPublished ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500'"
          ></span>
          <div>
            <span class="text-sm font-bold text-foreground">Status Landing Page:</span>
            <span
              class="text-sm font-semibold ml-1.5"
              :class="isPublished ? 'text-emerald-500' : 'text-amber-500'"
            >
              {{ isPublished ? 'Aktif (Published)' : 'Draft (Offline)' }}
            </span>
          </div>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <button
            @click="confirmTogglePublish"
            class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-xs font-bold text-white transition-all bg-primary hover:bg-primary/90"
          >
            {{ isPublished ? 'Nonaktifkan' : 'Aktifkan Publik' }}
          </button>
          <a
            v-if="store.landingPage?.slug"
            :href="slugUrl"
            target="_blank"
            class="flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl border border-white/20 bg-background/50 hover:bg-white/10 font-bold text-xs transition-colors text-foreground"
          >
            <Eye class="w-4 h-4" /> Lihat Halaman
          </a>
        </div>
      </div>

      <!-- Main Form Content -->
      <div class="glass-mini rounded-xl border border-white/20 p-6 shadow-sm">
        <!-- Tempat menyisipkan Section (Slot) -->
        <slot />
      </div>
    </template>

    <!-- Alert Dialog Konfirmasi Publikasi -->
    <AlertDialog :open="showPublishDialog" @update:open="showPublishDialog = $event">
      <AlertDialogContent class="sm:max-w-md">
        <AlertDialogHeader>
          <AlertDialogTitle>Konfirmasi Perubahan Status</AlertDialogTitle>
          <AlertDialogDescription>
            Anda akan {{ isPublished ? 'menonaktifkan (menyembunyikan)' : 'mengaktifkan (mempublikasikan)' }} landing page ini dari publik.
            Apakah Anda yakin ingin melanjutkan?
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel @click="showPublishDialog = false">Batal</AlertDialogCancel>
          <AlertDialogAction @click="handleTogglePublish">Ya, Lanjutkan</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  </div>
</template>
