<script setup>
import { computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppSidebar from '@/components/AppSidebar.vue'
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator
} from '@/components/ui/breadcrumb'
import { Separator } from '@/components/ui/separator'
import { SidebarInset, SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Bell, Search, UserPlus, CreditCard, MessageCircle, MessageSquare, Sun, Moon, Monitor } from 'lucide-vue-next'
import { ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { sidebarSlide, topbarSlide } from '@/config/motion'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger
} from '@/components/ui/dropdown-menu'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const isLoginTransition = computed(() => auth.isJustLoggedIn)
const sidebarDelay = computed(() => isLoginTransition.value ? 1000 : 100)
const topbarDelay = computed(() => isLoginTransition.value ? 1200 : 200)

const chatRoute = computed(() => {
  const role = auth.user?.role
  if (role === 'guru') return '/komunikasi/guru/chat'
  if (role === 'wali_kelas') return '/komunikasi/wali-kelas/chat'
  if (role === 'siswa') return '/komunikasi/siswa/chat'
  if (role === 'orang_tua') return '/komunikasi/orang-tua/chat'
  return '/komunikasi/chat'
})

// --- Color Mode Logic (System/Light/Dark) ---
const colorMode = ref('system')

const applyColorMode = (mode) => {
  if (mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
}

const cycleColorMode = () => {
  const modes = ['system', 'light', 'dark']
  const nextMode = modes[(modes.indexOf(colorMode.value) + 1) % modes.length]
  colorMode.value = nextMode
  localStorage.setItem('theme', nextMode)
  applyColorMode(nextMode)
}

onMounted(() => {
  if (auth.isJustLoggedIn) {
    setTimeout(() => {
      auth.isJustLoggedIn = false
    }, 1200)
  }

  // Restore Color Mode (Terang/Gelap/Sistem)
  const savedTheme = localStorage.getItem('theme') || 'system'
  colorMode.value = savedTheme
  applyColorMode(savedTheme)

  // Initialize global unread chat count & WebSocket listener
  auth.fetchUnreadCount()
  auth.setupGlobalChatListener()
})

// Fungsi untuk menelusuri hierarki breadcrumb secara rekursif
const breadcrumbs = computed(() => {
  const crumbs = []
  let current = route

  crumbs.unshift({
    title: current.meta.title || 'Halaman',
    active: true
  })

  let parentTitle = current.meta.parent
  while (parentTitle) {
    const parentRoute = router.getRoutes().find(r => r.meta.title === parentTitle)
    if (parentRoute) {
      crumbs.unshift({
        title: parentRoute.meta.title,
        path: parentRoute.path,
        active: false
      })
      parentTitle = parentRoute.meta.parent
    } else {
      crumbs.unshift({
        title: parentTitle,
        active: false
      })
      parentTitle = null
    }
  }

  return crumbs
})

// Data Dummy Notifikasi
const notifications = [
  {
    id: 1,
    title: 'Siswa Baru Mendaftar',
    desc: 'Budi Santoso telah mendaftar di Kelas 1-A',
    time: '2 menit yang lalu',
    icon: UserPlus,
    color: 'text-blue-500'
  },

  {
    id: 2,
    title: 'Pembayaran Berhasil',
    desc: 'SPP bulan Mei untuk Siti Aminah sudah lunas',
    time: '1 jam yang lalu',
    icon: CreditCard,
    color: 'text-green-500'
  },

  {
    id: 3,
    title: 'Pesan Baru',
    desc: 'Ada pesan baru dari orang tua murid Ani',
    time: '3 jam yang lalu',
    icon: MessageCircle,
    color: 'text-orange-500'
  }
]
</script>

<template>
  <SidebarProvider>
    <AppSidebar />
    <SidebarInset class="min-w-0 overflow-x-clip">
      <div class="sticky top-0 z-50 w-full pt-2">
        <header
          v-motion
          :initial="topbarSlide.initial"
          :enter="{ ...topbarSlide.enter, transition: { ...topbarSlide.enter.transition, delay: topbarDelay } }"
          :class="[
            'flex h-14 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 glass-mini mx-2 px-4 justify-between',
            auth.isLoggingOut ? 'topbar-exit-active' : ''
          ]"
        >
          <!-- SISI KIRI: Sidebar Trigger & Breadcrumb -->
          <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <Separator orientation="vertical" class="mr-2 h-4 my-auto !self-center" />
            <Breadcrumb>
              <BreadcrumbList>
                <template v-for="(crumb, index) in breadcrumbs" :key="index">
                  <BreadcrumbItem>
                    <BreadcrumbLink v-if="!crumb.active" :href="crumb.path || '#'">
                      {{ crumb.title }}
                    </BreadcrumbLink>
                    <BreadcrumbPage v-else>
                      {{ crumb.title }}
                    </BreadcrumbPage>
                  </BreadcrumbItem>
                  <BreadcrumbSeparator v-if="index < breadcrumbs.length - 1" />
                </template>
              </BreadcrumbList>
            </Breadcrumb>
          </div>

          <!-- SISI KANAN: Search & Notifications -->
          <div class="flex items-center gap-4">
            <div class="relative hidden md:block w-64">
              <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                type="search"
                name="nav-search"
                autocomplete="off"
                placeholder="Cari sesuatu..."
                class="pl-9 h-9 w-full bg-muted/50 focus-visible:bg-background transition-colors"
              />
            </div>

            <!-- Chat Icon (Guru, Siswa, Wali Kelas, & Orang Tua) -->
            <div v-if="auth.user?.role === 'guru' || auth.user?.role === 'siswa' || auth.user?.role === 'wali_kelas' || auth.user?.role === 'orang_tua'" class="relative">
              <Button
                variant="ghost"
                size="icon"
                class="rounded-full hover:bg-muted"
                @click="router.push(chatRoute)"
              >
                <MessageSquare class="h-5 w-5" />
              </Button>
              <span
                v-if="auth.unreadCount > 0"
                class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-0.5 text-[9px] font-extrabold text-white"
              >
                {{ auth.unreadCount }}
              </span>
            </div>

            <!-- Notification Dropdown -->
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <div class="relative cursor-pointer">
                  <Button variant="ghost" size="icon" class="rounded-full hover:bg-muted">
                    <Bell class="h-5 w-5" />
                  </Button>
                  <span class="absolute top-2 right-2 flex h-2 w-2">
                    <span
                      class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"
                    ></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                  </span>
                </div>
              </DropdownMenuTrigger>

              <DropdownMenuContent class="w-80" align="end">
                <DropdownMenuLabel class="font-bold flex justify-between items-center">
                  Notifikasi Terbaru
                  <span class="text-[10px] bg-primary/10 text-primary px-2 py-0.5 rounded-full"
                    >3 Baru</span
                  >
                </DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuGroup>
                  <DropdownMenuItem v-for="n in notifications" :key="n.id" class="p-3 cursor-pointer">
                    <div class="flex items-start gap-3">
                      <div class="mt-1 p-2 bg-muted rounded-full" :class="n.color">
                        <component :is="n.icon" class="h-4 w-4" />
                      </div>
                      <div class="flex flex-col gap-1">
                        <p class="text-sm font-semibold leading-none">{{ n.title }}</p>
                        <p class="text-xs text-muted-foreground line-clamp-2">{{ n.desc }}</p>
                        <p class="text-[10px] text-muted-foreground/70">{{ n.time }}</p>
                      </div>
                    </div>
                  </DropdownMenuItem>
                </DropdownMenuGroup>

                <DropdownMenuSeparator />
                <DropdownMenuItem
                  class="justify-center text-primary font-medium text-xs cursor-pointer"
                >
                  Lihat Semua Notifikasi
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>

            <!-- Mode Tampilan (Dark/Light/Sistem) -->
            <Button
              variant="ghost"
              size="icon"
              class="rounded-full hover:bg-muted"
              @click="cycleColorMode"
              title="Ganti Mode Tampilan"
            >
              <Monitor v-if="colorMode === 'system'" class="h-5 w-5" />
              <Sun v-else-if="colorMode === 'light'" class="h-5 w-5" />
              <Moon v-else class="h-5 w-5" />
            </Button>
          </div>
        </header>
      </div>

      <div
        :class="[
          'flex flex-1 flex-col gap-4 p-6 text-left transition-all duration-500 ease-[cubic-bezier(0.76,0,0.24,1)] min-w-0',
          auth.isLoggingOut ? 'content-exit-active' : ''
        ]"
      >
        <div class="w-full min-w-0 flex-1">
          <router-view />
        </div>
      </div>
    </SidebarInset>
  </SidebarProvider>
</template>
