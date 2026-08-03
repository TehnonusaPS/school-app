<script setup>
import {
  Field,
  FieldContent,
  FieldError,
  FieldLabel
} from '@/components/ui/field'
import DatePicker from '@/components/date-picker/DatePicker.vue'

defineProps({
  label: String,
  modelValue: [String, Object],
  error: String,
  required: Boolean,
  placeholder: String,
  disabled: Boolean
})

defineEmits(['update:modelValue'])
</script>

<template>
  <Field :data-invalid="!!error">
    <FieldLabel v-if="label">
      {{ label }}
      <span v-if="required" class="text-destructive">*</span>
    </FieldLabel>

    <FieldContent>
      <DatePicker
        :model-value="modelValue"
        @update:model-value="$emit('update:modelValue', $event)"
        :placeholder="placeholder || 'Pilih tanggal'"
        :disabled="disabled"
        :error="!!error"
      />
    </FieldContent>

    <FieldError v-if="error">{{ error }}</FieldError>
  </Field>
</template>
