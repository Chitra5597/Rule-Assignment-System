<?php
require_once 'config.php';
require_once 'src/Database/db.php';
require_once 'src/Models/Rule.php';
require_once 'src/Models/Group.php';
require_once 'src/Models/Assignment.php';
require_once 'src/Models/Validator.php';

echo "=== Testing Models ===\n\n";

// Test Rule Model
echo "1. Testing Rule Model\n";
$rule = new Rule('Check Rule', 'Condition');
$ruleId = $rule->save();
echo "✓ Rule created with ID: $ruleId\n";

$rule2 = new Rule('Approve', 'Decision');
$ruleId2 = $rule2->save();
echo "✓ Rule created with ID: $ruleId2\n";

$allRules = Rule::getAll();
echo "✓ Total rules: " . count($allRules) . "\n\n";

// Test Group Model
echo "2. Testing Group Model\n";
$group = new Group('Create Group');
$groupId = $group->save();
echo "✓ Group created with ID: $groupId\n";

$allGroups = Group::getAll();
echo "✓ Total groups: " . count($allGroups) . "\n\n";

// Test Assignment Model
echo "3. Testing Assignment Model\n";
$assignment = new Assignment($groupId, $ruleId, 1, 1, null);
$assignmentId = $assignment->save();
echo "✓ Assignment created with ID: $assignmentId\n";

$assignment2 = new Assignment($groupId, $ruleId2, 2, 1, $assignmentId);
$assignmentId2 = $assignment2->save();
echo "✓ Assignment created with ID: $assignmentId2\n\n";

// Test Validator
echo "4. Testing Validator\n";
$validator = new Validator();
$isValid = $validator->validateGroupHierarchy($groupId);
echo "✓ Hierarchy validation: " . ($isValid ? "PASS" : "FAIL") . "\n";
if (!$isValid) {
    echo "  Errors: " . implode(", ", $validator->getErrors()) . "\n";
}

echo "\n✅ All model tests completed\n";

?>