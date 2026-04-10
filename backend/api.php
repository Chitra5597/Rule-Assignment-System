<?php
require_once 'config.php';
require_once 'src/Database/db-context.php';
require_once 'src/Models/Rule.php';
require_once 'src/Models/Group.php';
require_once 'src/Models/Assignment.php';
require_once 'src/Validators/Validator.php';

$action = $_GET['action'] ?? null;
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($action) {
        // RULES
        case 'get-rules':
            $rules = Rule::getAll();
            response(['data' => $rules]);
            break;

        case 'create-rule':
            if (!isset($input['name']) || !isset($input['type'])) {
                response(['error' => 'Name and type required'], 400);
            }
            $errors = Rule::validate($input['name'], $input['type']);
            if (!empty($errors)) {
                response(['errors' => $errors], 400);
            }
            $rule = new Rule($input['name'], $input['type']);
            $id = $rule->save();
            response(['success' => true, 'id' => $id], 201);
            break;

        case 'get-rules-by-type':
            $type = $_GET['type'] ?? null;
            if (!$type) {
                response(['error' => 'Type required'], 400);
            }
            $rules = Rule::getByType($type);
            response(['data' => $rules]);
            break;

        // GROUPS
        case 'get-groups':
            $groups = Group::getAll();
            response(['data' => $groups]);
            break;

        case 'create-group':
            if (!isset($input['name'])) {
                response(['error' => 'Name required'], 400);
            }
            $errors = Group::validate($input['name']);
            if (!empty($errors)) {
                response(['errors' => $errors], 400);
            }
            $group = new Group($input['name']);
            $id = $group->save();
            response(['success' => true, 'id' => $id], 201);
            break;

        case 'get-group':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                response(['error' => 'ID required'], 400);
            }
            $group = Group::getById($id);
            if (!$group) {
                response(['error' => 'Group not found'], 404);
            }
            response(['data' => $group]);
            break;

        // ASSIGNMENTS
        case 'assign-rule':
            if (!isset($input['group_id']) || !isset($input['rule_id']) || !isset($input['level'])) {
                response(['error' => 'Missing required fields'], 400);
            }

            $validator = new Validator();
            $parentId = $input['parent_id'] ?? null;

            if (!$validator->validateRuleAssignment($input['group_id'], $input['rule_id'], $input['level'], $parentId)) {
                response(['errors' => $validator->getErrors()], 400);
            }

            $orderNum = Assignment::getNextOrder($input['group_id'], $input['level'], $parentId);
            $assignment = new Assignment(
                $input['group_id'],
                $input['rule_id'],
                $input['level'],
                $orderNum,
                $parentId
            );
            $id = $assignment->save();

            // Validate hierarchy
            $group = new Group('', $input['group_id']);
            if (!$validator->validateGroupHierarchy($input['group_id'])) {
                $assignment->delete();
                response(['errors' => $validator->getErrors()], 400);
            }

            response(['success' => true, 'id' => $id], 201);
            break;

        case 'get-hierarchy':
            $groupId = $_GET['group_id'] ?? null;
            if (!$groupId) {
                response(['error' => 'Group ID required'], 400);
            }
            $group = Group::getById($groupId);
            if (!$group) {
                response(['error' => 'Group not found'], 404);
            }
            $groupModel = new Group($group['name'], $group['id']);
            $hierarchy = $groupModel->getHierarchy();
            response(['group' => $group, 'hierarchy' => $hierarchy]);
            break;

        case 'remove-assignment':
            if (!isset($input['id'])) {
                response(['error' => 'ID required'], 400);
            }
            $assignment = Assignment::getById($input['id']);
            if (!$assignment) {
                response(['error' => 'Assignment not found'], 404);
            }

            $validator = new Validator();
            $assignmentModel = new Assignment(
                $assignment['group_id'],
                $assignment['rule_id'],
                $assignment['level'],
                $assignment['order_num'],
                $assignment['parent_id'],
                $assignment['id']
            );
            $assignmentModel->delete();

            // Validate hierarchy
            if (!$validator->validateGroupHierarchy($assignment['group_id'])) {
                response(['errors' => $validator->getErrors()], 400);
            }

            response(['success' => true, 'message' => 'Removed']);
            break;

        default:
            response(['error' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    response(['error' => $e->getMessage()], 500);
}

function response($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

?>