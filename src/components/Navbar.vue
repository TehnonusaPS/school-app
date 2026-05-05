<template>
  <nav class="sticky top-4 z-40 mx-auto w-[95%]">
    <div class="rounded-2xl border border-white/30 bg-white/40 px-6 py-3 shadow-lg backdrop-blur-md">
      <div class="flex items-center justify-between">
        
        <div class="flex items-center gap-8">
          <div class="flex items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-900 text-white shadow-md">
              <Layers class="h-5 w-5" />
            </div>
            <span class="hidden text-xl font-bold text-slate-900 md:block">
              Nexus<span class="text-blue-900">App</span>
            </span>
          </div>

          <div class="relative hidden sm:block">
            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500" />
            <input 
              type="text" 
              placeholder="Cari sesuatu..." 
              class="w-64 rounded-xl border border-white/40 bg-white/20 py-2 pl-10 pr-4 text-sm text-slate-900 outline-none transition-all focus:w-80 focus:border-blue-900/40 focus:ring-0"
            />
          </div>
        </div>

        <div class="flex items-center gap-3">
          <button class="relative rounded-xl p-2 text-slate-600 transition-colors hover:bg-white/60 hover:text-blue-900">
            <Bell class="h-5 w-5" />
            <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-red-500 border-2 border-white"></span>
          </button>

          <div class="h-6 w-[1px] bg-white/40 mx-2"></div>

          <div class="group relative">
            <button class="flex items-center gap-3 rounded-xl p-1 transition-all hover:bg-white/60">
              <div class="flex flex-col items-end hidden md:flex">
                <span class="text-xs font-bold text-slate-900 leading-tight">John Doe</span>
                <span class="text-[10px] text-slate-500">Super Admin</span>
              </div>
              <img 
                src="https://ui-avatars.com/api/?name=John+Doe&background=1e3a8a&color=fff" 
                alt="Avatar" 
                class="h-9 w-9 rounded-lg border border-white/50 shadow-sm"
              />
              <ChevronDown class="h-4 w-4 text-slate-500 transition-transform group-hover:rotate-180" />
            </button>

            <div class="invisible absolute right-0 mt-2 w-48 origin-top-right rounded-2xl border border-white/30 bg-white/80 p-2 shadow-xl backdrop-blur-xl transition-all opacity-0 group-hover:visible group-hover:opacity-100">
              <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-700 transition-colors hover:bg-blue-900 hover:text-white">
                <User class="h-4 w-4" /> Profil
              </a>
              <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-700 transition-colors hover:bg-blue-900 hover:text-white">
                <Settings class="h-4 w-4" /> Pengaturan
              </a>
              <div class="my-1 h-[1px] bg-slate-200/50"></div>
              <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm text-red-600 transition-colors hover:bg-red-50 hover:font-bold">
                <LogOut class="h-4 w-4" /> Keluar
              </a>
            </div>
          </div>

          <button class="ml-2 rounded-xl bg-blue-900 p-2 text-white md:hidden shadow-lg">
            <Menu class="h-5 w-5" />
          </button>
        </div>

      </div>
    </div>
  </nav>
</template>

<script setup>
import { 
  Search, 
  Bell, 
  ChevronDown, 
  User, 
  Settings, 
  LogOut, 
  Layers,
  Menu
} from 'lucide-vue-next'
</script>

<style scoped>
/* Animasi halus untuk dropdown */
.group:hover .absolute {
  transform: translateY(0);
}

input::placeholder {
  color: #64748b;
  font-weight: 400;
}
</style>