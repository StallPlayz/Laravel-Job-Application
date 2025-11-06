import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// Add token to requests if available
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Handle 401 responses (unauthorized)
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  },
)

export default {
  // Authentication
  register(data) {
    return api.post('/register', data)
  },
  login(data) {
    return api.post('/login', data)
  },
  logout() {
    return api.post('/logout')
  },
  getUser() {
    return api.get('/user')
  },

  // Jobs
  getJobs() {
    return api.get('/jobs')
  },
  getJob(id) {
    return api.get(`/jobs/${id}`)
  },

  // Applications
  getApplications() {
    return api.get('/applications')
  },
  getApplication(id) {
    return api.get(`/applications/${id}`)
  },
  createApplication(data) {
    return api.post('/applications', data)
  },
  updateApplication(id, data) {
    return api.put(`/applications/${id}`, data)
  },
  deleteApplication(id) {
    return api.delete(`/applications/${id}`)
  },
}
