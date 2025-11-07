<template>
  <div class="edit-page">
    <Header />

    <div class="edit-container">
      <div class="edit-card">
        <h2 class="page-title">Edit Application</h2>

        <div v-if="loading" class="loading-state">
          <p>Loading application...</p>
        </div>

        <div v-else-if="application" class="content">
          <div class="job-info">
            <h3 class="job-title">{{ application.job.title }}</h3>
            <p class="job-description">{{ application.job.description }}</p>
          </div>

          <form @submit.prevent="handleSubmit" class="application-form">
            <div class="form-group">
              <label for="reason">Why do you want to apply for this position? *</label>
              <textarea
                v-model="formData.reason"
                id="reason"
                rows="8"
                required
                placeholder="Tell us why you're the perfect fit for this role..."
              ></textarea>
            </div>

            <div class="form-actions">
              <button type="submit" :disabled="submitting" class="submit-btn">
                {{ submitting ? 'Updating...' : 'Update Application' }}
              </button>
              <button type="button" @click="goBack" class="cancel-btn">Cancel</button>
            </div>
          </form>
        </div>

        <div v-else class="error-state">
          <p>Application not found</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.edit-page {
  min-height: 100vh;
  background: #f7fafc;
}

.edit-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 40px 20px;
}

.edit-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  padding: 40px;
}

.page-title {
  font-size: 32px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 32px;
  text-align: center;
}

.loading-state,
.error-state {
  text-align: center;
  padding: 40px;
}

.loading-state p {
  color: #718096;
  font-size: 16px;
}

.error-state p {
  color: #e53e3e;
  font-size: 16px;
}

.content {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.job-info {
  background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
  padding: 24px;
  border-radius: 12px;
  border-left: 4px solid #667eea;
}

.job-title {
  font-size: 24px;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 12px;
}

.job-description {
  color: #4a5568;
  line-height: 1.7;
}

.application-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-size: 15px;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 10px;
}

.form-group textarea {
  padding: 14px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 15px;
  font-family: inherit;
  line-height: 1.6;
  transition: all 0.3s ease;
  outline: none;
  resize: vertical;
}

.form-group textarea:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-top: 8px;
}

.submit-btn,
.cancel-btn {
  padding: 14px 24px;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.submit-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.submit-btn:disabled {
  background: #cbd5e0;
  cursor: not-allowed;
  transform: none;
}

.cancel-btn {
  background: #edf2f7;
  color: #4a5568;
}

.cancel-btn:hover {
  background: #e2e8f0;
  transform: translateY(-2px);
}
</style>

<script>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import Header from '../components/Header.vue'
import api from '../services/api'

export default {
  name: 'EditApplication',
  components: {
    Header,
  },
  setup() {
    const router = useRouter()
    const route = useRoute()
    const application = ref(null)
    const loading = ref(true)
    const submitting = ref(false)
    const formData = ref({
      reason: '',
    })

    onMounted(() => {
      fetchApplication()
    })

    const fetchApplication = async () => {
      try {
        const response = await api.getApplication(route.params.id)
        application.value = response.data
        formData.value.reason = response.data.reason
      } catch (error) {
        console.error('Error fetching application:', error)
        alert('Failed to load application details')
      } finally {
        loading.value = false
      }
    }

    const handleSubmit = async () => {
      submitting.value = true

      try {
        await api.updateApplication(route.params.id, {
          reason: formData.value.reason,
        })

        alert('Application updated successfully!')
        router.push('/dashboard')
      } catch (error) {
        console.error('Error updating application:', error)
        if (error.response && error.response.data) {
          alert(error.response.data.message || 'Failed to update application')
        } else {
          alert('Failed to update application')
        }
      } finally {
        submitting.value = false
      }
    }

    const goBack = () => {
      router.push('/dashboard')
    }

    return {
      application,
      loading,
      submitting,
      formData,
      handleSubmit,
      goBack,
    }
  },
}
</script>
