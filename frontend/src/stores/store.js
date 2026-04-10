import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { rulesApi, groupsApi, assignmentsApi } from '../services/api'

export const useStore = defineStore('main', () => {
  // State
  const rules = ref([])
  const groups = ref([])
  const currentGroup = ref(null)
  const currentHierarchy = ref([])
  const loading = ref(false)
  const error = ref(null)
  const success = ref(null)

  // Computed
  const conditionRules = computed(() => 
    rules.value.filter(r => r.type === 'Condition')
  )

  const decisionRules = computed(() => 
    rules.value.filter(r => r.type === 'Decision')
  )

  const groupCount = computed(() => groups.value.length)
  const ruleCount = computed(() => rules.value.length)
  const conditionCount = computed(() => conditionRules.value.length)
  const decisionCount = computed(() => decisionRules.value.length)

  // Fetch Rules
  const fetchRules = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await rulesApi.getAll()
      rules.value = response.data.data || []
    } catch (err) {
      error.value = 'Failed to fetch rules'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  // Create Rule
  const createRule = async (name, type) => {
    loading.value = true
    error.value = null
    success.value = null
    try {
      const response = await rulesApi.create(name, type)
      if (response.data.success) {
        success.value = 'Rule created successfully'
        await fetchRules()
        return response.data.id
      }
    } catch (err) {
      error.value = err.response?.data?.error || 'Failed to create rule'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  // Fetch Groups
  const fetchGroups = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await groupsApi.getAll()
      groups.value = response.data.data || []
    } catch (err) {
      error.value = 'Failed to fetch groups'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  // Create Group
  const createGroup = async (name) => {
    loading.value = true
    error.value = null
    success.value = null
    try {
      const response = await groupsApi.create(name)
      if (response.data.success) {
        success.value = 'Group created successfully'
        await fetchGroups()
        return response.data.id
      }
    } catch (err) {
      error.value = err.response?.data?.error || 'Failed to create group'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  // Get Group Details
  const getGroup = async (id) => {
    loading.value = true
    error.value = null
    try {
      const response = await groupsApi.get(id)
      currentGroup.value = response.data.data
      return response.data.data
    } catch (err) {
      error.value = 'Failed to fetch group'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  // Get Hierarchy
  const getHierarchy = async (groupId) => {
    error.value = null
    try {
      console.log('Store: Fetching hierarchy for group', groupId)
      const response = await assignmentsApi.getHierarchy(groupId)
      console.log('Store: Hierarchy received', response.data)
      currentGroup.value = response.data.group
      currentHierarchy.value = response.data.hierarchy || []
      return response.data
    } catch (err) {
      error.value = 'Failed to fetch hierarchy'
      console.error('Store: getHierarchy error', err)
      throw err
    }
  }

  // Assign Rule
  const assignRule = async (groupId, ruleId, level, parentId = null) => {
    loading.value = true
    error.value = null
    success.value = null
    try {
      const response = await assignmentsApi.assign(groupId, ruleId, level, parentId)
      if (response.data.success) {
        success.value = 'Rule assigned successfully'
        await getHierarchy(groupId)
        return response.data.id
      }
    } catch (err) {
      error.value = err.response?.data?.error || 'Failed to assign rule'
      if (err.response?.data?.errors) {
        error.value = err.response.data.errors.join(', ')
      }
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  // Remove Assignment
  const removeAssignment = async (assignmentId, groupId) => {
    loading.value = true
    error.value = null
    success.value = null
    try {
      const response = await assignmentsApi.remove(assignmentId)
      if (response.data.success) {
        success.value = 'Assignment removed successfully'
        if (groupId) {
          await getHierarchy(groupId)
        }
      }
    } catch (err) {
      error.value = err.response?.data?.error || 'Failed to remove assignment'
      if (err.response?.data?.errors) {
        error.value = err.response.data.errors.join(', ')
      }
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  // Clear messages
  const clearMessages = () => {
    error.value = null
    success.value = null
  }

  return {
    // State
    rules,
    groups,
    currentGroup,
    currentHierarchy,
    loading,
    error,
    success,

    // Computed
    conditionRules,
    decisionRules,
    groupCount,
    ruleCount,
    conditionCount,
    decisionCount,

    // Methods
    fetchRules,
    createRule,
    fetchGroups,
    createGroup,
    getGroup,
    getHierarchy,
    assignRule,
    removeAssignment,
    clearMessages
  }
})