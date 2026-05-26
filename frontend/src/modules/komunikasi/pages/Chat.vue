<script setup>
import { computed, ref, watch } from 'vue'
import { Check, CheckCheck, Search } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import Avatar from '@/components/ui/avatar/Avatar.vue'
import { useAuthStore } from '@/stores/authStore'

const auth = useAuthStore()
const currentRole = computed(() => auth.user?.role || 'guru')
const isStudent = computed(() => currentRole.value === 'siswa')

const filters = [
  { label: 'Semua', value: 'all' },
  { label: 'Guru', value: 'guru' },
  { label: 'Siswa', value: 'siswa' }
]

const conversationsByRole = {
  guru: [
    {
      id: 1,
      name: 'Bpk. Budi Santoso',
      role: 'siswa',
      roleLabel: 'Siswa',
      detail: 'XI RPL 2',
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
          text: 'Selamat pagi, Pak Budi. Untuk jadwal ujian praktik biologi tetap sesuai rencana yaitu hari Kamis jam 08.00.',
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
      detail: 'X RPL 1',
      preview: 'Terima kasih informasinya.',
      unread: 0,
      time: '17:14',
      messages: [
        {
          id: 1,
          from: 'other',
          text: 'Pak, apakah tugas proyek hari ini dikumpulkan via kelas atau langsung ke guru?',
          time: '09:20'
        },
        {
          id: 2,
          from: 'me',
          text: 'Silakan dikumpulkan lewat kelas saja dulu, nanti saya cek satu per satu ya.',
          time: '09:26',
          status: 'sent'
        }
      ]
    },
    {
      id: 3,
      name: 'Bu Rina',
      role: 'guru',
      roleLabel: 'Guru',
      detail: 'Matematika',
      preview: 'Mohon cek data absensi ya Pak.',
      unread: 1,
      time: '17:14',
      messages: [
        {
          id: 1,
          from: 'other',
          text: 'Mohon cek data absensi kelas hari ini ya Pak.',
          time: '08:15'
        },
        {
          id: 2,
          from: 'me',
          text: 'Siap Bu, saya cek dan update setelah jam istirahat.',
          time: '08:21',
          status: 'read'
        }
      ]
    },
    {
      id: 4,
      name: 'Siti Aminah',
      role: 'siswa',
      roleLabel: 'Siswa',
      detail: 'Bahasa Inggris',
      preview: 'Terima kasih Pak.',
      unread: 0,
      time: '13:14',
      messages: [
        {
          id: 1,
          from: 'other',
          text: 'Pak, saya izin tanya materi yang kemarin sempat tertinggal.',
          time: '07:55'
        },
        {
          id: 2,
          from: 'me',
          text: 'Bisa, nanti saya kirim ringkasannya di grup kelas juga.',
          time: '08:03',
          status: 'sent'
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
              <div class="min-w-0">
                <div class="truncate font-medium text-foreground">{{ convo.name }}</div>
                <div class="truncate text-xs text-muted-foreground">{{ convo.preview }}</div>
              </div>
              <span
                v-if="convo.unread"
                class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary px-1.5 text-[11px] font-medium text-primary-foreground"
              >
                {{ convo.unread }}
              </span>
            </div>

            <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] text-muted-foreground">
              <span class="rounded-full border border-border px-2 py-0.5">{{
                convo.roleLabel
              }}</span>
              <span>{{ convo.detail }}</span>
            </div>
          </div>

          <div class="self-start text-[10px] text-muted-foreground">{{ convo.time }}</div>
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
        <div class="min-w-0 flex-1">
          <div class="flex flex-wrap items-center gap-2">
            <div class="truncate text-base font-semibold text-foreground">
              {{ activeConversation?.name || 'Pilih percakapan' }}
            </div>
            <span
              v-if="activeConversation"
              class="inline-flex rounded-full border border-border px-2 py-0.5 text-[11px] font-medium text-muted-foreground"
            >
              {{ activeConversation.roleLabel }}
            </span>
          </div>
          <div class="text-sm text-muted-foreground">
            {{ activeConversation ? activeConversation.detail : 'Belum ada percakapan aktif' }}
          </div>
        </div>
      </header>

      <div class="flex-1 overflow-auto p-4 sm:p-6">
        <template v-if="activeConversation">
          <div class="space-y-5">
            <div
              v-for="message in activeConversation.messages"
              :key="message.id"
              class="flex max-w-[85%] gap-3 sm:max-w-[75%]"
              :class="message.from === 'me' ? 'ml-auto justify-end' : 'justify-start'"
            >
              <Avatar
                v-if="message.from !== 'me'"
                class="mt-1 h-9 w-9 shrink-0 ring-1 ring-border"
              />

              <div
                class="max-w-full rounded-2xl border px-4 py-3 text-sm leading-6 shadow-sm"
                :class="
                  message.from === 'me'
                    ? 'border-primary/20 bg-primary text-primary-foreground'
                    : 'border-border bg-muted/40 text-foreground'
                "
              >
                <p class="whitespace-pre-line">{{ message.text }}</p>
                <div
                  class="mt-1 flex items-center gap-1 text-[11px] opacity-80"
                  :class="message.from === 'me' ? 'justify-end' : 'justify-start'"
                >
                  <span>{{ message.time }}</span>
                  <CheckCheck
                    v-if="message.from === 'me' && message.status === 'read'"
                    class="h-3.5 w-3.5"
                  />
                  <Check
                    v-else-if="message.from === 'me' && message.status === 'sent'"
                    class="h-3.5 w-3.5"
                  />
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
