<template>
  <div class="rule-tree">
    <div v-for="item in items" :key="item.id" class="tree-node">
      <div 
        class="node-content"
        :class="{ 'is-expanded': expandedNodes.has(item.id) }"
      >
        <button 
          v-if="item.children && item.children.length > 0"
          @click="toggleExpand(item.id)"
          class="expand-btn"
        >
          {{ expandedNodes.has(item.id) ? '▼' : '▶' }}
        </button>
        <span v-else class="expand-btn-placeholder"></span>

        <div class="node-info">
          <span class="rule-badge" :class="item.rule_type.toLowerCase()">
            {{ item.rule_type }}
          </span>
          <span class="rule-name">{{ item.rule_name }}</span>
          <span class="rule-level">L{{ item.level }}</span>
        </div>

        <button 
          @click="handleRemove(item.id)"
          class="remove-btn"
          title="Remove assignment"
        >
          ✕
        </button>
      </div>

      <div 
        v-if="item.children && item.children.length > 0 && expandedNodes.has(item.id)"
        class="children"
      >
        <RuleTree 
          :items="item.children"
          @onRemove="handleRemove"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['onRemove'])

const expandedNodes = ref(new Set())

const toggleExpand = (id) => {
  if (expandedNodes.value.has(id)) {
    expandedNodes.value.delete(id)
  } else {
    expandedNodes.value.add(id)
  }
}

const handleRemove = (assignmentId) => {
  emit('onRemove', assignmentId)
}
</script>

<style scoped>
.rule-tree {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.tree-node {
  border-left: 2px solid #e0e0e0;
  margin-left: 10px;
}

.node-content {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 10px;
  border-left: 4px solid transparent;
  margin-left: -6px;
  background: #f8f9fa;
  border-radius: 4px;
  transition: all 0.2s;
}

.node-content:hover {
  background: #ecf0f1;
  border-left-color: #3498db;
}

.node-content.is-expanded {
  background: #e8f4f8;
}

.expand-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 12px;
  color: #7f8c8d;
  padding: 0;
  width: 20px;
  text-align: center;
  transition: all 0.2s;
  flex-shrink: 0;
}

.expand-btn:hover {
  color: #2c3e50;
  transform: scale(1.2);
}

.expand-btn-placeholder {
  display: inline-block;
  width: 20px;
  flex-shrink: 0;
}

.node-info {
  display: flex;
  align-items: center;
  gap: 10px;
  flex: 1;
  min-width: 0;
}

.rule-badge {
  padding: 4px 8px;
  border-radius: 3px;
  font-size: 11px;
  font-weight: bold;
  text-transform: uppercase;
  white-space: nowrap;
  flex-shrink: 0;
}

.rule-badge.condition {
  background: #d6eaf8;
  color: #0c5aa0;
}

.rule-badge.decision {
  background: #d5f4e6;
  color: #0b5345;
}

.rule-name {
  font-weight: 600;
  color: #2c3e50;
  flex: 1;
  word-break: break-word;
}

.rule-level {
  font-size: 12px;
  background: #ecf0f1;
  padding: 2px 6px;
  border-radius: 3px;
  color: #7f8c8d;
  flex-shrink: 0;
}

.remove-btn {
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 4px;
  width: 28px;
  height: 28px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
  display: none;
  flex-shrink: 0;
}

.node-content:hover .remove-btn {
  display: inline-block;
}

.remove-btn:hover {
  background: #c0392b;
  transform: scale(1.1);
}

.children {
  margin-top: 0;
}
</style>