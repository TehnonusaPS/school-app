<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '07:30'
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const displayValue = ref(props.modelValue || '07:30')

watch(() => props.modelValue, (newVal) => {
  if (newVal !== displayValue.value) {
    displayValue.value = newVal || '07:30'
  }
}, { immediate: true })

const onInput = (e) => {
  let val = e.target.value.replace(/[^0-9]/g, '')
  
  if (val.length > 4) val = val.substring(0, 4)
  
  if (val.length >= 3) {
    displayValue.value = `${val.substring(0, 2)}:${val.substring(2)}`
  } else {
    displayValue.value = val
  }
}

const onBlur = () => {
  let raw = displayValue.value.replace(/[^0-9]/g, '')
  
  if (!raw) {
    displayValue.value = '07:30'
    emit('update:modelValue', '07:30')
    return
  }

  // Pad to 4 digits if needed (e.g. "730" -> "0730")
  if (raw.length === 1) raw = '0' + raw + '00'
  else if (raw.length === 2) raw = raw + '00'
  else if (raw.length === 3) raw = '0' + raw
  
  let hh = parseInt(raw.substring(0, 2), 10)
  let mm = parseInt(raw.substring(2, 4), 10)

  // Enforce 24h bounds: 00-23 for hours, 00-59 for minutes
  if (isNaN(hh) || hh < 0) hh = 0
  if (hh > 23) hh = 0 // wraps to 00 if > 23 as requested by user
  if (isNaN(mm) || mm < 0) mm = 0
  if (mm > 59) mm = 59

  const finalStr = `${String(hh).padStart(2, '0')}:${String(mm).padStart(2, '0')}`
  displayValue.value = finalStr
  emit('update:modelValue', finalStr)
}
</script>

<template>
  <input
    type="text"
    inputmode="numeric"
    :value="displayValue"
    @input="onInput"
    @blur="onBlur"
    maxlength="5"
    placeholder="07:30"
    :disabled="disabled"
    class="w-16 bg-card border border-input rounded-md px-2 py-1 text-xs font-mono font-bold text-center focus:ring-1 focus:ring-primary outline-none disabled:opacity-60 text-foreground transition-all"
    title="Format 24 Jam (00:00 - 23:59)"
  />
</template>
