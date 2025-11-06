<template>
  <header class="bg-blue-600 text-white shadow-md">
    <div class="container mx-auto px-4 py-4">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Job Application System</h1>

        <nav v-if="isAuthenticated">
          <div class="flex items-center gap-4">
            <span class="text-sm">Welcome, {{ userName }}</span>
            <button
              @click="handleLogout"
              class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition-colors"
            >
              Logout
            </button>
          </div>
        </nav>
      </div>
    </div>
  </header>
</template>

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
