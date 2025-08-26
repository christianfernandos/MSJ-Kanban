<?php
// Simple API untuk test kanban tanpa Laravel dependencies
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Get request method dan path
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$query_string = $_SERVER['QUERY_STRING'];

// Parse URL path
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/simple_api.php', '', $path);

// Response data
$response = [
    'success' => true,
    'message' => 'Simple Kanban API Working',
    'data' => [
        'method' => $method,
        'path' => $path,
        'query' => $query_string,
        'timestamp' => date('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION,
    ]
];

// Simulate different endpoints
if (strpos($path, '/test-task') !== false || isset($_GET['action']) && $_GET['action'] === 'test-task') {
    $response['data']['task'] = [
        'id' => 123,
        'title' => 'Sample Kanban Task',
        'description' => 'This is a test task for kanban board',
        'status' => 'in_progress',
        'priority' => 'high',
        'assigned_to' => 'test_user',
        'created_at' => '2025-08-25 14:30:00',
        'updated_at' => date('Y-m-d H:i:s')
    ];
    $response['message'] = 'Task details retrieved successfully';
}

if (strpos($path, '/get-task') !== false || isset($_GET['action']) && $_GET['action'] === 'get-task') {
    $task_id = $_GET['id'] ?? 'unknown';
    $response['data']['task'] = [
        'id' => $task_id,
        'title' => "Task #{$task_id}",
        'description' => "Description for task {$task_id}",
        'status' => 'pending',
        'priority' => 'medium',
        'board_id' => 1,
        'list_id' => 2,
        'created_at' => '2025-08-25 10:00:00',
        'updated_at' => date('Y-m-d H:i:s')
    ];
    $response['message'] = "Task {$task_id} details loaded";
}

// Output JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
?>
