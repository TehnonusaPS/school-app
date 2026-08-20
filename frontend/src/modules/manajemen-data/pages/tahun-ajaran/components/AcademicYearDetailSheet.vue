<script setup>
import { computed } from 'vue'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription } from '@/components/ui/sheet'
import { Badge } from '@/components/ui/badge'
import { Calendar, Clock, CheckCircle2, XCircle, Sparkles, BookOpen, Layers } from 'lucide-vue-next'

const props = defineProps({
  open: Boolean,
  item: Object
})

const emit = defineEmits(['update:open'])

function formatDate(dateStr) {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const isAktif = computed(() => props.item?.status === 'aktif')
</script>

<template>
  <Sheet :open="open" @update:open="emit('update:open', $event)">
    <SheetContent :show-close-button="false" class="sm:max-w-[480px] flex flex-col h-full gap-2 text-left p-6">
      
      <!-- Sheet Header -->
      <SheetHeader class="border-b border-border/60 pb-3.5 text-left">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <div class="p-2 rounded-xl bg-primary/10 text-primary shrink-0">
              <Calendar class="size-4" />
            </div>
            <div>
              <SheetTitle class="text-base font-extrabold text-foreground flex items-center gap-2">
                Tahun Ajaran {{ item?.tahun }}
              </SheetTitle>
              <SheetDescription class="text-xs text-muted-foreground mt-0.5">
                Rincian periode semester ganjil & genap serta status aktivasi.
              </SheetDescription>
            </div>
          </div>

          <Badge :variant="isAktif ? 'green' : 'gray'" class="px-3 py-1 rounded-full text-xs font-bold shrink-0">
            {{ isAktif ? 'Aktif' : 'Nonaktif' }}
          </Badge>
        </div>
      </SheetHeader>

      <!-- Content Body -->
      <div class="flex-1 overflow-y-auto py-5 space-y-4 no-scrollbar">
        
        <!-- Period Summary Card -->
        <div class="p-4 rounded-2xl bg-linear-to-br from-primary/10 via-primary/5 to-transparent border border-primary/20 space-y-2">
          <div class="flex items-center justify-between text-xs text-muted-foreground font-semibold">
            <span class="flex items-center gap-1.5 text-primary font-bold">
              <Sparkles class="size-3.5" />
              Rentang Periode Pembelajaran
            </span>
            <span class="text-[10px] bg-primary/15 text-primary font-extrabold px-2.5 py-0.5 rounded-full">
              Full Academic Year
            </span>
          </div>

          <div class="flex items-center justify-between pt-1">
            <div>
              <p class="text-[10px] text-muted-foreground uppercase font-bold tracking-wider">Awal Periode</p>
              <p class="text-xs font-extrabold text-foreground mt-0.5">
                {{ formatDate(item?.tanggalMulai) }}
              </p>
            </div>

            <div class="h-0.5 flex-1 mx-4 bg-primary/20 relative">
              <div class="absolute -top-1 left-1/2 -translate-x-1/2 bg-background border border-primary/30 rounded-full p-0.5">
                <Clock class="size-3 text-primary" />
              </div>
            </div>

            <div class="text-right">
              <p class="text-[10px] text-muted-foreground uppercase font-bold tracking-wider">Akhir Periode</p>
              <p class="text-xs font-extrabold text-foreground mt-0.5">
                {{ formatDate(item?.tanggalSelesai) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Semester Ganjil Card -->
        <div class="p-4 rounded-2xl border border-border/80 bg-card hover:border-primary/40 transition-all space-y-3 shadow-2xs">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="p-1.5 rounded-lg bg-primary/10 text-primary">
                <BookOpen class="size-3.5" />
              </div>
              <div>
                <h4 class="text-xs font-extrabold text-foreground">Semester Ganjil (Odd)</h4>
                <p class="text-[10px] text-muted-foreground">Sesi Pertama Tahun Akademik</p>
              </div>
            </div>

            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full"
              :class="isAktif ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-muted text-muted-foreground'">
              <component :is="isAktif ? CheckCircle2 : XCircle" class="size-3" />
              {{ isAktif ? 'Aktif' : 'Nonaktif' }}
            </span>
          </div>

          <div class="grid grid-cols-2 gap-3 pt-1 border-t border-border/40 text-xs">
            <div class="space-y-0.5">
              <span class="text-[10px] font-semibold text-muted-foreground">Tanggal Mulai</span>
              <p class="font-bold text-foreground font-mono">{{ formatDate(item?.odd?.tanggalMulai) }}</p>
            </div>
            <div class="space-y-0.5">
              <span class="text-[10px] font-semibold text-muted-foreground">Tanggal Selesai</span>
              <p class="font-bold text-foreground font-mono">{{ formatDate(item?.odd?.tanggalSelesai) }}</p>
            </div>
          </div>
        </div>

        <!-- Semester Genap Card -->
        <div class="p-4 rounded-2xl border border-border/80 bg-card hover:border-emerald-500/40 transition-all space-y-3 shadow-2xs">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <div class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                <Layers class="size-3.5" />
              </div>
              <div>
                <h4 class="text-xs font-extrabold text-foreground">Semester Genap (Even)</h4>
                <p class="text-[10px] text-muted-foreground">Sesi Kedua Tahun Akademik</p>
              </div>
            </div>

            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full"
              :class="isAktif ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-muted text-muted-foreground'">
              <component :is="isAktif ? CheckCircle2 : XCircle" class="size-3" />
              {{ isAktif ? 'Aktif' : 'Nonaktif' }}
            </span>
          </div>

          <div class="grid grid-cols-2 gap-3 pt-1 border-t border-border/40 text-xs">
            <div class="space-y-0.5">
              <span class="text-[10px] font-semibold text-muted-foreground">Tanggal Mulai</span>
              <p class="font-bold text-foreground font-mono">{{ formatDate(item?.even?.tanggalMulai) }}</p>
            </div>
            <div class="space-y-0.5">
              <span class="text-[10px] font-semibold text-muted-foreground">Tanggal Selesai</span>
              <p class="font-bold text-foreground font-mono">{{ formatDate(item?.even?.tanggalSelesai) }}</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Footer -->
      <div class="border-t border-border/60 pt-3.5 flex items-center justify-end shrink-0">
        <button
          type="button"
          class="px-5 py-2 rounded-xl text-xs font-bold bg-muted hover:bg-muted/80 text-foreground transition-colors cursor-pointer"
          @click="emit('update:open', false)"
        >
          Tutup
        </button>
      </div>

    </SheetContent>
  </Sheet>
</template>
