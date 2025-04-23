<template>
    <div class="min-h-screen bg-gray-50 text-gray-900">
      <header class="bg-white shadow p-4 flex items-center justify-between">
        <h1 class="text-xl font-bold">Background Job Runner</h1>
        <nav class="space-x-4">
          <Link href="/" class="nav-link">Dashboard</Link>
          <Link href="/job-test" class="nav-link">Test Jobs</Link>
        </nav>
      </header>
  
      <main class="p-6">
        <slot />
      </main>
    </div>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  </script>
  
  <!-- <style scoped>
  .nav-link {
    @apply text-blue-600 hover:text-blue-800 font-medium transition;
  }
  </style> -->
  