<?php
// Simple API testing

$baseUrl = 'http://localhost:8000/api.php';

echo "=== Testing Rule Assignment System API ===\n\n";

// Test 1: Create a Rule
echo "1. Creating Rule...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl?action=create-rule");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'name' => 'Check Rule',
    'type' => 'Condition'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
echo $response . "\n\n";

// Test 2: Get all rules
echo "2. Getting all rules...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl?action=get-rules");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
echo $response . "\n\n";

// Test 3: Create a Group
echo "3. Creating Group...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl?action=create-group");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'name' => 'Create Group'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
echo $response . "\n\n";

echo "✅ API Testing Complete\n";
?>