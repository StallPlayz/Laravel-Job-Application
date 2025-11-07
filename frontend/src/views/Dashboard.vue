<template>
  <div class="dashboard">
    <Header />

    <div class="dashboard-container">
      <div class="dashboard-grid">
        <!-- Available Jobs Section -->
        <div class="section">
          <h2 class="section-title">Available Jobs</h2>

          <div v-if="loadingJobs" class="loading-state">
            <p>Loading jobs...</p>
          </div>

          <div v-else-if="jobs.length === 0" class="empty-state">
            <p>No jobs available at the moment.</p>
          </div>

          <div v-else class="jobs-list">
            <div v-for="job in jobs" :key="job.id" class="job-card">
              <h3 class="job-title">{{ job.title }}</h3>
              <p class="job-description">{{ job.description }}</p>
              <button
                @click="goToApply(job.id)"
                :disabled="hasApplied(job.id)"
                :class="['apply-btn', { disabled: hasApplied(job.id) }]"
              >
                {{ hasApplied(job.id) ? 'Already Applied' : 'Apply Now' }}
              </button>
            </div>
          </div>
        </div>

        <!-- My Applications Section -->
        <div class="section">
          <h2 class="section-title">My Applications</h2>

          <div v-if="loadingApplications" class="loading-state">
            <p>Loading applications...</p>
          </div>

          <div v-else-if="applications.length === 0" class="empty-state">
            <p>You haven't applied to any jobs yet.</p>
          </div>

          <div v-else class="applications-list">
            <div v-for="application in applications" :key="application.id" class="application-card">
              <h3 class="application-title">{{ application.job.title }}</h3>
              <div class="application-reason">
                <strong>Reason:</strong> {{ application.reason }}
              </div>
              <div class="application-actions">
                <button @click="editApplication(application.id)" class="edit-btn">Edit</button>
                <button @click="deleteApplication(application.id)" class="delete-btn">
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #f7fafc;
}

.dashboard-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 32px;
}

@media (min-width: 768px) {
  .dashboard-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.section {
  display: flex;
  flex-direction: column;
}

.section-title {
  font-size: 28px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 24px;
}

.loading-state,
.empty-state {
  background: white;
  border-radius: 12px;
  padding: 40px;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.loading-state p,
.empty-state p {
  color: #718096;
  font-size: 16px;
}

.jobs-list,
.applications-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.job-card,
.application-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.job-card:hover,
.application-card:hover {
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.job-title,
.application-title {
  font-size: 20px;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 12px;
}

.job-description {
  color: #4a5568;
  line-height: 1.6;
  margin-bottom: 16px;
}

.application-reason {
  background: #f7fafc;
  padding: 12px;
  border-radius: 8px;
  color: #4a5568;
  line-height: 1.6;
  margin-bottom: 16px;
}

.application-reason strong {
  color: #2d3748;
}

.apply-btn {
  padding: 10px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.apply-btn:hover:not(.disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(102, 126, 234, 0.3);
}

.apply-btn.disabled {
  background: #cbd5e0;
  cursor: not-allowed;
  transform: none;
}

.application-actions {
  display: flex;
  gap: 12px;
}

.edit-btn,
.delete-btn {
  flex: 1;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.edit-btn {
  background: #ecc94b;
  color: white;
}

.edit-btn:hover {
  background: #d69e2e;
  transform: translateY(-2px);
}

.delete-btn {
  background: #f56565;
  color: white;
}

.delete-btn:hover {
  background: #e53e3e;
  transform: translateY(-2px);
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
