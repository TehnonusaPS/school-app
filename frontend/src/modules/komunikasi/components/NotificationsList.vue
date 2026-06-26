<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { getNotifications, markAsRead as apiMarkAsRead, markAllAsRead as apiMarkAllAsRead } from '@/services/notificationService'
import PageHeader from '@/components/page-header/PageHeader.vue'
import { toast } from 'vue-sonner'
import { Bell, BellOff, Megaphone, CheckCircle } from 'lucide-vue-next'
import { glassSlide, glassFade } from '@/config/motion'

const auth = useAuthStore()
const notifications = ref([])
const loading = ref(false)

const loadNotifications = async () => {
  loading.value = true
  try {
    const data = await getNotifications()
    notifications.value = data.notifications || []
  } catch (error) {
    console.error('Gagal memuat notifikasi:', error)
    toast.error('Gagal mengambil daftar notifikasi.')
  } finally {
    loading.value = false
  }
}

const handleMarkAsRead = async (notification) => {
  if (notification.read_at) return
  try {
    await apiMarkAsRead(notification.id)
    notification.read_at = new Date().toISOString()
    // Re-fetch unread count for global badge
    auth.fetchUnreadNotificationsCount()
    toast.success('Notifikasi ditandai sebagai dibaca')
  } catch (error) {
    console.error(error)
  }
}

const handleMarkAllAsRead = async () => {
  if (notifications.value.filter(n => !n.read_at).length === 0) return
  try {
    await apiMarkAllAsRead()
    notifications.value.forEach(n => {
      if (!n.read_at) n.read_at = new Date().toISOString()
    })
    auth.unreadNotificationsCount = 0
    toast.success('Semua notifikasi ditandai sebagai dibaca')
  } catch (error) {
    console.error(error)
  }
}

const formatTime = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Subscribe to real-time notification broadcasts
let unsubscribe = null
onMounted(() => {
  loadNotifications()
  unsubscribe = auth.onIncomingNotification((newNotif) => {
    // Prepend the new notification to the list in real-time
    notifications.value.unshift(newNotif)
  })
})

onUnmounted(() => {
  if (unsubscribe) unsubscribe()
})
</script>

<template>
  <div
    v-motion
    :initial="glassFade.initial"
    :visible-once="glassFade.visible"
    class="space-y-6 p-1"
  >
    <!-- Header -->
    <PageHeader
      title="Notifikasi"
      description="Pemberitahuan penting mengenai pengumuman dan informasi yayasan"
      :actions="notifications.filter(n => !n.read_at).length > 0 ? [
        {
          label: 'Tandai Semua Dibaca',
          icon: CheckCircle,
          variant: 'outline',
          click: handleMarkAllAsRead
        }
      ] : []"
    />

    <!-- List -->
    <div 
      v-motion
      :initial="glassSlide.initial"
      :visible-once="glassSlide.visible"
      class="space-y-4"
    >
      <div v-if="loading && notifications.length === 0" class="flex flex-col items-center justify-center p-12 text-muted-foreground">
        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-primary mr-3"></div>
        <p class="text-sm font-semibold mt-4">Memuat notifikasi...</p>
      </div>

      <div v-else-if="notifications.length === 0" class="flex flex-col items-center justify-center p-16 text-center bg-card border border-border/60 rounded-2xl shadow-xs">
        <BellOff class="h-12 w-12 text-muted-foreground/60 mb-3" />
        <h3 class="text-sm font-bold text-foreground">Tidak Ada Notifikasi</h3>
        <p class="text-xs text-muted-foreground max-w-xs mt-1">
          Semua pemberitahuan dan pengumuman baru dari yayasan akan muncul di sini.
        </p>
      </div>

      <div v-else class="grid grid-cols-1 gap-3">
        <div
          v-for="n in notifications"
          :key="n.id"
          class="p-5 bg-card border rounded-2xl transition-all duration-300 flex items-start gap-4"
          :class="[
            !n.read_at 
              ? 'border-primary/40 shadow-md shadow-primary/5 hover:border-primary/60 bg-primary/[0.01]' 
              : 'border-border/60 hover:border-border hover:shadow-xs'
          ]"
        >
          <!-- Icon -->
          <div 
            class="p-3 rounded-full shrink-0"
            :class="!n.read_at ? 'bg-primary/20 text-primary' : 'bg-muted text-muted-foreground'"
          >
            <Megaphone class="h-5 w-5" />
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0 space-y-1 text-left">
            <div class="flex items-center justify-between gap-4">
              <h4 class="text-sm font-extrabold leading-snug text-foreground flex items-center gap-2">
                {{ n.title }}
                <span 
                  v-if="!n.read_at" 
                  class="h-2 w-2 rounded-full bg-red-500 inline-block animate-pulse"
                  title="Belum Dibaca"
                ></span>
              </h4>
              <span class="text-[10px] text-muted-foreground/75 font-mono font-medium whitespace-nowrap">
                {{ formatTime(n.created_at) }}
              </span>
            </div>
            
            <p class="text-xs leading-relaxed text-muted-foreground pr-4 whitespace-pre-wrap">
              {{ n.content }}
            </p>

            <div class="pt-2 flex items-center justify-between">
              <span class="text-[10px] bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider">
                {{ n.data?.category || 'UMUM' }}
              </span>
              <button
                v-if="!n.read_at"
                @click="handleMarkAsRead(n)"
                class="text-[11px] text-primary hover:text-primary/80 font-bold flex items-center gap-1 transition-colors"
              >
                <CheckCircle class="size-3.5" />
                Tandai dibaca
              </button>
              <span v-else class="text-[10px] text-muted-foreground flex items-center gap-1 font-semibold">
                <CheckCircle class="size-3 text-emerald-500" />
                Sudah dibaca
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
