<template>
  <div class="min-h-screen bg-gray-100">
    <Header />

    <div class="container mx-auto px-4 py-8">
      <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8">
          <h2 class="text-3xl font-bold text-gray-800 mb-6">Apply for Job</h2>

          <div v-if="loading" class="text-center py-4">
            <p class="text-gray-600">Loading job details...</p>
          </div>

          <div v-else-if="job">
            <div class="mb-6 p-4 bg-gray-50 rounded">
              <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ job.title }}</h3>
              <p class="text-gray-600">{{ job.description }}</p>
            </div>

            <form @submit.prevent="handleSubmit">
              <div class="mb-6">
                <label for="reason">Alasan Melamar *</label>
                <textarea
                  v-model="formData.reason"
                  id="reason"
                  rows="6"
                  required
                  placeholder="Jelaskan alasan Anda melamar pekerjaan ini..."
                ></textarea>
              </div>

              <div class="flex gap-4">
                <button type="submit" :disabled="submitting" class="flex-1 btn-primary">
                  {{ submitting ? 'Submitting...' : 'Submit Application' }}
                </button>
                <button type="button" @click="goBack" class="flex-1 btn-gray">Cancel</button>
              </div>
            </form>
          </div>

          <div v-else class="text-center py-4">
            <p class="text-red-600">Job not found</p>
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

.max-w-2xl {
  max-width: 42rem;
}

.mx-auto {
  margin-left: auto;
  margin-right: auto;
}

.bg-white {
  background-color: #ffffff;
}

.rounded-lg {
  border-radius: 0.5rem;
}

.shadow-md {
  box-shadow:
    0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.p-8 {
  padding: 2rem;
}

.p-4 {
  padding: 1rem;
}

.text-3xl {
  font-size: 1.875rem;
  line-height: 2.25rem;
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

.text-red-600 {
  color: #dc2626;
}

.mb-6 {
  margin-bottom: 1.5rem;
}

.mb-2 {
  margin-bottom: 0.5rem;
}

.text-center {
  text-align: center;
}

.bg-gray-50 {
  background-color: #f9fafb;
}

.rounded {
  border-radius: 0.25rem;
}

label {
  display: block;
  color: #374151;
  font-size: 0.875rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

textarea {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 1rem;
  line-height: 1.5;
  font-family: inherit;
}

textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.flex {
  display: flex;
}

.gap-4 {
  gap: 1rem;
}

.flex-1 {
  flex: 1 1 0%;
}

button {
  padding: 0.5rem 1rem;
  font-weight: 700;
  border-radius: 0.375rem;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.2s;
}

.btn-primary {
  background-color: #2563eb;
  color: #ffffff;
}

.btn-primary:hover:not(:disabled) {
  background-color: #1d4ed8;
}

.btn-primary:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

.btn-gray {
  background-color: #6b7280;
  color: #ffffff;
}

.btn-gray:hover {
  background-color: #4b5563;
}
</style>

<script>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import Header from '../components/Header.vue'
import api from '../services/api'

export default {
  name: 'ApplyJob',
  components: {
    Header,
  },
  setup() {
    const router = useRouter()
    const route = useRoute()
    const job = ref(null)
    const loading = ref(true)
    const submitting = ref(false)
    const formData = ref({
      reason: '',
    })

    onMounted(() => {
      fetchJob()
    })

    const fetchJob = async () => {
      try {
        const response = await api.getJob(route.params.jobId)
        job.value = response.data
      } catch (error) {
        console.error('Error fetching job:', error)
        alert('Failed to load job details')
      } finally {
        loading.value = false
      }
    }

    const handleSubmit = async () => {
      submitting.value = true

      try {
        await api.createApplication({
          job_id: route.params.jobId,
          reason: formData.value.reason,
        })

        alert('Application submitted successfully!')
        router.push('/dashboard')
      } catch (error) {
        console.error('Error submitting application:', error)
        if (error.response && error.response.data) {
          alert(error.response.data.message || 'Failed to submit application')
        } else {
          alert('Failed to submit application')
        }
      } finally {
        submitting.value = false
      }
    }

    const goBack = () => {
      router.push('/dashboard')
    }

    return {
      job,
      loading,
      submitting,
      formData,
      handleSubmit,
      goBack,
    }
  },
}
</script>
