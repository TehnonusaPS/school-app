<script setup>
import { ref, watch, computed } from 'vue'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription } from '@/components/ui/sheet'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import { Camera } from 'lucide-vue-next'

const props = defineProps({
  open: {
    type: Boolean,
    required: true
  },
  item: {
    type: Object,
    default: null
  },
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  },
  sections: {
    type: Array,
    default: () => []
  },
  avatarKey: {
    type: [String, Boolean],
    default: 'foto'
  },
  titleKey: {
    type: String,
    default: 'nama'
  },
  allowAvatarEdit: {
    type: Boolean,
    default: true
  },
  submitLabel: {
    type: String,
    default: 'Simpan'
  },
  cancelLabel: {
    type: String,
    default: 'Batal'
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:open', 'submit', 'cancel'])

const isOpen = computed({
  get: () => props.open,
  set: val => emit('update:open', val)
})

const localForm = ref({})
const fileInput = ref(null)

watch(
  () => props.open,
  (newOpen) => {
    if (newOpen) {
      const form = {}
      props.sections.forEach(section => {
        section.fields.forEach(field => {
          if (field.key) {
            form[field.key] = props.item ? (props.item[field.key] ?? '') : ''
          }
        })
      })
      if (props.avatarKey && typeof props.avatarKey === 'string') {
        form[props.avatarKey] = props.item ? (props.item[props.avatarKey] ?? '') : ''
      }
      if (props.item && props.item.id !== undefined) {
        form.id = props.item.id
      }
      localForm.value = form
    }
  },
  { immediate: true }
)

const resolvedAvatarFallback = computed(() => {
  const nameVal = localForm.value[props.titleKey] || ''
  if (!nameVal) return '?'
  const parts = String(nameVal).trim().split(/\s+/)
  if (parts.length === 0) return '?'
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase()
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
})

const showAvatar = computed(() => {
  if (props.avatarKey === false || props.avatarKey === 'false') return false
  return !!props.avatarKey
})

const triggerFileInput = () => {
  if (props.allowAvatarEdit && fileInput.value) {
    fileInput.value.click()
  }
}

const handleFileChange = (event) => {
  const file = event.target.files?.[0]
  if (file) {
    const previewUrl = URL.createObjectURL(file)
    localForm.value[props.avatarKey] = previewUrl
    localForm.value._avatarFile = file
  }
}

const handleCancel = () => {
  emit('cancel')
  isOpen.value = false
}

const handleSubmit = () => {
  emit('submit', { ...localForm.value })
}
</script>

<template>
  <Sheet v-model:open="isOpen">
    <SheetContent :show-close-button="false" class="gap-2 h-full flex flex-col">
      <slot name="header">
        <SheetHeader class="border-b border-border pb-3 shrink-0">
          <div class="flex items-center gap-4">
            <!-- Dynamic Avatar Uploader -->
            <div
              v-if="showAvatar"
              class="relative group cursor-pointer shrink-0 rounded-full select-none"
              @click="triggerFileInput"
            >
              <Avatar class="size-16 border-2 border-border/80 shrink-0">
                <AvatarImage
                  v-if="localForm[avatarKey]"
                  :src="localForm[avatarKey]"
                  :alt="localForm[titleKey] || 'Avatar'"
                />
                <AvatarFallback class="text-lg font-bold">
                  {{ resolvedAvatarFallback }}
                </AvatarFallback>
              </Avatar>
              <!-- Hover Edit Camera Overlay -->
              <div
                v-if="allowAvatarEdit"
                class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200"
              >
                <Camera class="size-5 text-white" />
              </div>
              <input
                v-if="allowAvatarEdit"
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleFileChange"
              />
            </div>

            <div class="flex-1 min-w-0 space-y-1 text-left">
              <SheetTitle class="text-base font-bold text-foreground truncate">
                {{ title }}
              </SheetTitle>
              <SheetDescription v-if="description" class="text-xs text-muted-foreground">
                {{ description }}
              </SheetDescription>
            </div>
          </div>
        </SheetHeader>
      </slot>

      <!-- Scrollable Form Body -->
      <div class="overflow-y-auto pr-1 flex-1 no-scrollbar space-y-6 py-2">
        <slot :form="localForm">
          <Accordion type="multiple" class="w-full" :default-value="sections.map(s => s.id)">
            <AccordionItem
              v-for="section in sections"
              :key="section.id"
              :value="section.id"
            >
              <AccordionTrigger class="text-sm font-semibold">
                {{ section.title }}
              </AccordionTrigger>
              <AccordionContent class="space-y-4 pt-3">
                <slot :name="`section-${section.id}`" :section="section" :form="localForm">
                  <div
                    v-for="field in section.fields"
                    :key="field.label"
                    class="grid gap-1.5"
                  >
                    <slot :name="`field-${field.key || field.label.toLowerCase().replace(/\s+/g, '-')}`" :field="field" :form="localForm">
                      <div class="flex items-center gap-1.5">
                        <component
                          v-if="field.icon"
                          :is="field.icon"
                          class="size-3.5 text-muted-foreground"
                        />
                        <label class="text-xs font-semibold text-muted-foreground">
                          {{ field.label }}
                        </label>
                      </div>
                      <Textarea
                        v-if="field.textarea"
                        v-model="localForm[field.key]"
                        class="min-h-16 bg-background border-input focus-visible:ring-1 focus-visible:ring-ring text-sm"
                        :placeholder="field.placeholder || ('Masukkan ' + field.label)"
                      />
                      <Input
                        v-else
                        v-model="localForm[field.key]"
                        class="h-8 bg-background border-input focus-visible:ring-1 focus-visible:ring-ring text-sm"
                        :placeholder="field.placeholder || ('Masukkan ' + field.label)"
                      />
                    </slot>
                  </div>
                </slot>
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </slot>
      </div>

      <!-- Footer Action Buttons -->
      <div class="border-t border-border/80 px-6 py-4 flex items-center justify-end gap-3 shrink-0 bg-card/40 dark:bg-card/25 backdrop-blur-md -mx-6 -mb-6 mt-auto">
        <slot name="actions" :form="localForm" :submit="handleSubmit" :cancel="handleCancel">
          <Button
            variant="outline"
            size="sm"
            @click="handleCancel"
          >
            {{ cancelLabel }}
          </Button>
          <Button
            size="sm"
            :loading="loading"
            @click="handleSubmit"
          >
            {{ submitLabel }}
          </Button>
        </slot>
      </div>
    </SheetContent>
  </Sheet>
</template>
