<?php
/*
File Name: router.php
Description: The router.php that redirects requests to specific API action handlers depending on category and action sent in.
Sources: 
    - https://chatgpt.com (null coalescing operator (?? operator) as cleaner alternative to if-else)
    - ChatGPT (isset() function to check if a variable is set and is not NULL)
    - https://www.w3schools.com/php/php_switch.asp (switch statements instead of if-else for cleaner code) 
    - VSCode Copilot (for suggesting the use of file_get_contents("php://input"), i think it looks cleaner than using $_POST)

*/
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
$id = $_GET['id'] ?? null; // Do this for all the actions that require an id

// Route based on the action and category
switch ($category) {
    case 'ideas':
        switch ($action) {
            case 'add':
                // Reads the raw JSON data from the request body.
                // Decodes this JSON into a PHP associative array, which is stored in $data.
                $data = json_decode(file_get_contents("php://input"), true);  // VSCode Copilot suggested this cleaner alternative to $_POST
                
                // Pass the decoded data to the addIdea function
                echo json_encode(addIdea(
                    $data['ideaId'] ?? null,  // If i want to add sub-ideas, i can use this field
                    $data['name'],
                    $data['description'],
                    $data['category'],
                    $data['priority']));
                    break;
                
            case 'update':
                $data = json_decode(file_get_contents("php://input"), true); // VSCode Copilot suggested this cleaner alternative to $_POST
                
                // Pass the decoded data and id to the updateIdea function
                echo json_encode(updateIdea(
                    $id,  // Use the captured id
                    $data['name'],
                    $data['description'],
                    $data['category'],
                    $data['priority']));
                break;

            case 'get':
                // Handle the 'get' action for ideas
                echo getIdeas();
                break;

            case 'delete':
                // Handle the 'delete' action for ideas
                echo json_encode(deleteIdea($id));
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
                // Handle the 'add' action for todo items
                // ChatGPT suggested using the null coalescing operator (??) as a cleaner alternative to if-else
                // ChatGPT isset() function to check if a variable is set and is not NULL
                $input = json_decode(file_get_contents('php://input'), true); // Decode as associative array
                $title = isset($input['title']) ? trim($input['title']) : '';
                $parent_id = isset($input['parent_id']) ? (int)$input['parent_id'] : null;
                
                if ($title) {
                    addTodo($title, $parent_id);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Title is required']);
                }
                break;

            case 'delete': 
                // Handle the 'delete' action for todo items
                $data = json_decode(file_get_contents("php://input"), true); // VSCode Copilot suggested this cleaner alternative to $_POST
                json_encode(deleteTodoItem($data['id']));
                break;

            case 'update':
                // Handle the 'update' action for todo items
                $input = json_decode(file_get_contents('php://input'), true); // VSCode Copilot suggested this cleaner alternative to $_POST
                updateTodo($input);
                break;
            case 'get':

                // Handle the 'get' action for todo items
                echo json_encode(fetchTodos());
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
