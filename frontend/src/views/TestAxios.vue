<template>
  <div class="test-container">
    <h2>🧪 Test Axios Connection</h2>

    <div class="status-box" :class="statusClass">
      <h3>Status: {{ status }}</h3>
      <p v-if="message">{{ message }}</p>
    </div>

    <div class="buttons">
      <button @click="testBackendConnection" class="btn-test">Test Backend Connection</button>
      <button @click="testGetJobs" class="btn-test">Test Get Jobs API</button>
      <button @click="testWithError" class="btn-test">Test Error Handling</button>
    </div>

    <div v-if="response" class="response-box">
      <h3>Response:</h3>
      <pre>{{ JSON.stringify(response, null, 2) }}</pre>
    </div>

    <div class="console-log">
      <h3>Console Logs:</h3>
      <div v-for="(log, index) in logs" :key="index" class="log-item">
        <span class="log-time">{{ log.time }}</span>
        <span :class="`log-${log.type}`">{{ log.message }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'

export default {
  name: 'TestAxios',
  setup() {
    const status = ref('Not tested yet')
    const statusClass = ref('status-idle')
    const message = ref('')
    const response = ref(null)
    const logs = ref([])

    const addLog = (message, type = 'info') => {
      const now = new Date()
      logs.value.push({
        time: `${now.getHours()}:${now.getMinutes()}:${now.getSeconds()}`,
        message,
        type,
      })
      console.log(`[${type.toUpperCase()}] ${message}`)
    }

    const testBackendConnection = async () => {
      status.value = 'Testing...'
      statusClass.value = 'status-loading'
      response.value = null

      addLog('🔄 Starting backend connection test...', 'info')

      try {
        addLog('📡 Sending request to http://localhost:8000/api/jobs', 'info')

        const result = await axios.get('http://localhost:8000/api/jobs')

        addLog('✅ Backend connection successful!', 'success')
        addLog(`📦 Received ${result.data.length} jobs`, 'success')

        status.value = 'Connected ✅'
        statusClass.value = 'status-success'
        message.value = `Backend is running! Found ${result.data.length} jobs.`
        response.value = result.data
      } catch (error) {
        addLog('❌ Backend connection failed!', 'error')

        if (error.code === 'ERR_NETWORK' || error.message.includes('Network Error')) {
          addLog('🔴 Cannot connect to http://localhost:8000', 'error')
          addLog('💡 Make sure Laravel backend is running with: php artisan serve', 'warn')
          status.value = 'Backend Not Running ❌'
          message.value = 'Cannot connect to backend. Please start Laravel with: php artisan serve'
        } else if (error.response) {
          addLog(`🔴 Server responded with error: ${error.response.status}`, 'error')
          status.value = `Error ${error.response.status}`
          message.value = error.response.data.message || 'Server error'
        } else {
          addLog(`🔴 Unknown error: ${error.message}`, 'error')
          status.value = 'Error'
          message.value = error.message
        }

        statusClass.value = 'status-error'
        response.value = { error: error.message }
      }
    }

    const testGetJobs = async () => {
      status.value = 'Testing API...'
      statusClass.value = 'status-loading'
      response.value = null

      addLog('🔄 Testing GET /api/jobs endpoint...', 'info')

      try {
        // Menggunakan service API yang sudah ada
        const api = axios.create({
          baseURL: 'http://localhost:8000/api',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
          },
        })

        addLog('📡 Making API request...', 'info')
        const result = await api.get('/jobs')

        addLog('✅ API request successful!', 'success')
        addLog(`📊 Data structure: ${Object.keys(result).join(', ')}`, 'info')

        status.value = 'API Works ✅'
        statusClass.value = 'status-success'
        message.value = 'API endpoint is working correctly!'
        response.value = result.data
      } catch (error) {
        addLog('❌ API request failed!', 'error')
        addLog(`Error details: ${error.message}`, 'error')

        status.value = 'API Error ❌'
        statusClass.value = 'status-error'
        message.value = error.message
        response.value = { error: error.message }
      }
    }

    const testWithError = async () => {
      status.value = 'Testing Error Handling...'
      statusClass.value = 'status-loading'
      response.value = null

      addLog('🔄 Intentionally triggering an error...', 'warn')

      try {
        addLog('📡 Requesting non-existent endpoint...', 'info')
        await axios.get('http://localhost:8000/api/nonexistent')
      } catch (error) {
        addLog('✅ Error handling is working!', 'success')
        addLog(`Caught error: ${error.message}`, 'info')

        if (error.response) {
          addLog(`Status Code: ${error.response.status}`, 'info')
          addLog(`Status Text: ${error.response.statusText}`, 'info')
        }

        status.value = 'Error Handling Works ✅'
        statusClass.value = 'status-success'
        message.value = 'Axios error handling is configured correctly!'
        response.value = {
          errorCaught: true,
          status: error.response?.status,
          message: error.message,
        }
      }
    }

    return {
      status,
      statusClass,
      message,
      response,
      logs,
      testBackendConnection,
      testGetJobs,
      testWithError,
    }
  },
}
</script>

<style scoped>
.test-container {
  max-width: 800px;
  margin: 2rem auto;
  padding: 2rem;
  font-family: 'Courier New', monospace;
}

h2 {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 2rem;
}

.status-box {
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 2rem;
  text-align: center;
}

.status-idle {
  background-color: #f0f0f0;
  border: 2px solid #ccc;
}

.status-loading {
  background-color: #fff3cd;
  border: 2px solid #ffc107;
  animation: pulse 1s infinite;
}

.status-success {
  background-color: #d4edda;
  border: 2px solid #28a745;
}

.status-error {
  background-color: #f8d7da;
  border: 2px solid #dc3545;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.buttons {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.btn-test {
  flex: 1;
  padding: 1rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: bold;
  transition: background-color 0.3s;
}

.btn-test:hover {
  background-color: #0056b3;
}

.response-box {
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 5px;
  padding: 1rem;
  margin-bottom: 2rem;
}

.response-box pre {
  overflow-x: auto;
  font-size: 0.85rem;
  margin: 0;
}

.console-log {
  background-color: #1e1e1e;
  color: #d4d4d4;
  border-radius: 5px;
  padding: 1rem;
  max-height: 400px;
  overflow-y: auto;
}

.console-log h3 {
  color: #569cd6;
  margin-top: 0;
  margin-bottom: 1rem;
}

.log-item {
  padding: 0.5rem 0;
  border-bottom: 1px solid #333;
  display: flex;
  gap: 1rem;
}

.log-time {
  color: #858585;
  flex-shrink: 0;
}

.log-info {
  color: #4ec9b0;
}

.log-success {
  color: #4caf50;
}

.log-error {
  color: #f44336;
}

.log-warn {
  color: #ff9800;
}
</style>
