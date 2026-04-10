<template>
  <div class="rules-page">
    <h1>Manage Rules</h1>

    <!-- Create Rule Form -->
    <div class="form-section">
      <h2>Create New Rule</h2>
      <form @submit.prevent="submitForm" class="form">
        <div class="form-group">
          <label>Rule Name</label>
          <input 
            v-model="form.name" 
            type="text" 
            placeholder="Create Rule"
            required
          />
        </div>

        <div class="form-group">
          <label>Rule Type</label>
          <select v-model="form.type" required>
            <option value="">Select Type</option>
            <option value="Condition">Condition (has children)</option>
            <option value="Decision">Decision (final rule)</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary" :disabled="store.loading">
          {{ store.loading ? 'Creating...' : 'Create Rule' }}
        </button>
      </form>
    </div>

    <!-- Rules List -->
    <div class="rules-section">
      <h2>All Rules</h2>

      <div class="filter-buttons">
        <button 
          @click="filterType = null"
          :class="{ active: filterType === null }"
          class="filter-btn"
        >
          All ({{ store.ruleCount }})
        </button>
        <button 
          @click="filterType = 'Condition'"
          :class="{ active: filterType === 'Condition' }"
          class="filter-btn"
        >
          Condition ({{ store.conditionCount }})
        </button>
        <button 
          @click="filterType = 'Decision'"
          :class="{ active: filterType === 'Decision' }"
          class="filter-btn"
        >
          Decision ({{ store.decisionCount }})
        </button>
      </div>

      <div v-if="filteredRules.length > 0" class="rules-list">
        <div 
          v-for="rule in filteredRules" 
          :key="rule.id" 
          class="rule-card"
          :class="{ 'is-condition': rule.type === 'Condition', 'is-decision': rule.type === 'Decision' }"
        >
          <div class="rule-header">
            <div class="rule-type-arrow" :class="rule.type.toLowerCase()">
              {{ rule.type }}
            </div>
            <div class="rule-name">{{ rule.name }}</div>
          </div>
          <div class="rule-footer">
            <small>{{ formatDate(rule.created_date) }}</small>
          </div>
        </div>
      </div>
      <div v-else class="empty-state">
        <p>No rules found</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useStore } from '../stores/store'

const store = useStore()
const filterType = ref(null)
const form = ref({
  name: '',
  type: ''
})

const filteredRules = computed(() => {
  if (filterType.value === null) {
    return store.rules
  }
  return store.rules.filter(r => r.type === filterType.value)
})

async function submitForm() {
  const errors = []
  
  if (!form.value.name.trim()) {
    errors.push('Rule name is required')
  }
  if (!form.value.type) {
    errors.push('Rule type is required')
  }

  if (errors.length > 0) {
    store.error = errors.join(', ')
    return
  }

  const success = await store.createRule(form.value.name, form.value.type)
  if (success) {
    form.value.name = ''
    form.value.type = ''
  }
}

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString()
}
</script>

<style scoped>
.rules-page {
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
  font-size: 18px;
}

.form-section {
  background: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}

.form {
  display: grid;
  grid-template-columns: 1fr 1fr auto;
  gap: 15px;
  align-items: flex-end;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 8px;
  font-weight: 600;
  color: #2c3e50;
  font-size: 14px;
}

.form-group input,
.form-group select {
  padding: 10px 15px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 14px;
}

.btn-primary {
  background: #3498db;
  color: white;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.rules-section {
  background: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.filter-buttons {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.filter-btn {
  padding: 8px 15px;
  border: 2px solid #ddd;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 600;
  font-size: 14px;
}

.filter-btn:hover {
  border-color: #3498db;
}

.filter-btn.active {
  background: #3498db;
  color: white;
  border-color: #3498db;
}

.rules-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 15px;
}

.rule-card {
  background: #f8f9fa;
  border-left: 4px solid #ddd;
  padding: 15px;
  border-radius: 6px;
  cursor: pointer;
}

.rule-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.rule-type-arrow {
  padding: 4px 10px;
  border-radius: 3px;
  font-size: 12px;
  font-weight: bold;
  text-transform: uppercase;
  white-space: nowrap;
}

.rule-type-arrow.condition {
  background: #d6eaf8;
  color: #0c5aa0;
}

.rule-type-arrow.decision {
  background: #d5f4e6;
  color: #0b5345;
}

.rule-name {
  font-weight: 600;
  color: #2c3e50;
  word-break: break-word;
}

.rule-footer {
  color: #7f8c8d;
  font-size: 12px;
}

.empty-state {
  text-align: center;
  padding: 40px 20px;
  color: #7f8c8d;
}

</style>