<script setup>
import { ref } from 'vue'
import { ArrowRight } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Card, CardContent } from '@/components/ui/card'

const form = ref({
  nama: '',
  jabatan: '',
  sekolah: '',
  whatsapp: '',
  pesan: ''
})

const isSubmitting = ref(false)

function handleSubmit() {
  if (!form.value.nama || !form.value.sekolah || !form.value.whatsapp) {
    toast.error('Mohon lengkapi kolom Nama Anda, Nama Sekolah/Yayasan, dan Nomor WhatsApp.')
    return
  }

  isSubmitting.value = true
  
  // Simulasi kirim data ke server
  setTimeout(() => {
    isSubmitting.value = false
    toast.success('Pesan Anda telah berhasil dikirim! Tim kami akan segera menghubungi Anda.')
    form.value = {
      nama: '',
      jabatan: '',
      sekolah: '',
      whatsapp: '',
      pesan: ''
    }
  }, 1200)
}
</script>

<template>
  <Card class="rounded-3xl border-slate-100  p-6 sm:p-8 shadow-xl shadow-slate-100/50  dark:border-slate-800/80 dark:bg-slate-900/40 dark:shadow-none">
    <CardContent class="p-0 space-y-5">
      <form @submit.prevent="handleSubmit" class="space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Nama Anda -->
          <div class="space-y-2">
            <Label for="contact-name" class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
              Nama Anda
            </Label>
            <Input 
              id="contact-name"
              v-model="form.nama"
              placeholder="Nama lengkap" 
              class="h-11 sm:h-12 bg-slate-50/50 border-slate-200/80 rounded-xl px-4 text-sm focus-visible:ring-primary/20 focus-visible:border-primary dark:bg-slate-900/50 dark:border-slate-800 transition-all"
            />
          </div>

          <!-- Jabatan -->
          <div class="space-y-2">
            <Label for="contact-role" class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
              Jabatan
            </Label>
            <Input 
              id="contact-role"
              v-model="form.jabatan"
              placeholder="Kepala Sekolah / Ketua Yayasan" 
              class="h-11 sm:h-12 bg-slate-50/50 border-slate-200/80 rounded-xl px-4 text-sm focus-visible:ring-primary/20 focus-visible:border-primary dark:bg-slate-900/50 dark:border-slate-800 transition-all"
            />
          </div>
        </div>

        <!-- Nama Sekolah / Yayasan -->
        <div class="space-y-2">
          <Label for="contact-school" class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
            Nama Sekolah / Yayasan
          </Label>
          <Input 
            id="contact-school"
            v-model="form.sekolah"
            placeholder="Nama institusi Anda" 
            class="h-11 sm:h-12 bg-slate-50/50 border-slate-200/80 rounded-xl px-4 text-sm focus-visible:ring-primary/20 focus-visible:border-primary dark:bg-slate-900/50 dark:border-slate-800 transition-all"
          />
        </div>

        <!-- Nomor WhatsApp -->
        <div class="space-y-2">
          <Label for="contact-whatsapp" class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
            Nomor WhatsApp
          </Label>
          <Input 
            id="contact-whatsapp"
            v-model="form.whatsapp"
            placeholder="08xx-xxxx-xxxx" 
            class="h-11 sm:h-12 bg-slate-50/50 border-slate-200/80 rounded-xl px-4 text-sm focus-visible:ring-primary/20 focus-visible:border-primary dark:bg-slate-900/50 dark:border-slate-800 transition-all"
          />
        </div>

        <!-- Pesan / Kebutuhan -->
        <div class="space-y-2">
          <Label for="contact-message" class="text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-300">
            Pesan / Kebutuhan
          </Label>
          <Textarea 
            id="contact-message"
            v-model="form.pesan"
            placeholder="Ceritakan kebutuhan sekolah atau yayasan Anda..." 
            class="min-h-[110px] bg-slate-50/50 border-slate-200/80 rounded-xl p-4 text-sm resize-none focus-visible:ring-primary/20 focus-visible:border-primary dark:bg-slate-900/50 dark:border-slate-800 transition-all"
          />
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="isSubmitting"
          class="w-full flex items-center justify-center gap-2 rounded-xl bg-primary hover:bg-primary/90 text-primary-foreground active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed transition-all py-3.5 text-sm font-bold shadow-lg shadow-primary/20 dark:shadow-none cursor-pointer"
        >
          <span v-if="isSubmitting">Mengirim...</span>
          <span v-else class="flex items-center gap-2">
            Kirim & Jadwalkan Demo
            <ArrowRight class="size-4" />
          </span>
        </button>
      </form>
    </CardContent>
  </Card>
</template>
