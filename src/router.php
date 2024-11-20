<?php
header('Content-Type: application/json');

// Include necessary files
require_once 'apis/ideas-api/add.php';
require_once 'apis/ideas-api/delete.php';
require_once 'apis/ideas-api/update.php';
require_once 'apis/ideas-api/get.php';
require_once 'apis/todo-api/add.php';
require_once 'apis/todo-api/delete.php';
require_once 'apis/todo-api/update.php';
require_once 'apis/todo-api/get.php';

// Get the action and category from the request
$action = $_GET['action'] ?? '';
$category = $_GET['category'] ?? ''; // 'ideas' or 'todo'

// Route based on the action and category
switch ($category) {
    case 'ideas':
        switch ($action) {
            case 'add':
                // Reads the raw JSON data from the request body.
                // Decodes this JSON into a PHP associative array, which is stored in $data.
                $data = json_decode(file_get_contents("php://input"), true); 
                
                // Pass the decoded data to the addIdea function
                echo json_encode(addIdea(
                    $data['ideaId'] ?? null,  // Assuming you might want to use this in the future
                    $data['name'],
                    $data['description'],
                    $data['category'],
                    $data['priority']));
                break;
            case 'delete':
                deleteIdea();
                break;
            case 'update':
                updateIdea();
                break;
            case 'get':
                echo getIdeas();
                break;
            default:
                http_response_code(404);
                echo json_encode(['message' => 'Action not found for ideas']);
                break;
        }
        break;

    case 'todo':
        switch ($action) {
            case 'add':
                addTodo();
                break;
            case 'delete':
                deleteTodo();
                break;
            case 'update':
                updateTodo();
                break;
            case 'get':
                getTodos();
                break;
            default:
                http_response_code(404);
                echo json_encode(['message' => 'Action not found for todo']);
                break;
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['message' => 'Category not found']);
        break;
}
