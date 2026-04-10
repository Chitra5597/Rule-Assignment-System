<template>
  <div id="app" class="app">
    <header class="header">
      <div class="header-content">
        <h1 class="logo">📋 Rule Assignment System</h1>
        <nav class="nav">
          <router-link to="/" :class="{ active: $route.path === '/' }">
            Dashboard
          </router-link>
          <router-link to="/rules" :class="{ active: $route.path === '/rules' }">
            Rules
          </router-link>
          <router-link to="/groups" :class="{ active: $route.path === '/groups' }">
            Groups
          </router-link>
        </nav>
      </div>
    </header>

    <main class="main-content">
      <!-- Alert Messages -->
      <div v-if="store.success" class="alert alert-success">
        ✓ {{ store.success }}
      </div>
      <div v-if="store.error" class="alert alert-error">
        ✗ {{ store.error }}
      </div>

      <!-- Loading Spinner -->
      <div v-if="store.loading" class="loading">
        <div class="spinner"></div>
        <p>Loading...</p>
      </div>

      <!-- Page Content -->
      <router-view v-if="!store.loading" />
    </main>

    <footer class="footer">
      <p>&copy; 2024 Rule Assignment System. All rights reserved.</p>
    </footer>
  </div>
</template>

<script setup>
import { useStore } from './stores/store'
import { watch } from 'vue'

const store = useStore()

// Auto-clear messages after 5 seconds
watch(() => [store.success, store.error], () => {
  if (store.success || store.error) {
    setTimeout(() => {
      store.clearMessages()
    }, 5000)
  }
})

// Initialize data on mount
store.fetchRules()
store.fetchGroups()
</script>

<style scoped>
.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f5f7fa;
}

.header {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  color: white;
  padding: 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 15px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  font-size: 24px;
  font-weight: bold;
  margin: 0;
}

.nav {
  display: flex;
  gap: 20px;
}

.nav a {
  color: white;
  text-decoration: none;
  padding: 8px 15px;
  border-radius: 4px;
  transition: all 0.3s;
  font-weight: 500;
}

.nav a:hover {
  background: rgba(255, 255, 255, 0.1);
}

.nav a.active {
  background: rgba(52, 152, 219, 0.8);
}

.main-content {
  flex: 1;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
  padding: 30px 20px;
}

.alert {
  padding: 15px 20px;
  margin-bottom: 20px;
  border-radius: 6px;
  font-weight: 500;
  animation: slideIn 0.3s ease-out;
}

.alert-success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.alert-error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

@keyframes slideIn {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 400px;
  gap: 20px;
}

.spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.footer {
  background: #2c3e50;
  color: white;
  text-align: center;
  padding: 20px;
  margin-top: 40px;
  font-size: 14px;
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 15px;
  }

  .nav {
    gap: 10px;
    width: 100%;
    justify-content: flex-start;
  }

  .main-content {
    padding: 15px 10px;
  }
}
</style>