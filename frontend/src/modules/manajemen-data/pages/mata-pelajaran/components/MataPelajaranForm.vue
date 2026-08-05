<script setup>
import { BookMarked, BookOpen, FileText, GraduationCap } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import SubjectGradeMapper from './SubjectGradeMapper.vue'

const props = defineProps({
  form: {
    type: Object,
    required: true
  },
  isEdit: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div class="w-full space-y-6 text-left">
    <!-- Informasi Utama Section Card -->
    <div class="rounded-2xl border border-border/80 dark:border-zinc-800 bg-card p-5 space-y-4 shadow-sm">
      <div class="flex items-center gap-2 pb-3 border-b border-border/60 dark:border-zinc-800">
        <div class="h-7 w-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
          <BookOpen class="h-4 w-4" />
        </div>
        <div>
          <h4 class="text-xs font-bold uppercase tracking-wider text-foreground">Informasi Utama Mapel</h4>
          <p class="text-[11px] text-muted-foreground">Identitas kode dan nama resmi mata pelajaran</p>
        </div>
      </div>

      <!-- Form Inputs Vertically Stacked -->
      <div class="space-y-4">
        <!-- Kode Mapel -->
        <div class="space-y-1.5">
          <Label class="text-xs font-medium text-foreground flex items-center gap-1.5">
            <BookMarked class="h-3.5 w-3.5 text-primary shrink-0" />
            Kode Mapel <span class="text-destructive">*</span>
          </Label>
          <Input 
            v-model="form.kode"
            placeholder="Contoh: MAT, IND, IPAS"
            class="uppercase font-mono font-bold tracking-wider text-xs h-10 border-input dark:border-zinc-800 focus-visible:ring-primary/20"
            required
          />
          <p class="text-[10px] text-muted-foreground">Kode/Singkatan unik mata pelajaran</p>
        </div>

        <!-- Nama Mapel -->
        <div class="space-y-1.5">
          <Label class="text-xs font-medium text-foreground flex items-center gap-1.5">
            <BookOpen class="h-3.5 w-3.5 text-primary shrink-0" />
            Nama Mata Pelajaran <span class="text-destructive">*</span>
          </Label>
          <Input 
            v-model="form.nama"
            placeholder="Contoh: Matematika, Bahasa Indonesia"
            class="text-xs h-10 border-input dark:border-zinc-800 focus-visible:ring-primary/20"
            required
          />
          <p class="text-[10px] text-muted-foreground">Nama resmi mata pelajaran yang akan muncul di rapor</p>
        </div>
      </div>

      <!-- Deskripsi / Keterangan -->
      <div class="space-y-1.5 pt-1">
        <Label class="text-xs font-medium text-foreground flex items-center gap-1.5">
          <FileText class="h-3.5 w-3.5 text-primary shrink-0" />
          Deskripsi & Ruang Lingkup
        </Label>
        <Textarea
          v-model="form.deskripsi"
          placeholder="Tuliskan penjelasan singkat mengenai ruang lingkup materi pembelajaran mapel ini..."
          class="text-xs min-h-[80px] resize-none border-input dark:border-zinc-800 focus-visible:ring-primary/20 leading-relaxed"
          :rows="3"
        />
      </div>
    </div>

    <!-- Section 2: Pemetaan Tingkat Kelas Pembelajar -->
    <div class="rounded-2xl border border-border/80 dark:border-zinc-800 bg-card p-5 space-y-4 shadow-sm">
      <div class="flex items-center gap-2 pb-2 border-b border-border/60 dark:border-zinc-800">
        <div class="h-7 w-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
          <GraduationCap class="h-4 w-4" />
        </div>
        <div>
          <h4 class="text-xs font-bold uppercase tracking-wider text-foreground">Tingkat Kelas Pembelajar</h4>
          <p class="text-[11px] text-muted-foreground">Pilih alokasi kelas yang mempelajari mata pelajaran ini</p>
        </div>
      </div>

      <SubjectGradeMapper
        v-model="form.grades"
        :level="form.school_level || 'SD'"
        label="Daftar Kelas Terpilih"
      />
    </div>
  </div>
</template>
