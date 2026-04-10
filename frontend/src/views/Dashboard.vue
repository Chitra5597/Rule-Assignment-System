<template>
  <div class="dashboard">
    <h1>Dashboard</h1>
    
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-content">
          <div class="stat-label">Total Rules</div>
          <div class="stat-value">{{ store.ruleCount }}</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">🔀</div>
        <div class="stat-content">
          <div class="stat-label">Condition Rules</div>
          <div class="stat-value">{{ store.conditionCount }}</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">✓</div>
        <div class="stat-content">
          <div class="stat-label">Decision Rules</div>
          <div class="stat-value">{{ store.decisionCount }}</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">📁</div>
        <div class="stat-content">
          <div class="stat-label">Groups</div>
          <div class="stat-value">{{ store.groupCount }}</div>
        </div>
      </div>
    </div>

    <div class="dashboard-content">
      <div class="content-section">
        <h2>Recent Groups</h2>
        <div v-if="store.groups.length > 0" class="groups-list">
          <div 
            v-for="group in store.groups.slice(0, 5)" 
            :key="group.id" 
            class="group-item"
            @click="$router.push(`/groups/${group.id}`)"
          >
            <div class="group-icon">📁</div>
            <div class="group-info">
              <div class="group-name">{{ group.name }}</div>
              <div class="group-date">{{ formatDate(group.created_date) }}</div>
            </div>
            <div class="group-arrow">→</div>
          </div>
        </div>
        <div v-else class="empty-state">
          <p>No groups yet. <router-link to="/groups">Create one</router-link></p>
        </div>
      </div>

      <div class="content-section">
        <h2>Quick Actions</h2>
        <div class="actions-list">
          <router-link to="/rules" class="action-button action-rules">
            <span class="action-icon">📝</span>
            <span>Manage Rules</span>
          </router-link>
          <router-link to="/groups" class="action-button action-groups">
            <span class="action-icon">📁</span>
            <span>Manage Groups</span>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useStore } from '../stores/store'

const store = useStore()

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString()
}
</script>

<style scoped>
.dashboard {
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
  font-size: 32px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.stat-card {
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 15px;
  align-items: center;
  transition: all 0.3s;
  cursor: pointer;
}

.dashboard-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

.content-section {
  background: white;
  border-radius: 8px;
  padding: 25px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.content-section h2 {
  color: #2c3e50;
  margin-bottom: 20px;
  font-size: 20px;
}

.groups-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.group-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  border-left: 4px solid transparent;
}

.group-arrow {
  color: #3498db;
  font-weight: bold;
}

.empty-state {
  text-align: center;
  padding: 40px 20px;
  color: #7f8c8d;
}

.empty-state a {
  color: #3498db;
  text-decoration: none;
  font-weight: 600;
}

.empty-state a:hover {
  text-decoration: underline;
}

.actions-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.action-button {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 15px 20px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s;
  border: 2px solid transparent;
}

.action-rules {
  background: #e3f2fd;
  color: #1976d2;
  border-color: #1976d2;
}

.action-groups {
  background: #f3e5f5;
  color: #7b1fa2;
  border-color: #7b1fa2;
}


</style>