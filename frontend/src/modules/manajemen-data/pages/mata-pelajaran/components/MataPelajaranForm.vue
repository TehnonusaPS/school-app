<script setup>
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion'
import FormInput from '@/components/forms/FormInput.vue'
import FormTextArea from '@/components/forms/FormTextArea.vue'

const props = defineProps({
  form: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <div class="w-full space-y-4 text-left">
    <Accordion type="multiple" class="w-full" :default-value="['info']">
      <AccordionItem value="info">
        <AccordionTrigger class="text-sm font-semibold">
          Informasi Umum
        </AccordionTrigger>
        <AccordionContent class="space-y-4 pt-3">
          <div class="grid gap-4 grid-cols-1">
            <FormInput 
              v-model="form.kode" 
              label="Kode Mata Pelajaran" 
              placeholder="Contoh: MAT, IND"
              required
            />
            <FormInput 
              v-model="form.nama" 
              label="Nama Mata Pelajaran" 
              placeholder="Contoh: Matematika, Bahasa Indonesia"
              required
            />
            <FormTextArea 
              v-model="form.deskripsi" 
              label="Keterangan" 
              placeholder="Tuliskan penjelasan singkat mengenai ruang lingkup pembelajaran mapel ini..."
              :rows="3"
            />
          </div>
        </AccordionContent>
      </AccordionItem>
    </Accordion>
  </div>
</template>
