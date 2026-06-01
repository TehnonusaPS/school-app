<script setup>
import { ImagePlus } from 'lucide-vue-next'

defineProps({
  label: {
    type: String,
    default: 'Upload Gambar'
  },
  note: String,
  preview: String,
  error: String
})

const emit = defineEmits(['change'])

const handleFileChange = (event) => {
  const file = event.target.files?.[0]

  if (!file) return

  emit('change', file)
}
</script>

<template>
  <div class="space-y-3">
    <label
      class="flex flex-col items-center justify-center w-64 aspect-square mx-auto rounded-xl border-2 border-dashed cursor-pointer transition hover:bg-muted/50 overflow-hidden"
      :class="[
        error
          ? 'border-destructive'
          : 'border-muted-foreground/25'
      ]"
    >
      <template v-if="preview">
        <img :src="preview" alt="Preview" class="w-full h-full object-cover"/>
      </template>

      <template v-else>
        <div class="flex flex-col items-center gap-3 text-muted-foreground">
          <ImagePlus class="w-10 h-10" />
          <div class="text-sm text-center">Klik untuk upload gambar</div>
        </div>
      </template>

      <input
        type="file"
        accept="image/*"
        class="hidden"
        @change="handleFileChange"
      />
    </label>
    <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
    <div v-if="note" class="p-4 rounded-lg bg-white border border-primary/10 text-xs text-muted-foreground italic">
        {{ note }}
    </div>
  </div>
</template>