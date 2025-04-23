<script setup>
import { ref } from 'vue'
import axios from 'axios'
import AppLayout from '../Layouts/JobAppLayout.vue';

const message = ref(null)

const triggerJob = async (type) => {
  message.value = null
  try {
    const response = await axios.post('/api/job-test', { type })
    message.value = response.data.message
  } catch (error) {
    message.value = error.response?.data?.message || 'Something went wrong.'
  }
}
</script>

<template>
     <AppLayout>
    <div class="p-6 space-y-4">
      <h1 class="text-2xl font-bold mb-4">Background Job Test Panel</h1>
  
      <div class="space-y-2">
        <button @click="triggerJob('basic')" class="btn">Run Basic Job</button>
        <button @click="triggerJob('delayed')" class="btn">Run Delayed Job (5s)</button>
        <button @click="triggerJob('chained')" class="btn">Run Chained Job</button>
        <button @click="triggerJob('retry')" class="btn">Run Retry Job (fails randomly)</button>
        <button @click="triggerJob('disallowed')" class="btn">Run Disallowed Job</button>
      </div>
  
      <div v-if="message" class="mt-4 p-3 bg-green-100 border border-green-400 rounded">
        {{ message }}
      </div>
    </div>
     </AppLayout>
  </template>
  
 
  
  <style scoped>
  .btn {
    @apply px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition;
  }
  </style>
  