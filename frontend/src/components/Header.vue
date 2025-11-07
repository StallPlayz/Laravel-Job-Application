<template>
  <header class="header">
    <div class="header-container">
      <div class="header-content">
        <h1 class="header-title">Job Application System</h1>

        <nav v-if="isAuthenticated" class="header-nav">
          <span class="welcome-text">Welcome, {{ userName }}</span>
          <button @click="handleLogout" class="logout-btn">Logout</button>
        </nav>
      </div>
    </div>
  </header>
</template>

<style scoped>
.header {
  background-color: #2563eb;
  color: #ffffff;
  box-shadow:
    0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.header-container {
  width: 100%;
  max-width: 1024px;
  margin: 0 auto;
  padding: 1rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-title {
  font-size: 1.5rem;
  line-height: 2rem;
  font-weight: 700;
  margin: 0;
}

.header-nav {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.welcome-text {
  font-size: 0.875rem;
  font-weight: 500;
}

.logout-btn {
  background-color: #ef4444;
  color: #ffffff;
  padding: 0.5rem 1rem;
  font-weight: 700;
  border-radius: 0.375rem;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.2s;
}

.logout-btn:hover {
  background-color: #dc2626;
}

.logout-btn:active {
  background-color: #b91c1c;
}
</style>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

export default {
  name: 'Header',
  setup() {
    const router = useRouter()
    const isAuthenticated = ref(false)
    const userName = ref('')

    onMounted(() => {
      checkAuth()
    })

    const checkAuth = () => {
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')

      if (token && user) {
        isAuthenticated.value = true
        userName.value = JSON.parse(user).name
      }
    }

    const handleLogout = async () => {
      try {
        await api.logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        isAuthenticated.value = false
        router.push('/login')
      }
    }

    return {
      isAuthenticated,
      userName,
      handleLogout,
    }
  },
}
</script>
