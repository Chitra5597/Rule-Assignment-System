<template>
  <div class="groups-page">
    <h1>Manage Groups</h1>

    <!-- Create Group Form -->
    <div class="form-section">
      <h2>Create New Group</h2>
      <form @submit.prevent="submitForm" class="form">
        <div class="form-group">
          <input 
            v-model="form.name" 
            type="text" 
            placeholder="Create Group"
            required
          />
        </div>
        <button type="submit" class="btn btn-primary" :disabled="store.loading">
          {{ store.loading ? 'Creating...' : 'Create Group' }}
        </button>
      </form>
    </div>

    <!-- Groups List -->
    <div class="groups-section">
      <h2>All Groups ({{ store.groupCount }})</h2>

      <div v-if="store.groups.length > 0" class="groups-grid">
        <div 
          v-for="group in store.groups" 
          :key="group.id" 
          class="group-card"
          @click="$router.push(`/groups/${group.id}`)"
        >
          <div class="group-icon">📁</div>
          <div class="group-details">
            <div class="group-name">{{ group.name }}</div>
            <div class="group-info">
              <small>Created: {{ formatDate(group.created_date) }}</small>
            </div>
          </div>
          <div class="group-action">→</div>
        </div>
      </div>
      <div v-else class="empty-state">
        <p>No groups yet. Create one above!</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useStore } from '../stores/store'

const store = useStore()
const form = ref({
  name: ''
})

async function submitForm() {
  if (!form.value.name.trim()) {
    store.error = 'Group name is required'
    return
  }

  const success = await store.createGroup(form.value.name)
  if (success) {
    form.value.name = ''
  }
}

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString()
}
</script>

<style scoped>
.groups-page {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

h1 {
  color: #2c3e50;
  margin-bottom: 30px;
}

h2 {
  color: #34495e;
  margin-bottom: 20px;
}

.form-section {
  background: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}

.form {
  display: flex;
  gap: 10px;
}

.form-group {
  flex: 1;
}

.form-group input {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
  font-family: inherit;
}

.form-group input:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.btn {
  padding: 12px 25px;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #3498db;
  color: white;
}

.groups-section {
  background: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.groups-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.group-card {  
  background: #f8f9fa;
  border-left: 4px solid #ddd;
  padding: 15px;
  border-radius: 6px;
  cursor: pointer;
}

</style>