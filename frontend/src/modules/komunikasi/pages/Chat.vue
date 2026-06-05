<script setup>
import { computed, ref, watch } from 'vue'
import { Check, CheckCheck, Search } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import Avatar from '@/components/ui/avatar/Avatar.vue'
import { useAuthStore } from '@/stores/authStore'

const auth = useAuthStore()
const currentRole = computed(() => {
  const role = auth.user?.role || 'guru'
  if (role === 'wali_kelas') return 'guru'
  if (role === 'orang_tua') return 'siswa'
  return role
})
const isStudent = computed(() => currentRole.value === 'siswa')

const filters = [
  { label: 'Semua', value: 'all' },
  { label: 'Orang Tua', value: 'orang_tua' },
  { label: 'Siswa', value: 'siswa' }
]

const conversationsByRole = {
  guru: [
    {
      id: 1,
      name: 'Bpk. Budi Santoso',
      role: 'orang_tua',
      roleLabel: 'Orang Tua',
      detail: 'Andi - 3 C',
      preview: 'Selamat pagi, Pak Guru.',
      unread: 2,
      time: '13:14',
      messages: [
        {
          id: 1,
          from: 'other',
          text: 'Selamat pagi, Pak Guru. Saya ingin menanyakan terkait jadwal ujian praktek biologi minggu depan, apakah ada perubahan jadwal?',
          time: '10:00'
        },
        {
          id: 2,
          from: 'me',
          text: 'Selamat pagi, Pak Budi. Untuk jadwal ujian praktik biologi tetap sesuai rencana yaitu hari Kamis jam 08.00. Mohon Andi dipersiapkan dengan baik ya, Pak.',
          time: '10:14',
          status: 'read'
        },
        {
          id: 3,
          from: 'other',
          text: 'Baik, terima kasih atas informasinya, Pak. Nanti saya sampaikan ke Andi langsung saat ia pulang.',
          time: '12:24'
        },
        {
          id: 4,
          from: 'me',
          text: 'Sama - Sama',
          time: '13:14',
          status: 'sent'
        }
      ]
    },
    {
      id: 2,
      name: 'Andi Ahmad',
      role: 'siswa',
      roleLabel: 'Siswa',
      detail: 'Andi - 3 C',
      preview: 'Terima kasih informasinya.',
      unread: 0,
      time: '17:14',
      messages: [
        {
          id: 1,
          from: 'other',
          text: 'Selamat pagi, Pak Guru. Saya ingin menanyakan terkait Tugas praktek biologi, untuk materi yang akan diuji apa saja ya?',
          time: '09:14'
        },
        {
          id: 2,
          from: 'me',
          text: 'Selamat pagi, Untuk tugas praktek biologi materi yang akan diuji dari Bab I - Bab III, Mohon Andi dipersiapkan dengan baik ya, Pak.',
          time: '10:14',
          status: 'read'
        },
        {
          id: 3,
          from: 'other',
          text: 'Baik, terima kasih atas informasinya, Pak.',
          time: '12:24'
        },
        {
          id: 4,
          from: 'me',
          text: 'Sama - sama',
          time: '17:14',
          status: 'read'
        }
      ]
    }
  ],
  siswa: [
    {
      id: 1,
      name: 'Bpk. Budi Santoso',
      role: 'guru',
      roleLabel: 'Guru',
      detail: 'Biologi',
      preview: 'Selamat pagi, Pak Guru.',
      unread: 2,
      time: '13:14',
      messages: [
        {
          id: 1,
          from: 'other',
          text: 'Selamat pagi, Pak Guru. Saya ingin menanyakan terkait jadwal ujian praktik biologi minggu depan, apakah ada perubahan jadwal?',
          time: '10:00'
        },
        {
          id: 2,
          from: 'me',
          text: 'Selamat pagi, Pak Budi. Untuk jadwal ujian praktik biologi tetap sesuai rencana yaitu hari Kamis jam 08.00. Mohon Andi dipersiapkan dengan baik ya, Pak.',
          time: '10:14',
          status: 'read'
        },
        {
          id: 3,
          from: 'other',
          text: 'Baik, terima kasih atas informasinya, Pak. Nanti saya sampaikan ke Andi langsung saat ia pulang.',
          time: '12:24'
        },
        {
          id: 4,
          from: 'me',
          text: 'Sama - Sama',
          time: '13:14',
          status: 'sent'
        }
      ]
    },
    {
      id: 2,
      name: 'Ibu Dewi Sartika',
      role: 'guru',
      roleLabel: 'Guru',
      detail: 'Matematika',
      preview: 'Mohon cek latihan nomor 3.',
      unread: 0,
      time: '17:14',
      messages: [
        {
          id: 1,
          from: 'other',
          text: 'Andi, mohon cek latihan nomor 3 ya. Ada yang perlu dibetulkan.',
          time: '09:10'
        },
        {
          id: 2,
          from: 'me',
          text: 'Baik Bu, nanti saya kirim ulang jawabannya.',
          time: '09:18',
          status: 'sent'
        }
      ]
    },
    {
      id: 3,
      name: 'Ibu Fatimah Azzahra',
      role: 'guru',
      roleLabel: 'Guru',
      detail: 'Bahasa Inggris',
      preview: 'Terima kasih ya.',
      unread: 0,
      time: '17:14',
      messages: [
        {
          id: 1,
          from: 'other',
          text: 'Andi, jangan lupa presentasi hari Jumat ya.',
          time: '08:44'
        },
        {
          id: 2,
          from: 'me',
          text: 'Siap Bu, terima kasih infonya.',
          time: '08:50',
          status: 'read'
        }
      ]
    }
  ]
}

const activeFilter = ref('all')
const searchQuery = ref('')
const newMessage = ref('')
const activeConversationId = ref(conversationsByRole[currentRole.value]?.[0]?.id || null)

const filterOptions = computed(() => (isStudent.value ? [] : filters))

const conversations = computed(() => conversationsByRole[currentRole.value] || [])

const filteredConversations = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()

  return conversations.value.filter(convo => {
    const matchesRole =
      isStudent.value || activeFilter.value === 'all' || convo.role === activeFilter.value
    const matchesQuery =
      !query ||
      convo.name.toLowerCase().includes(query) ||
      convo.preview.toLowerCase().includes(query) ||
      convo.detail.toLowerCase().includes(query)

    return matchesRole && matchesQuery
  })
})

const activeConversation = computed(() => {
  return (
    conversations.value.find(convo => convo.id === activeConversationId.value) ||
    filteredConversations.value[0] ||
    null
  )
})

const panelTitle = computed(() => (isStudent.value ? 'Komunikasi dengan Guru' : 'Komunikasi'))
const panelDescription = computed(() =>
  isStudent.value
    ? 'Lihat dan balas pesan dari guru secara rapi dan cepat.'
    : 'Kelola percakapan dengan guru dan siswa dari satu tempat.'
)

watch(
  filteredConversations,
  list => {
    if (!list.length) return

    const stillVisible = list.some(convo => convo.id === activeConversationId.value)
    if (!stillVisible) {
      activeConversationId.value = list[0].id
    }
  },
  { immediate: true }
)

function selectConversation(convo) {
  activeConversationId.value = convo.id
}

function send() {
  if (!newMessage.value.trim() || !activeConversation.value) return

  activeConversation.value.messages.push({
    id: Date.now(),
    from: 'me',
    text: newMessage.value.trim(),
    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    status: 'sent'
  })
  activeConversation.value.preview = newMessage.value.trim()
  newMessage.value = ''
}
</script>

<template>
  <div class="flex flex-col gap-4 lg:h-[calc(100vh-11rem)] lg:flex-row">
    <aside class="w-full overflow-hidden rounded-xl border border-border bg-card shadow-sm lg:w-92">
      <div class="border-b border-border p-4 sm:p-5">
        <div>
          <h2 class="text-xl font-semibold tracking-tight text-foreground">{{ panelTitle }}</h2>
          <p class="text-sm text-muted-foreground">{{ panelDescription }}</p>
        </div>

        <div class="mt-4 relative">
          <Search
            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
          />
          <Input v-model="searchQuery" placeholder="Cari percakapan ...." class="h-10 pl-9" />
        </div>

        <div v-if="filterOptions.length" class="mt-4 flex flex-wrap gap-2">
          <Button
            v-for="filter in filterOptions"
            :key="filter.value"
            type="button"
            size="sm"
            :variant="activeFilter === filter.value ? 'default' : 'outline'"
            class="rounded-full"
            @click="activeFilter = filter.value"
          >
            {{ filter.label }}
          </Button>
        </div>
      </div>

      <div class="max-h-96 overflow-auto p-2 sm:max-h-112 lg:h-[calc(100%-8.5rem)]">
        <button
          v-for="convo in filteredConversations"
          :key="convo.id"
          type="button"
          class="flex w-full items-center gap-3 rounded-lg border border-transparent p-3 text-left transition-colors hover:border-border hover:bg-accent/60"
          :class="activeConversationId === convo.id ? 'border-border bg-accent/50' : ''"
          @click="selectConversation(convo)"
        >
          <Avatar class="h-11 w-11 shrink-0 ring-1 ring-border" />

          <div class="min-w-0 flex-1">
            <div class="flex items-start justify-between gap-2">
              <div class="truncate font-semibold text-foreground text-sm sm:text-base">{{ convo.name }}</div>
              <div class="flex items-center gap-1">
                <div class="text-[10px] text-muted-foreground font-medium font-mono">{{ convo.time }}</div>
                <span
                  v-if="convo.unread"
                  class="inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[9px] font-bold text-white"
                >
                  {{ convo.unread }}
                </span>
              </div>
            </div>

            <div class="mt-1.5 flex items-center gap-2 text-xs text-muted-foreground">
              <span class="rounded-md border border-border px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider bg-muted/40 text-foreground/85">
                {{ convo.roleLabel }}
              </span>
              <span class="text-[11px] font-medium">{{ convo.detail }}</span>
            </div>
          </div>
        </button>

        <div
          v-if="!filteredConversations.length"
          class="flex h-40 items-center justify-center rounded-lg border border-dashed border-border text-sm text-muted-foreground"
        >
          Tidak ada percakapan yang cocok.
        </div>
      </div>
    </aside>

    <section
      class="flex min-h-112 flex-1 flex-col overflow-hidden rounded-xl border border-border bg-card shadow-sm"
    >
      <header class="flex items-center gap-3 border-b border-border p-4 sm:p-5">
        <Avatar class="h-11 w-11 ring-1 ring-border" />
        <div class="min-w-0 flex-1 text-left">
          <div class="truncate text-base font-semibold text-foreground">
            {{ activeConversation?.name || 'Pilih percakapan' }}
          </div>
          <div v-if="activeConversation" class="mt-1 flex items-center gap-2 text-xs text-muted-foreground">
            <span class="rounded-md border border-border px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider bg-muted/40 text-foreground/85">
              {{ activeConversation.roleLabel }}
            </span>
            <span class="text-[11px] font-medium">{{ activeConversation.detail }}</span>
          </div>
        </div>
      </header>

      <div class="flex-1 overflow-auto p-4 sm:p-6">
        <template v-if="activeConversation">
          <div class="space-y-5">
            <div
              v-for="message in activeConversation.messages"
              :key="message.id"
              class="flex flex-col gap-1 w-full"
              :class="message.from === 'me' ? 'items-end' : 'items-start'"
            >
              <div class="flex items-start gap-3 max-w-[85%] sm:max-w-[75%]" :class="message.from === 'me' ? 'justify-end' : ''">
                <!-- Avatar for other sender -->
                <Avatar
                  v-if="message.from !== 'me'"
                  class="h-9 w-9 shrink-0 ring-1 ring-border self-start"
                />

                <div class="flex flex-col gap-1 w-full">
                  <!-- Message Bubble Box -->
                  <div
                    class="rounded-xl border px-4 py-3 text-sm leading-6 shadow-xs text-left"
                    :class="
                      message.from === 'me'
                        ? 'border-primary/30 bg-primary/10 text-foreground'
                        : 'border-border bg-muted/40 text-foreground'
                    "
                  >
                    <p class="whitespace-pre-line">{{ message.text }}</p>
                  </div>

                  <!-- Time & Status below message box -->
                  <div
                    class="flex items-center gap-1.5 text-[10px] font-medium text-muted-foreground px-1 font-mono"
                    :class="message.from === 'me' ? 'justify-end' : 'justify-start'"
                  >
                    <span>{{ message.time }}</span>
                    <template v-if="message.from === 'me'">
                      <CheckCheck
                        v-if="message.status === 'read'"
                        class="h-3 w-3 text-emerald-500"
                      />
                      <Check
                        v-else-if="message.status === 'sent'"
                        class="h-3 w-3 text-muted-foreground/80"
                      />
                    </template>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>

        <div
          v-else
          class="flex h-full items-center justify-center rounded-lg border border-dashed border-border text-sm text-muted-foreground"
        >
          Pilih percakapan untuk melihat isi chat.
        </div>
      </div>

      <footer class="border-t border-border bg-background p-4 sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row">
          <Input v-model="newMessage" placeholder="Tulis pesan..." class="h-11 flex-1" />
          <Button class="h-11 shrink-0 px-6" @click="send">Kirim</Button>
        </div>
      </footer>
    </section>
  </div>
</template>
