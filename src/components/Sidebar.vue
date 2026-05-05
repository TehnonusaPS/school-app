<template>
  <div class="flex h-screen bg-gradient-to-br from-slate-50 to-blue-100 font-sans overflow-hidden">
    
    <!-- Sidebar -->
    <aside 
      :class="[
        'h-full transition-all duration-300 ease-in-out border-r border-white/30 bg-white/40 backdrop-blur-xl shadow-xl flex flex-col',
        isCollapsed ? 'w-20' : 'w-72'
      ]"
    >
      <!-- Logo -->
      <div class="p-6 flex items-center gap-4 border-b border-white/20 min-h-[80px]">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-900 text-white shadow-lg">
          <Layers class="h-6 w-6" />
        </div>

        <transition name="fade">
          <span v-if="!isCollapsed" class="text-xl font-bold text-slate-900 whitespace-nowrap">
            Nexus<span class="text-blue-900">App</span>
          </span>
        </transition>
      </div>

      <!-- Menu -->
      <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">
        <div v-for="item in navItems" :key="item.name">
          <button
            @click="activeItem = item.name"
            :class="[
              'w-full group flex items-center gap-4 px-3 py-3 rounded-xl transition-all duration-200 relative',
              activeItem === item.name
                ? 'bg-blue-900 text-white shadow-lg'
                : 'text-slate-600 hover:bg-white/60 hover:text-blue-900'
            ]"
          >
            <component :is="item.icon" class="h-6 w-6 shrink-0" />

            <transition name="fade">
              <span v-if="!isCollapsed" class="font-medium whitespace-nowrap">
                {{ item.name }}
              </span>
            </transition>

            <!-- Tooltip -->
            <div
              v-if="isCollapsed"
              class="absolute left-full ml-3 px-2 py-1 text-xs bg-slate-900 text-white rounded opacity-0 group-hover:opacity-100 whitespace-nowrap"
            >
              {{ item.name }}
            </div>
          </button>
        </div>
      </nav>

      <!-- Profile -->
      <div class="p-4 border-t border-white/20">
        <div :class="['flex items-center gap-3', isCollapsed && 'justify-center']">
          <img
            src="https://ui-avatars.com/api/?name=John+Doe"
            class="h-10 w-10 rounded-xl"
          />

          <div v-if="!isCollapsed" class="flex flex-col">
            <span class="text-sm font-bold">John Doe</span>
            <span class="text-xs text-slate-500">Admin</span>
          </div>
        </div>

        <button
          @click="isCollapsed = !isCollapsed"
          class="mt-4 w-full flex justify-center py-2 hover:bg-white/50 rounded-lg"
        >
          <ChevronRight v-if="isCollapsed" />
          <ChevronLeft v-else />
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col overflow-hidden">
      
      <!-- TOPBAR -->
      <header class="h-16 flex items-center justify-between px-6 bg-white/50 backdrop-blur border-b border-white/30">
        <h2 class="text-xl font-bold text-slate-800">
          {{ activeItem }}
        </h2>

        <div class="text-sm text-slate-500">
          {{ today }}
        </div>
      </header>

      <!-- CONTENT -->
      <div class="flex-1 p-6 overflow-y-auto">
        <div class="grid grid-cols-3 gap-4 mb-6">
          <div class="bg-white p-4 rounded-xl shadow">
            Total User: 12,840
          </div>
          <div class="bg-white p-4 rounded-xl shadow">
            Active: 458
          </div>
          <div class="bg-white p-4 rounded-xl shadow">
            Status: Aman
          </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow min-h-[300px] flex items-center justify-center text-slate-400">
          Konten {{ activeItem }}
        </div>
      </div>
    </main>

  </div>
</template>

<script setup>
import { ref, computed, markRaw } from 'vue'
import {
  LayoutDashboard,
  BarChart3,
  MessageSquare,
  Users,
  Settings,
  ChevronLeft,
  ChevronRight,
  Layers
} from 'lucide-vue-next'

const isCollapsed = ref(false)
const activeItem = ref('Dashboard')

const navItems = [
  { name: 'Dashboard', icon: markRaw(LayoutDashboard) },
  { name: 'Statistik', icon: markRaw(BarChart3) },
  { name: 'Pesan', icon: markRaw(MessageSquare) },
  { name: 'Tim', icon: markRaw(Users) },
  { name: 'Pengaturan', icon: markRaw(Settings) }
]

const today = computed(() =>
  new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long'
  })
)
</script>

<style>
body {
  margin: 0;
}

/* Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.2);
  border-radius: 10px;
}

/* Fade */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>