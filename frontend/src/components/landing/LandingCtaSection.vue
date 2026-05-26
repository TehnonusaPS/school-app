<script setup>
import LandingContactInfo from './LandingContactInfo.vue'
import LandingContactForm from './LandingContactForm.vue'
</script>

<template>
  <section id="kontak" class="relative px-6 py-24 bg-gradient-to-b from-transparent to-slate-50/30 dark:to-slate-950/5">
    <div class="mx-auto max-w-6xl">
      <!-- Section Header -->
      <div class="mx-auto max-w-2xl text-center space-y-3 mb-14">
        <span class="inline-flex items-center rounded-full border border-primary/20 bg-primary/5 px-4 py-1.5 text-xs font-semibold text-primary dark:border-primary/30 dark:bg-primary/10 dark:text-primary-foreground/90">
          Hubungi Kami
        </span>
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">
          Siap Bergabung Bersama Kami?
        </h2>
        <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 leading-relaxed">
          Isi formulir di bawah dan tim kami akan menghubungi Anda untuk demonstrasi gratis sesuai kebutuhan sekolah atau yayasan Anda.
        </p>
      </div>

      <!-- Main Layout Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        <!-- Left Side: Contact Info & Banner -->
        <div class="lg:col-span-5">
          <LandingContactInfo />
        </div>

        <!-- Right Side: The Premium Form -->
        <div class="lg:col-span-7">
          <LandingContactForm />
        </div>
      </div>
    </div>
  </section>
</template>
