<script setup>
import { computed, ref, onMounted, watch } from 'vue'

const props = defineProps({
  value: { type: [String, Number], required: true },
  delay: { type: Number, default: 0 }
})

const chars = computed(() => {
  return String(props.value).split('').map(char => {
    // Check if it's a digit (0-9)
    if (char.trim() === '' || isNaN(Number(char))) {
      return { isDigit: false, value: char }
    }
    
    // Generate a column of random digits to spin through
    const col = []
    const spins = 12 // Number of random spins before settling
    for(let i = 0; i < spins; i++) {
      col.push(Math.floor(Math.random() * 10))
    }
    // The final target number
    col.push(char)
    
    return { isDigit: true, value: char, column: col }
  })
})

const isReady = ref(false)
let timeoutId = null

const startSpin = () => {
  isReady.value = false
  if (timeoutId) clearTimeout(timeoutId)
  
  // Wait for the global delay, then start the spin!
  timeoutId = setTimeout(() => {
    isReady.value = true
  }, props.delay + 50)
}

onMounted(() => {
  startSpin()
})

// Re-spin if the value changes dynamically
watch(() => props.value, () => {
  startSpin()
})
</script>

<template>
  <div class="inline-flex items-center">
    <template v-for="(charObj, index) in chars" :key="index">
      
      <!-- Non-digit characters (like dots, commas, spaces) -->
      <span v-if="!charObj.isDigit" class="inline-block">{{ charObj.value }}</span>
      
      <!-- Digit columns (Mahjong slot effect) -->
      <div v-else class="relative inline-flex flex-col overflow-hidden align-bottom" style="height: 1.1em; line-height: 1.1em;">
        <!-- Invisible static character to maintain correct dynamic width -->
        <span class="invisible">{{ charObj.value }}</span>
        
        <!-- The spinning column -->
        <div 
          class="absolute inset-x-0 top-0 flex flex-col"
          :style="{ 
            transitionProperty: 'transform',
            transitionDuration: isReady ? '2200ms' : '0ms',
            transitionTimingFunction: 'cubic-bezier(0.16, 1, 0.3, 1)',
            transitionDelay: isReady ? `${index * 120}ms` : '0ms',
            transform: isReady ? 'translateY(calc(-100% + 1.1em))' : 'translateY(0)'
          }"
        >
          <span 
            v-for="(n, i) in charObj.column" 
            :key="i" 
            class="flex items-center justify-center h-[1.1em]"
          >
            {{ n }}
          </span>
        </div>
      </div>
      
    </template>
  </div>
</template>
