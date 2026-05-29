<script setup>
import {
  Field,
  FieldContent,
  FieldError,
  FieldLabel
} from '@/components/ui/field'

import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select'

defineProps({
  label: String,
  placeholder: String,
  modelValue: String,
  error: String,
  required: Boolean,
  options: {
    type: Array,
    default: () => []
  }
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
      <Select
        :model-value="modelValue"
        @update:model-value="$emit('update:modelValue', $event)"
      >
        <SelectTrigger class="w-full" :class="{'border-destructive': error}">
          <SelectValue :placeholder="placeholder" />
        </SelectTrigger>

        <SelectContent>
          <SelectItem
            v-for="item in options"
            :key="item.value"
            :value="item.value"
          >
            {{ item.label }}
          </SelectItem>
        </SelectContent>
      </Select>
    </FieldContent>

    <FieldError v-if="error">{{ error }}</FieldError>
  </Field>
</template>