<script lang="ts" setup>
import type { ToasterProps } from 'vue-sonner'

import {
  CircleCheckIcon,
  InfoIcon,
  Loader2Icon,
  OctagonXIcon,
  TriangleAlertIcon,
  XIcon,
} from 'lucide-vue-next'
import { Toaster as Sonner } from 'vue-sonner'
import { cn } from '@/lib/utils'

const props = defineProps<ToasterProps>()
</script>

<template>
  <Sonner
    :class="cn('toaster group', props.class)"
    :style="{
      '--normal-bg': 'color-mix(in oklch, var(--primary) 85%, transparent)',
      '--normal-text': 'var(--primary-foreground)',
      '--normal-border': 'transparent',
      '--border-radius': '12px',
    }"
    :close-button="true"
    :toast-options="{
      classes: {
        toast:
          'group toast shadow-xl rounded-xl border p-4 text-sm font-medium transition-all duration-300 flex items-center gap-3',
        description: '!text-primary-foreground opacity-90 text-xs',
        actionButton:
          'group-[.toast]:bg-primary group-[.toast]:text-primary-foreground',
        cancelButton:
          'group-[.toast]:bg-muted group-[.toast]:text-muted-foreground',
        closeButton:
          'group-[.toast]:!bg-background group-[.toast]:!border group-[.toast]:!text-muted-foreground group-[.toast]:hover:!text-foreground group-[.toast]:hover:!border-foreground/30',
      },
    }"
    v-bind="props"
  >
    <template #success-icon>
      <CircleCheckIcon class="size-5 text-emerald-500 dark:text-emerald-400 shrink-0 dynamic-icon" />
    </template>
    <template #info-icon>
      <InfoIcon class="size-5 text-blue-500 dark:text-blue-400 shrink-0 dynamic-icon" />
    </template>
    <template #warning-icon>
      <TriangleAlertIcon class="size-5 text-amber-500 dark:text-amber-400 shrink-0 dynamic-icon" />
    </template>
    <template #error-icon>
      <OctagonXIcon class="size-5 text-rose-500 dark:text-rose-400 shrink-0 dynamic-icon" />
    </template>
    <template #loading-icon>
      <div>
        <Loader2Icon class="size-5 animate-spin text-primary shrink-0" />
      </div>
    </template>
    <template #close-icon>
      <XIcon class="size-4 opacity-70 hover:opacity-100 transition-opacity" />
    </template>
  </Sonner>
</template>

<style>
/* Premium Glassmorphism & Animations for Toasts */
.toaster .toast {
  backdrop-filter: blur(16px) saturate(120%) !important;
  -webkit-backdrop-filter: blur(16px) saturate(120%) !important;
  font-family: inherit !important;
  transform: scale(0.95);
  animation: toast-entrance 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes toast-entrance {
  to {
    transform: scale(1);
  }
}

/* Force description text color for normal primary toasts */
.toaster [data-sonner-toast]:not([data-type="success"]):not([data-type="error"]):not([data-type="warning"]):not([data-type="info"]) [data-description] {
  color: var(--primary-foreground) !important;
  opacity: 0.9 !important;
}

/* Light mode overrides */
.toaster {
  --success-bg: rgba(16, 185, 129, 0.07) !important;
  --success-text: #047857 !important;
  --success-border: rgba(16, 185, 129, 0.2) !important;

  --error-bg: rgba(244, 63, 94, 0.07) !important;
  --error-text: #be123c !important;
  --error-border: rgba(244, 63, 94, 0.2) !important;
  
  --info-bg: rgba(59, 130, 246, 0.07) !important;
  --info-text: #1d4ed8 !important;
  --info-border: rgba(59, 130, 246, 0.2) !important;

  --warning-bg: rgba(245, 158, 11, 0.07) !important;
  --warning-text: #a16207 !important;
  --warning-border: rgba(245, 158, 11, 0.2) !important;
}

.toaster .toast.success {
  box-shadow: 0 10px 30px -5px rgba(16, 185, 129, 0.12),
              0 0 15px -3px rgba(16, 185, 129, 0.05) !important;
}

.toaster .toast.error {
  box-shadow: 0 10px 30px -5px rgba(244, 63, 94, 0.12),
              0 0 15px -3px rgba(244, 63, 94, 0.05) !important;
}

/* Dark mode overrides */
.dark .toaster {
  --success-bg: rgba(16, 185, 129, 0.1) !important;
  --success-text: #34d399 !important;
  --success-border: rgba(16, 185, 129, 0.25) !important;

  --error-bg: rgba(244, 63, 94, 0.1) !important;
  --error-text: #fb7185 !important;
  --error-border: rgba(244, 63, 94, 0.25) !important;

  --info-bg: rgba(59, 130, 246, 0.1) !important;
  --info-text: #60a5fa !important;
  --info-border: rgba(59, 130, 246, 0.25) !important;

  --warning-bg: rgba(245, 158, 11, 0.1) !important;
  --warning-text: #fbbf24 !important;
  --warning-border: rgba(245, 158, 11, 0.25) !important;
}

.dark .toaster .toast.success {
  box-shadow: 0 12px 30px -5px rgba(16, 185, 129, 0.2),
              0 0 20px 2px rgba(16, 185, 129, 0.05) !important;
}

.dark .toaster .toast.error {
  box-shadow: 0 12px 30px -5px rgba(244, 63, 94, 0.2),
              0 0 20px 2px rgba(244, 63, 94, 0.05) !important;
}

/* Subtle icon entrance animation */
.dynamic-icon {
  animation: icon-pop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes icon-pop {
  0% {
    transform: scale(0.6) rotate(-15deg);
    opacity: 0;
  }
  100% {
    transform: scale(1) rotate(0deg);
    opacity: 1;
  }
}
</style>
