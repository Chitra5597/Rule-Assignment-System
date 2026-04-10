<?php

class Validator {
    private $errors = [];

    // Validate rule assignment with hierarchy check
    public function validateRuleAssignment($groupId, $ruleId, $level, $parentId = null) {
        $this->errors = [];

        // Basic validation
        $assignmentErrors = Assignment::validate($groupId, $ruleId, $level, $parentId);
        if (!empty($assignmentErrors)) {
            $this->errors = array_merge($this->errors, $assignmentErrors);
            return false;
        }

        // Check if assigning Condition rule and ensure it will have children
        $rule = Rule::getById($ruleId);
        if ($rule['type'] === Rule::TYPE_CONDITION) {
            // Condition rule can be assigned, will be validated when removing children
        }

        return true;
    }

    // Validate group hierarchy (Condition rules must have children)
    public function validateGroupHierarchy($groupId) {
        $this->errors = [];

        $assignments = Assignment::getByGroup($groupId);

        foreach ($assignments as $assignment) {
            $rule = Rule::getById($assignment['rule_id']);
            
            if ($rule['type'] === Rule::TYPE_CONDITION) {
                $children = Assignment::getChildren($assignment['id']);
                if (empty($children)) {
                    $this->errors[] = "Condition rule '{$rule['name']}' must have at least one child";
                }
            }
        }

        return empty($this->errors);
    }

    // Add error message
    public function addError($message) {
        $this->errors[] = $message;
    }

    // Get all errors
    public function getErrors() {
        return $this->errors;
    }

    // Check if has errors
    public function hasErrors() {
        return !empty($this->errors);
    }

    // Get errors as string
    public function getErrorsAsString() {
        return implode(", ", $this->errors);
    }

    // Get errors as array for JSON
    public function getErrorsArray() {
        return [
            'errors' => $this->errors,
            'count' => count($this->errors)
        ];
    }
}

?>