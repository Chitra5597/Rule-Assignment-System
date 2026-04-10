<?php

class Assignment {
    private $id;
    private $groupId;
    private $ruleId;
    private $parentId;
    private $level;
    private $orderNum;
    private $createdDate;

    public function __construct($groupId, $ruleId, $level, $orderNum, $parentId = null, $id = null, $createdDate = null) {
        $this->groupId = $groupId;
        $this->ruleId = $ruleId;
        $this->level = $level;
        $this->orderNum = $orderNum;
        $this->parentId = $parentId;
        $this->id = $id;
        $this->createdDate = $createdDate;
    }

    // Save assignment to database
    public function save() {
        $id = insert('assignments', [
            'group_id' => $this->groupId,
            'rule_id' => $this->ruleId,
            'parent_id' => $this->parentId,
            'level' => $this->level,
            'order_num' => $this->orderNum
        ]);
        $this->id = $id;
        return $id;
    }

    // Get assignment by ID
    public static function getById($id) {
        return getOne("SELECT * FROM assignments WHERE id = ?", [$id]);
    }

    // Get assignments by group
    public static function getByGroup($groupId) {
        return getAll(
            "SELECT a.*, r.name as rule_name, r.type as rule_type 
             FROM assignments a 
             JOIN rules r ON a.rule_id = r.id 
             WHERE a.group_id = ? 
             ORDER BY a.level ASC, a.order_num ASC",
            [$groupId]
        );
    }

    // Get children of assignment
    public static function getChildren($parentId) {
        return getAll(
            "SELECT a.*, r.name as rule_name, r.type as rule_type 
             FROM assignments a 
             JOIN rules r ON a.rule_id = r.id 
             WHERE a.parent_id = ? 
             ORDER BY a.order_num ASC",
            [$parentId]
        );
    }

    // Check if assignment exists
    public static function exists($id) {
        $assignment = self::getById($id);
        return $assignment !== null;
    }

    // Check if rule is already assigned under parent
    public static function isDuplicate($groupId, $ruleId, $parentId = null) {
        $query = "SELECT COUNT(*) as count FROM assignments 
                  WHERE group_id = ? AND rule_id = ? AND parent_id " . ($parentId ? "= ?" : "IS NULL");
        $params = $parentId ? [$groupId, $ruleId, $parentId] : [$groupId, $ruleId];
        
        $result = getOne($query, $params);
        return $result['count'] > 0;
    }

    // Get next order number for tier
    public static function getNextOrder($groupId, $level, $parentId = null) {
        $query = "SELECT MAX(order_num) as max_order FROM assignments 
                  WHERE group_id = ? AND level = ? AND parent_id " . ($parentId ? "= ?" : "IS NULL");
        $params = $parentId ? [$groupId, $level, $parentId] : [$groupId, $level];
        
        $result = getOne($query, $params);
        return ($result['max_order'] ?? 0) + 1;
    }

    // Get parent assignment
    public function getParent() {
        if (!$this->parentId) {
            return null;
        }
        return self::getById($this->parentId);
    }

    // Get associated rule
    public function getRule() {
        return Rule::getById($this->ruleId);
    }

    // Get associated group
    public function getGroup() {
        return Group::getById($this->groupId);
    }

    // Check if has children
    public function hasChildren() {
        $children = self::getChildren($this->id);
        return count($children) > 0;
    }

    // Get child count
    public function getChildCount() {
        $result = getOne(
            "SELECT COUNT(*) as count FROM assignments WHERE parent_id = ?",
            [$this->id]
        );
        return $result['count'] ?? 0;
    }

    // Update assignment
    public function update() {
        return update('assignments', [
            'group_id' => $this->groupId,
            'rule_id' => $this->ruleId,
            'parent_id' => $this->parentId,
            'level' => $this->level,
            'order_num' => $this->orderNum
        ], 'id = ?', [$this->id]);
    }

    // Delete assignment (and cascade delete children)
    public function delete() {
        // Delete all children recursively
        $children = self::getChildren($this->id);
        foreach ($children as $child) {
            $childAssign = new Assignment(
                $child['group_id'],
                $child['rule_id'],
                $child['level'],
                $child['order_num'],
                $child['parent_id'],
                $child['id']
            );
            $childAssign->delete();
        }

        // Delete self
        return delete('assignments', 'id = ?', [$this->id]);
    }

    // Validate assignment
    public static function validate($groupId, $ruleId, $level, $parentId = null) {
        $errors = [];

        // Check if group exists
        if (!Group::exists($groupId)) {
            $errors[] = "Group does not exist";
        }

        // Check if rule exists
        if (!Rule::exists($ruleId)) {
            $errors[] = "Rule does not exist";
        }

        // Check tier level
        if ($level < 1 || $level > MAX_TIERS) {
            $errors[] = "Level must be between 1 and " . MAX_TIERS;
        }

        // Validate parent if provided
        if ($parentId) {
            $parent = self::getById($parentId);
            if (!$parent) {
                $errors[] = "Parent assignment does not exist";
            } else {
                // Check tier relationship
                if ($level <= $parent['level']) {
                    $errors[] = "Child level must be greater than parent level";
                }

                // Check immediate next tier
                if ($level > $parent['level'] + 1) {
                    $errors[] = "Child must be in immediate next tier";
                }

                // Check if parent rule is Condition
                $parentRule = Rule::getById($parent['rule_id']);
                if ($parentRule['type'] !== Rule::TYPE_CONDITION) {
                    $errors[] = "Parent rule must be Condition type";
                }
            }
        }

        // Check if parent is condition rule
        $rule = Rule::getById($ruleId);
        if ($parentId && $rule['type'] === Rule::TYPE_DECISION) {
            // This is allowed - Decision rules can be children
        }

        // Check for duplicates
        if (self::isDuplicate($groupId, $ruleId, $parentId)) {
            $errors[] = "Rule already assigned under this parent";
        }

        return $errors;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getGroupId() {
        return $this->groupId;
    }

    public function getRuleId() {
        return $this->ruleId;
    }

    public function getParentId() {
        return $this->parentId;
    }

    public function getLevel() {
        return $this->level;
    }

    public function getOrderNum() {
        return $this->orderNum;
    }

    public function getCreatedDate() {
        return $this->createdDate;
    }

    // Convert to array
    public function toArray() {
        return [
            'id' => $this->id,
            'group_id' => $this->groupId,
            'rule_id' => $this->ruleId,
            'parent_id' => $this->parentId,
            'level' => $this->level,
            'order_num' => $this->orderNum,
            'created_date' => $this->createdDate
        ];
    }

    // Convert to JSON
    public function toJson() {
        return json_encode($this->toArray());
    }
}

?>