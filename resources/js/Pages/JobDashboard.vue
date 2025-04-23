<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AppLayout from '../Layouts/JobAppLayout.vue' // use the shared layout

const statusLabel = (status) => {
  switch (status) {
    case 1: return 'Running'
    case 2: return 'Completed'
    case 3: return 'Failed'
    case 4: return 'Cancelled'
    default: return 'Pending'
  }
}

const jobs = ref({ data: [] })

const loadJobs = async (url = '/api/jobs') => {
  try {
    const res = await axios.get(url)
    jobs.value = res.data
  } catch (e) {
    console.error('Failed to load jobs', e)
  }
}


const cancelJob = async (id) => {
  if (!confirm('Are you sure you want to cancel this job?')) return
  await axios.post(`/api/jobs/${id}/cancel`)
  await loadJobs()
}



onMounted(() => {
  loadJobs()
})
</script>

<template>
  <AppLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Job Queue Dashboard</h1>

      <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow rounded-lg">
          <thead>
            <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
              <th class="p-3">ID</th>
              <th class="p-3">Class</th>
              <th class="p-3">Method</th>
              <th class="p-3">Status</th>
              <th class="p-3">Retries</th>
              <th class="p-3">Run At</th>
              <th class="p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(job, index) in jobs.data" :key="job.id" :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'" class="border-t">
              <td class="p-3">{{ job.id }}</td>
              <td class="p-3">{{ job.class }}</td>
              <td class="p-3">{{ job.method }}</td>
              <td class="p-3">{{ statusLabel(job.status) }}</td>
              <td class="p-3">{{ job.retries }}</td>
              <td class="p-3">{{ job.run_at }}</td>
              <td class="p-3">
                <button
                  class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm"
                  @click="cancelJob(job.id)"
                  v-if="job.status == '0' || job.status == '1'"
                >
                  Cancel
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="mt-4 flex justify-between items-center">
        <button
          @click="loadJobs(jobs.prev_page_url)"
          :disabled="!jobs.prev_page_url"
          class="px-4 py-2 bg-gray-200 rounded"
        >
          Previous
        </button>
        <button
          @click="loadJobs(jobs.next_page_url)"
          :disabled="!jobs.next_page_url"
          class="px-4 py-2 bg-gray-200 rounded"
        >
          Next
        </button>
      </div>
      </div>
    </div>
  </AppLayout>
</template>
