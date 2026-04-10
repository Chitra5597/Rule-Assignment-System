<?php

class Rule {
    private $id;
    private $name;
    private $type;
    private $createdDate;

    const TYPE_CONDITION = 'Condition';
    const TYPE_DECISION = 'Decision';

    public function __construct($name, $type, $id = null, $createdDate = null) {
        $this->name = $name;
        $this->type = $type;
        $this->id = $id;
        $this->createdDate = $createdDate;
    }

    // Save rule to database
    public function save() {
        $id = insert('rules', [
            'name' => $this->name,
            'type' => $this->type
        ]);
        $this->id = $id;
        return $id;
    }

    // Get all rules
    public static function getAll() {
        return getAll("SELECT * FROM rules ORDER BY name ASC");
    }

    // Get rule by ID
    public static function getById($id) {
        return getOne("SELECT * FROM rules WHERE id = ?", [$id]);
    }

    // Get rule by name
    public static function getByName($name) {
        return getOne("SELECT * FROM rules WHERE name = ?", [$name]);
    }

    // Get rules by type
    public static function getByType($type) {
        return getAll("SELECT * FROM rules WHERE type = ? ORDER BY name ASC", [$type]);
    }

    // Get all condition rules
    public static function getConditionRules() {
        return self::getByType(self::TYPE_CONDITION);
    }

    // Get all decision rules
    public static function getDecisionRules() {
        return self::getByType(self::TYPE_DECISION);
    }

    // Check if rule exists
    public static function exists($id) {
        $rule = self::getById($id);
        return $rule !== null;
    }

    // Update rule
    public function update() {
        return update('rules', [
            'name' => $this->name,
            'type' => $this->type
        ], 'id = ?', [$this->id]);
    }

    // Delete rule
    public function delete() {
        return delete('rules', 'id = ?', [$this->id]);
    }

    // Check if rule is condition type
    public function isCondition() {
        return $this->type === self::TYPE_CONDITION;
    }

    // Check if rule is decision type
    public function isDecision() {
        return $this->type === self::TYPE_DECISION;
    }

    // Validate rule data
    public static function validate($name, $type) {
        $errors = [];

        if (empty($name)) {
            $errors[] = "Rule name cannot be empty";
        }

        if (strlen($name) > 255) {
            $errors[] = "Rule name cannot exceed 255 characters";
        }

        if (!in_array($type, [self::TYPE_CONDITION, self::TYPE_DECISION])) {
            $errors[] = "Invalid rule type";
        }

        if (self::getByName($name)) {
            $errors[] = "Rule name already exists";
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

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getCreatedDate() {
        return $this->createdDate;
    }

    // Convert to array
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'created_date' => $this->createdDate
        ];
    }

    // Convert to JSON
    public function toJson() {
        return json_encode($this->toArray());
    }
}

?>