<script setup>
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion'
import FormInput from '@/components/forms/FormInput.vue'
import FormSelect from '@/components/forms/FormSelect.vue'

defineProps({
  form: {
    type: Object,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})
</script>

<template>
  <div class="w-full space-y-4 text-left">
    <Accordion type="multiple" class="w-full" :default-value="['info']">
      <AccordionItem value="info">
        <AccordionTrigger class="text-sm font-semibold">
          Informasi Kelas
        </AccordionTrigger>
        <AccordionContent class="space-y-4 pt-3">
          <div class="grid gap-4 grid-cols-1">
            <FormInput
              v-model="form.name"
              label="Nama Kelas"
              placeholder="Contoh: X MIPA 1"
              :error="errors.name"
              required
            />
            
            <div class="grid grid-cols-2 gap-4">
              <FormSelect
                v-model="form.grade"
                label="Tingkat"
                placeholder="Pilih tingkat"
                :error="errors.grade"
                :options="[
                  { label: 'Kelas 10', value: '10' },
                  { label: 'Kelas 11', value: '11' },
                  { label: 'Kelas 12', value: '12' }
                ]"
                required
              />
              <FormInput
                v-model="form.major"
                label="Jurusan/Program"
                placeholder="Contoh: MIPA, IPS, dll"
                :error="errors.major"
              />
            </div>

            <FormInput
              v-model="form.homeroom_teacher"
              label="Wali Kelas"
              placeholder="Nama wali kelas (opsional)"
              :error="errors.homeroom_teacher"
            />

            <div class="grid grid-cols-2 gap-4">
              <FormInput
                v-model="form.room"
                label="Ruangan"
                placeholder="Contoh: R. 101"
                :error="errors.room"
              />
              <FormInput
                v-model.number="form.capacity"
                type="number"
                label="Kapasitas Siswa"
                placeholder="Contoh: 36"
                :error="errors.capacity"
                required
              />
            </div>
            
            <FormSelect
              v-model="form.status"
              label="Status"
              placeholder="Pilih status"
              :error="errors.status"
              :options="[
                { label: 'Aktif', value: 'active' },
                { label: 'Penuh', value: 'full' },
                { label: 'Belum Ada Wali', value: 'no_teacher' }
              ]"
              required
            />
          </div>
        </AccordionContent>
      </AccordionItem>
    </Accordion>
  </div>
</template>
