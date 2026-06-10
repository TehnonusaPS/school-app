<script setup>
import { computed, ref } from 'vue'
import { Eye, Pencil, Trash2 } from 'lucide-vue-next'
import DataTableToolbar from './DataTableToolbar.vue'
import DataTablePagination from './DataTablePagination.vue'
import BaseDataTable from './BaseDataTable.vue'
import { Card } from '@/components/ui/card'
import {
  AlertDialog,
  AlertDialogContent,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogCancel,
  AlertDialogAction
} from '@/components/ui/alert-dialog'

const props = defineProps({
  columns: Array,
  items: Array,
  filters: Array,
  actions: Array,
  rowActions: Array,
  filterValues: {
    type: Object,
    default: () => ({})
  },
  from: Number,
  to: Number,
  total: Number,
  page: Number,
  perPage: {
    type: Number,
    default: 10
  },
  minHeight: {
    type: String,
    default: '260px'
  },
  // Built-in row action handlers — otomatis tampilkan tombol jika di-pass
  // Gunakan @view="..." / @edit="..." / @delete="..." di parent
  onView: Function,
  onEdit: Function,
  onDelete: Function,
  // Menentukan teks label item di dialog konfirmasi hapus:
  //   string   → nama field, misal 'nama' atau 'username'
  //   function → (item) => string, misal item => `${item.nama} (${item.nisn})`
  //   default  → auto-detect field: nama, name, username, title, label
  deleteLabel: [String, Function],
  illustration: {
    type: String,
    default: 'textbook'
  }
})

const emit = defineEmits(['update:page', 'update:filterValues', 'update:perPage'])

const localFilterValues = computed({
  get: () => props.filterValues,
  set: val => emit('update:filterValues', val)
})

// ── Delete confirmation dialog ────────────────────────────────────────────────
const deleteDialogOpen = ref(false)
const pendingDeleteItem = ref(null)

const requestDelete = item => {
  pendingDeleteItem.value = item
  deleteDialogOpen.value = true
}

const deleteItemLabel = computed(() => {
  const item = pendingDeleteItem.value
  if (!item) return ''
  if (typeof props.deleteLabel === 'function') return props.deleteLabel(item)
  if (typeof props.deleteLabel === 'string') return item[props.deleteLabel] ?? ''
  // Auto-detect field umum jika deleteLabel tidak diset
  return item.nama ?? item.name ?? item.username ?? item.title ?? item.label ?? ''
})

const confirmDelete = () => {
  if (props.onDelete && pendingDeleteItem.value) {
    props.onDelete(pendingDeleteItem.value.id, pendingDeleteItem.value)
  }
  deleteDialogOpen.value = false
  pendingDeleteItem.value = null
}

const cancelDelete = () => {
  deleteDialogOpen.value = false
  pendingDeleteItem.value = null
}
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Built-in default row actions.
 * Tombol muncul otomatis sesuai handler yang dipasang di parent:
 *   @view="..."   → tombol Detail (Eye)
 *   @edit="..."   → tombol Edit (Pencil)
 *   @delete="..." → tombol Hapus (Trash2), membuka konfirmasi dialog terlebih dahulu
 *
 * Handler @delete menerima (id, item):
 *   @delete="(id, item) => handleDelete(id)"
 */
const defaultRowActions = computed(() => {
  const actions = []
  if (props.onView)
    actions.push({ label: 'Detail', icon: Eye, click: item => props.onView(item.id, item) })
  if (props.onEdit) actions.push({ label: 'Edit', icon: Pencil, click: props.onEdit })
  if (props.onDelete) actions.push({ label: 'Hapus', icon: Trash2, click: requestDelete })
  return actions
})

// Pakai custom rowActions jika disediakan, selain itu pakai default built-in
const resolvedRowActions = computed(() =>
  props.rowActions?.length ? props.rowActions : defaultRowActions.value
)
</script>

<template>
  <Card
    v-motion
    class="w-full min-w-0 relative overflow-hidden"
    :initial="{ opacity: 0, y: 20 }"
    :visible-once="{
      opacity: 1,
      y: 0,
      transition: {
        type: 'spring',
        stiffness: 90,
        damping: 15,
        mass: 0.8
      }
    }"
  >
    <!-- Background Watermarks -->
    <div
      v-if="illustration"
      class="absolute top-[-45px] right-[-50px] size-52 rotate-[-15deg] opacity-[0.15] dark:opacity-[0.22] pointer-events-none select-none bg-primary z-0 watermark-illustration"
      style="
        mask-size: contain;
        -webkit-mask-size: contain;
        mask-repeat: no-repeat;
        -webkit-mask-repeat: no-repeat;
        mask-position: center;
        -webkit-mask-position: center;
      "
      :style="{
        maskImage: `url(/images/illustrations/${illustration}.png)`,
        webkitMaskImage: `url(/images/illustrations/${illustration}.png)`
      }"
    />

    <DataTableToolbar
      class="relative z-10"
      :filters="filters"
      :actions="actions"
      v-model:filterValues="localFilterValues"
    />

    <BaseDataTable
      class="relative z-10"
      :columns="columns"
      :items="items"
      :row-actions="resolvedRowActions"
      :row-number-offset="(from ?? 1) - 1"
      :min-height="minHeight"
      :per-page="perPage"
    >
      <!-- Forward all dynamic cell slots -->
      <template
        v-for="column in columns"
        #[`cell-${column.key}`]="slotProps"
      >
        <slot
          :name="`cell-${column.key}`"
          v-bind="slotProps"
        />
      </template>
    </BaseDataTable>

    <DataTablePagination
      class="relative z-10"
      :from="from"
      :to="to"
      :total="total"
      :page="page"
      :per-page="perPage"
      @update:page="emit('update:page', $event)"
      @update:perPage="emit('update:perPage', $event)"
    />

    <!-- Delete Confirmation Dialog -->
    <AlertDialog :open="deleteDialogOpen">
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Konfirmasi Hapus</AlertDialogTitle>
          <AlertDialogDescription>
            Apakah Anda yakin ingin menghapus
            <span
              v-if="deleteItemLabel"
              class="font-semibold text-foreground"
            >
              {{ deleteItemLabel }}
            </span>
            <span v-else>data ini</span>
            ? Tindakan ini tidak dapat dibatalkan.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel @click="cancelDelete">Batal</AlertDialogCancel>
          <AlertDialogAction
            class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
            @click="confirmDelete"
          >
            Hapus
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  </Card>
</template>
