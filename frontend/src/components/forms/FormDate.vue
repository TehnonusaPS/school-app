<script setup>
import {
  Field,
  FieldContent,
  FieldError,
  FieldLabel
} from '@/components/ui/field'

import { Input } from '@/components/ui/input'

defineProps({
  label: String,
  modelValue: String,
  error: String,
  required: Boolean
})

defineEmits(['update:modelValue'])
</script>

<template>
  <Field :data-invalid="!!error">
    <FieldLabel>
      {{ label }}
      <span v-if="required" class="text-destructive">*</span>
    </FieldLabel>

    <FieldContent>
      <Input
        type="date"
        :model-value="modelValue"
        @update:model-value="$emit('update:modelValue', $event)"
        :class="{
          'border-destructive': error
        }"
      />
    </FieldContent>

    <FieldError v-if="error">{{ error }}</FieldError>
  </Field>
</template>
