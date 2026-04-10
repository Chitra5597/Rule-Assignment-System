import axios from 'axios'

const API_URL = 'http://localhost:8000/api.php'

const api = axios.create({
    baseURL: API_URL,
    headers: {
        'Content-Type': 'application/json'
    }
})

// Rules API
export const rulesApi = {
    getAll() {
        return api.get('', { params: { action: 'get-rules' } })
    },
    
    getByType(type) {
        return api.get('', { params: { action: 'get-rules-by-type', type } })
    },
    
    create(name, type) {
        return api.post('', { name, type }, { params: { action: 'create-rule' } })
    }
}

// Groups API
export const groupsApi = {
    getAll() {
        return api.get('', { params: { action: 'get-groups' } })
    },
    
    get(id) {
        return api.get('', { params: { action: 'get-group', id } })
    },
    
    create(name) {
        return api.post('', { name }, { params: { action: 'create-group' } })
    }
}

// Assignments API
export const assignmentsApi = {
    assign(groupId, ruleId, level, parentId = null) {
        return api.post('', 
            { group_id: groupId, rule_id: ruleId, level, parent_id: parentId },
            { params: { action: 'assign-rule' } }
        )
    },
    
    getHierarchy(groupId) {
        return api.get('', { params: { action: 'get-hierarchy', group_id: groupId } })
    },
    
    remove(id) {
        return api.post('', { id }, { params: { action: 'remove-assignment' } })
    }
}

export default api