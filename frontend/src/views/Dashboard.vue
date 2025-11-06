<template>
  <div class="min-h-screen bg-gray-100">
    <Header />

    <div class="container mx-auto px-4 py-8">
      <div class="grid md-grid-cols-2 gap-8">
        <!-- Job Listings -->
        <div>
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Available Jobs</h2>

          <div v-if="loadingJobs" class="text-center py-4">
            <p class="text-gray-600">Loading jobs...</p>
          </div>

          <div v-else-if="jobs.length === 0" class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600">No jobs available at the moment.</p>
          </div>

          <div v-else class="space-y-4">
            <div v-for="job in jobs" :key="job.id" class="bg-white rounded-lg shadow p-6">
              <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ job.title }}</h3>
              <p class="text-gray-600 mb-4">{{ job.description }}</p>
              <button
                @click="goToApply(job.id)"
                :disabled="hasApplied(job.id)"
                class="btn-primary"
                :class="{ 'btn-disabled': hasApplied(job.id) }"
              >
                {{ hasApplied(job.id) ? 'Already Applied' : 'Apply Now' }}
              </button>
            </div>
          </div>
        </div>

        <!-- My Applications -->
        <div>
          <h2 class="text-2xl font-bold text-gray-800 mb-4">My Applications</h2>

          <div v-if="loadingApplications" class="text-center py-4">
            <p class="text-gray-600">Loading applications...</p>
          </div>

          <div v-else-if="applications.length === 0" class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600">Anda belum melamar</p>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="application in applications"
              :key="application.id"
              class="bg-white rounded-lg shadow p-6"
            >
              <h3 class="text-xl font-semibold text-gray-800 mb-2">
                {{ application.job.title }}
              </h3>
              <p class="text-gray-600 mb-4"><strong>Alasan:</strong> {{ application.reason }}</p>
              <div class="flex gap-2">
                <button @click="editApplication(application.id)" class="btn-yellow">Edit</button>
                <button @click="deleteApplication(application.id)" class="btn-red">Delete</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.min-h-screen {
  min-height: 100vh;
}

.bg-gray-100 {
  background-color: #f3f4f6;
}

.container {
  width: 100%;
  margin-left: auto;
  margin-right: auto;
  padding-left: 1rem;
  padding-right: 1rem;
}

@media (min-width: 1024px) {
  .container {
    max-width: 1024px;
  }
}

.px-4 {
  padding-left: 1rem;
  padding-right: 1rem;
}

.py-8 {
  padding-top: 2rem;
  padding-bottom: 2rem;
}

.py-4 {
  padding-top: 1rem;
  padding-bottom: 1rem;
}

.mx-auto {
  margin-left: auto;
  margin-right: auto;
}

.grid {
  display: grid;
  gap: 2rem;
}

@media (min-width: 768px) {
  .md-grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

.gap-8 {
  gap: 2rem;
}

.gap-2 {
  gap: 0.5rem;
}

.text-2xl {
  font-size: 1.5rem;
  line-height: 2rem;
}

.text-xl {
  font-size: 1.25rem;
  line-height: 1.75rem;
}

.font-bold {
  font-weight: 700;
}

.font-semibold {
  font-weight: 600;
}

.text-gray-800 {
  color: #1f2937;
}

.text-gray-600 {
  color: #4b5563;
}

.mb-4 {
  margin-bottom: 1rem;
}

.mb-2 {
  margin-bottom: 0.5rem;
}

.text-center {
  text-align: center;
}

.bg-white {
  background-color: #ffffff;
}

.rounded-lg {
  border-radius: 0.5rem;
}

.shadow {
  box-shadow:
    0 1px 3px 0 rgba(0, 0, 0, 0.1),
    0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.p-6 {
  padding: 1.5rem;
}

.space-y-4 > * + * {
  margin-top: 1rem;
}

.flex {
  display: flex;
}

button {
  padding: 0.5rem 1rem;
  font-weight: 700;
  border-radius: 0.375rem;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.2s;
  color: #ffffff;
}

.btn-primary {
  background-color: #2563eb;
}

.btn-primary:hover:not(:disabled) {
  background-color: #1d4ed8;
}

.btn-primary:disabled,
.btn-disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

.btn-yellow {
  background-color: #eab308;
}

.btn-yellow:hover {
  background-color: #ca8a04;
}

.btn-red {
  background-color: #ef4444;
}

.btn-red:hover {
  background-color: #dc2626;
}
</style>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Header from '../components/Header.vue'
import api from '../services/api'

export default {
  name: 'Dashboard',
  components: {
    Header,
  },
  setup() {
    const router = useRouter()
    const jobs = ref([])
    const applications = ref([])
    const loadingJobs = ref(true)
    const loadingApplications = ref(true)

    onMounted(() => {
      fetchJobs()
      fetchApplications()
    })

    const fetchJobs = async () => {
      try {
        const response = await api.getJobs()
        jobs.value = response.data
      } catch (error) {
        console.error('Error fetching jobs:', error)
        alert('Failed to load jobs')
      } finally {
        loadingJobs.value = false
      }
    }

    const fetchApplications = async () => {
      try {
        const response = await api.getApplications()
        applications.value = response.data
      } catch (error) {
        console.error('Error fetching applications:', error)
        alert('Failed to load applications')
      } finally {
        loadingApplications.value = false
      }
    }

    const hasApplied = (jobId) => {
      return applications.value.some((app) => app.job_id === jobId)
    }

    const goToApply = (jobId) => {
      router.push(`/apply/${jobId}`)
    }

    const editApplication = (id) => {
      router.push(`/edit-application/${id}`)
    }

    const deleteApplication = async (id) => {
      if (!confirm('Are you sure you want to delete this application?')) {
        return
      }

      try {
        await api.deleteApplication(id)
        alert('Application deleted successfully')
        fetchApplications()
      } catch (error) {
        console.error('Error deleting application:', error)
        alert('Failed to delete application')
      }
    }

    return {
      jobs,
      applications,
      loadingJobs,
      loadingApplications,
      hasApplied,
      goToApply,
      editApplication,
      deleteApplication,
    }
  },
}
</script>
