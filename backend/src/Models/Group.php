<?php

class Group {
    private $id;
    private $name;
    private $createdDate;

    public function __construct($name, $id = null, $createdDate = null) {
        $this->name = $name;
        $this->id = $id;
        $this->createdDate = $createdDate;
    }

    // Save group to database
    public function save() {
        $id = insert('groups', [
            'name' => $this->name
        ]);
        $this->id = $id;
        return $id;
    }

    // Get all groups
    public static function getAll() {
        return getAll("SELECT * FROM groups ORDER BY name ASC");
    }

    // Get group by ID
    public static function getById($id) {
        return getOne("SELECT * FROM groups WHERE id = ?", [$id]);
    }

    // Get group by name
    public static function getByName($name) {
        return getOne("SELECT * FROM groups WHERE name = ?", [$name]);
    }

    // Check if group exists
    public static function exists($id) {
        $group = self::getById($id);
        return $group !== null;
    }

    // Get all assignments in group
    public function getAssignments() {
        return getAll(
            "SELECT * FROM assignments WHERE group_id = ? ORDER BY level ASC, order_num ASC",
            [$this->id]
        );
    }

    // Get assignments with rule details
    public function getAssignmentsWithRules() {
        return getAll(
            "SELECT a.*, r.name as rule_name, r.type as rule_type 
             FROM assignments a 
             JOIN rules r ON a.rule_id = r.id 
             WHERE a.group_id = ? 
             ORDER BY a.level ASC, a.order_num ASC",
            [$this->id]
        );
    }

    // Get root assignments (level 1, no parent)
    public function getRootAssignments() {
        return getAll(
            "SELECT a.*, r.name as rule_name, r.type as rule_type 
             FROM assignments a 
             JOIN rules r ON a.rule_id = r.id 
             WHERE a.group_id = ? AND a.parent_id IS NULL 
             ORDER BY a.order_num ASC",
            [$this->id]
        );
    }

    // Get assignment count
    public function getAssignmentCount() {
        $result = getOne(
            "SELECT COUNT(*) as count FROM assignments WHERE group_id = ?",
            [$this->id]
        );
        return $result['count'] ?? 0;
    }

    // Get hierarchy
    public function getHierarchy() {
        $assignments = $this->getAssignmentsWithRules();
        return $this->buildHierarchy($assignments);
    }

    // Build hierarchical structure
    private function buildHierarchy($assignments, $parentId = null, $level = 1) {
        $result = [];

        foreach ($assignments as $assignment) {
            if ($assignment['parent_id'] == $parentId && $assignment['level'] == $level) {
                $children = $this->buildHierarchy($assignments, $assignment['id'], $level + 1);
                $result[] = [
                    'id' => $assignment['id'],
                    'rule_id' => $assignment['rule_id'],
                    'rule_name' => $assignment['rule_name'],
                    'rule_type' => $assignment['rule_type'],
                    'level' => $assignment['level'],
                    'order' => $assignment['order_num'],
                    'children' => $children
                ];
            }
        }

        return $result;
    }

    // Update group
    public function update() {
        return update('groups', [
            'name' => $this->name
        ], 'id = ?', [$this->id]);
    }

    // Delete group
    public function delete() {
        return delete('groups', 'id = ?', [$this->id]);
    }

    // Validate group data
    public static function validate($name) {
        $errors = [];

        if (empty($name)) {
            $errors[] = "Group name cannot be empty";
        }

        if (strlen($name) > 255) {
            $errors[] = "Group name cannot exceed 255 characters";
        }

        if (self::getByName($name)) {
            $errors[] = "Group name already exists";
        }

        return $errors;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getCreatedDate() {
        return $this->createdDate;
    }

    // Convert to array
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_date' => $this->createdDate,
            'assignment_count' => $this->getAssignmentCount()
        ];
    }

    // Convert to JSON
    public function toJson() {
        return json_encode($this->toArray());
    }
}

?>