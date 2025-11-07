<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2 class="auth-title">Create Account</h2>

      <div v-if="errorMessage" class="error-alert">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleRegister" class="auth-form">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input
            v-model="formData.name"
            type="text"
            id="name"
            required
            placeholder="Enter your full name"
          />
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <input
            v-model="formData.email"
            type="email"
            id="email"
            required
            placeholder="your@email.com"
          />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input
            v-model="formData.password"
            type="password"
            id="password"
            required
            minlength="6"
            placeholder="At least 6 characters"
          />
        </div>

        <button type="submit" :disabled="loading" class="submit-btn">
          {{ loading ? 'Creating Account...' : 'Register' }}
        </button>
      </form>

      <p class="auth-footer">
        Already have an account?
        <router-link to="/login" class="auth-link">Sign in here</router-link>
      </p>
    </div>
  </div>
</template>

<style scoped>
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.auth-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  padding: 48px 40px;
  width: 100%;
  max-width: 440px;
}

.auth-title {
  font-size: 32px;
  font-weight: 700;
  color: #1a202c;
  text-align: center;
  margin-bottom: 32px;
}

.error-alert {
  background: #fee;
  border-left: 4px solid #ef4444;
  color: #991b1b;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 24px;
  font-size: 14px;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

.form-group input {
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 15px;
  transition: all 0.3s ease;
  outline: none;
}

.form-group input:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.submit-btn {
  margin-top: 8px;
  padding: 14px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.submit-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
}

.auth-footer {
  text-align: center;
  margin-top: 24px;
  color: #6b7280;
  font-size: 14px;
}

.auth-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s;
}

.auth-link:hover {
  color: #764ba2;
  text-decoration: underline;
}
</style>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

export default {
  name: 'Register',
  setup() {
    const router = useRouter()
    const formData = ref({
      name: '',
      email: '',
      password: '',
    })
    const loading = ref(false)
    const errorMessage = ref('')

    const handleRegister = async () => {
      loading.value = true
      errorMessage.value = ''

      try {
        await api.register(formData.value)
        alert('Registration successful! Please login.')
        router.push('/login')
      } catch (error) {
        if (error.response && error.response.data) {
          if (error.response.data.errors) {
            const errors = Object.values(error.response.data.errors).flat()
            errorMessage.value = errors.join(', ')
          } else {
            errorMessage.value = error.response.data.message || 'Registration failed'
          }
        } else {
          errorMessage.value = 'An error occurred. Please try again.'
        }
      } finally {
        loading.value = false
      }
    }

    return {
      formData,
      loading,
      errorMessage,
      handleRegister,
    }
  },
}
</script>
