<?php
/*
File Name: router.php
Description: Routes API requests to the appropriate handlers based on the `category` and `action` provided in the request.
Sources: 
    - https://chatgpt.com (null coalescing operator (?? operator) as a cleaner alternative to if-else)
    - ChatGPT (isset() function to check if a variable is set and is not NULL)
    - https://www.w3schools.com/php/php_switch.asp (switch statements instead of if-else for cleaner code) 
    - VSCode Copilot (suggested the use of file_get_contents("php://input") as a cleaner alternative to $_POST)
    - VSCode Copilot (for comments describing the code)
*/

header('Content-Type: application/json');

// Include the necessary API files for handling ideas and to-do operations
require_once 'apis/ideas-api/add.php';
require_once 'apis/ideas-api/delete.php';
require_once 'apis/ideas-api/update.php';
require_once 'apis/ideas-api/get.php';
require_once 'apis/todo-api/add.php';
require_once 'apis/todo-api/delete.php';
require_once 'apis/todo-api/update.php';
require_once 'apis/todo-api/get.php';

// Extract the action and category from the query parameters
$action = $_GET['action'] ?? ''; // Default to an empty string if not provided
$category = $_GET['category'] ?? ''; // Category can be 'ideas' or 'todo'
$id = $_GET['id'] ?? null; // Extract the ID if provided, used for update and delete operations

// Route requests based on the category (e.g., 'ideas' or 'todo')
switch ($category) {
    case 'ideas': // Handle API actions related to ideas
        switch ($action) {
            case 'add': // Handle adding an idea
                $data = json_decode(file_get_contents("php://input"), true); // Parse the input JSON as an associative array
                echo json_encode(addIdea(
                    $data['ideaId'] ?? null, // Handle sub-ideas using this field if provided
                    $data['name'], 
                    $data['description'], 
                    $data['category'], 
                    $data['priority']
                ));
                break;

            case 'update': // Handle updating an idea
                $data = json_decode(file_get_contents("php://input"), true);
                echo json_encode(updateIdea(
                    $id, // Pass the extracted ID
                    $data['name'], 
                    $data['description'], 
                    $data['category'], 
                    $data['priority']
                ));
                break;

            case 'get': // Handle retrieving all ideas
                echo json_encode(getIdeas()); // Fetch ideas from the database
                break;

            case 'delete': // Handle deleting an idea
                echo json_encode(deleteIdea($id)); // Call the delete function with the ID
                break;

            default: // Handle unsupported actions for ideas
                http_response_code(404); // Set the HTTP response code to 404 (Not Found)
                echo json_encode(['message' => 'Action not found for ideas']); // Return an error message
                break;
        }
        break;

    case 'todo': // Handle API actions related to to-do items
        switch ($action) {
            case 'add': // Handle adding a to-do item
                $input = json_decode(file_get_contents('php://input'), true); // Parse the input JSON
                $title = isset($input['title']) ? trim($input['title']) : ''; // Extract and trim the title
                $parent_id = isset($input['parent_id']) ? (int)$input['parent_id'] : null; // Extract and cast parent_id

                if ($title) { // Ensure title is provided
                    addTodo($title, $parent_id); // Add the to-do item
                } else {
                    http_response_code(400); // Set the HTTP response code to 400 (Bad Request)
                    echo json_encode(['message' => 'Title is required']); // Return an error message
                }
                break;

            case 'delete': // Handle deleting a to-do item
                $data = json_decode(file_get_contents("php://input"), true);
                json_encode(deleteTodoItem($data['id'])); // Call the delete function with the ID
                break;

            case 'update': // Handle updating a to-do item
                $input = json_decode(file_get_contents('php://input'), true);
                updateTodo($input); // Pass the input data to the update function
                break;

            case 'get': // Handle retrieving all to-do items
                echo fetchTodos(); // Fetch to-do items from the database
                break;

            default: // Handle unsupported actions for to-do items
                http_response_code(404); // Set the HTTP response code to 404 (Not Found)
                echo json_encode(['message' => 'Action not found for todo']); // Return an error message
                break;
        }
        break;

    default: // Handle unsupported categories
        http_response_code(404); // Set the HTTP response code to 404 (Not Found)
        echo json_encode(['message' => 'Category not found']); // Return an error message
        break;
}
?>
