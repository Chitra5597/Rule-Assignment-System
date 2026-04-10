<template>
  <div class="group-detail">
    <div class="header">
      <router-link to="/groups" class="back-btn">← Back</router-link>
      <h1 v-if="currentGroupData">{{ currentGroupData.name }}</h1>
      <h1 v-else>Group Details</h1>
    </div>

    <!-- Loading state -->
    <div v-if="isLoading" class="loading">
      <div class="spinner"></div>
      <p>Loading group details...</p>
    </div>

    <!-- Loaded state -->
    <div v-else-if="currentGroupData" class="content">
      <!-- Assign Rule Form -->
      <div class="form-section">
        <h2>Assign Rule</h2>
        <form @submit.prevent="submitAssign" class="form">
          <div class="form-group">
            <label>Parent Rule (Optional)</label>
            <select v-model="assignForm.parentId" @change="updateTierLevel">
              <option value="">-- Root Level --</option>
              <option v-for="rule in rootRules" :key="rule.id" :value="rule.id">
                {{ rule.rule_name }} ({{ rule.rule_type }})
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>Rule to Assign</label>
            <select v-model="assignForm.ruleId" required>
              <option value="">Select Rule</option>
              <option v-for="rule in store.rules" :key="rule.id" :value="rule.id">
                {{ rule.name }} ({{ rule.type }})
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>Tier Level</label>
            <input 
              v-model.number="assignForm.level" 
              type="number" 
              min="1" 
              max="3" 
              readonly
              class="read-only"
            />
          </div>

          <button type="submit" class="btn btn-primary" :disabled="store.loading">
            {{ store.loading ? 'Assigning...' : 'Assign Rule' }}
          </button>
        </form>
      </div>

      <!-- Hierarchy Tree -->
      <div class="hierarchy-section">
        <h2>Rule Hierarchy</h2>
        <div v-if="currentHierarchy.length > 0" class="hierarchy-tree">
          <RuleTree 
            :items="currentHierarchy"
            @onRemove="removeAssignment"
          />
        </div>
        <div v-else class="empty-state">
          <p>No rules assigned yet. Assign one above!</p>
        </div>
      </div>
    </div>

    <!-- Error state -->
    <div v-else class="empty-state">
      <p>Unable to load group details</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from '../stores/store'
import RuleTree from '../components/RuleTree.vue'

const store = useStore()
const props = defineProps({
  id: {
    type: String,
    required: true
  }
})

const isLoading = ref(true)
const currentGroupData = ref(null)
const currentHierarchy = ref([])

const assignForm = ref({
  parentId: '',
  ruleId: '',
  level: 1
})

const rootRules = computed(() => {
  return currentHierarchy.value.map(item => ({
    id: item.id,
    rule_name: item.rule_name,
    rule_type: item.rule_type
  }))
})

// Load data only once
onMounted(async () => {
  console.log('GroupDetail mounted with id:', props.id)
  
  if (props.id) {
    isLoading.value = true
    
    try {
      const result = await store.getHierarchy(props.id)
      console.log('GroupDetail: Data loaded', result)
      currentGroupData.value = result.group
      currentHierarchy.value = result.hierarchy || []
    } catch (err) {
      console.error('GroupDetail: Failed to load', err)
    } finally {
      isLoading.value = false
    }
  }
})

// Update tier level when parent changes
const updateTierLevel = () => {
  if (!assignForm.value.parentId) {
    assignForm.value.level = 1
    return
  }

  const findParentLevel = (items) => {
    for (const item of items) {
      if (item.id === parseInt(assignForm.value.parentId)) {
        return item.level + 1
      }
      if (item.children && item.children.length > 0) {
        const found = findParentLevel(item.children)
        if (found) return found
      }
    }
    return null
  }

  const level = findParentLevel(currentHierarchy.value)
  if (level) {
    assignForm.value.level = level
  }
}

const submitAssign = async () => {
  if (!assignForm.value.ruleId) {
    store.error = 'Please select a rule'
    return
  }

  await store.assignRule(
    parseInt(props.id),
    parseInt(assignForm.value.ruleId),
    assignForm.value.level,
    assignForm.value.parentId ? parseInt(assignForm.value.parentId) : null
  )

  if (store.success) {
    assignForm.value.ruleId = ''
    assignForm.value.parentId = ''
    assignForm.value.level = 1
    // Refresh hierarchy after assignment
    const result = await store.getHierarchy(props.id)
    currentHierarchy.value = result.hierarchy || []
  }
}

const removeAssignment = async (assignmentId) => {
  if (confirm('Are you sure you want to remove this assignment?')) {
    await store.removeAssignment(assignmentId, parseInt(props.id))
    // Refresh hierarchy after removal
    const result = await store.getHierarchy(props.id)
    currentHierarchy.value = result.hierarchy || []
  }
}
</script>

<style scoped>
.group-detail {
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

.header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 30px;
}

.back-btn {
  padding: 10px 15px;
  background: #ecf0f1;
  border-radius: 6px;
  text-decoration: none;
  color: #2c3e50;
  font-weight: 600;
  transition: all 0.2s;
  white-space: nowrap;
}

h1 {
  color: #2c3e50;
  margin: 0;
  flex: 1;
}

h2 {
  color: #34495e;
  margin-bottom: 20px;
  font-size: 18px;
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

.content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

.form-section {
  background: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  height: fit-content;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 15px;
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

.read-only {
  background: #ecf0f1;
  cursor: not-allowed;
}

.btn {
  padding: 12px 20px;
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

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.hierarchy-section {
  background: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.hierarchy-tree {
  max-height: 600px;
  overflow-y: auto;
}

</style>