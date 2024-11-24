<?php

/*
 * File Name: add.php
 * Description: Handles adding new to-do items to the ToDoTable, supporting hierarchical relationships.
 * Sources:
 *    - Quizzer PDO Code (for creating SQL tables
 *   - https://www.php.net/manual/en/pdo.lastinsertid.php (for getting the last inserted ID)
 *    - chatgpt.com (for understanding the structure of the add function)
 */

require_once __DIR__ . '/../../db/db.php'; // Had to change the path to this format because it wasnt working in standard format

header('Content-Type: application/json');

// TODO: Make error function so it doesnt get called everywhere
// Erorr response function
function errorResponse($message) {
    global $dbh;
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}


function addTodo(){
    $rawInput = file_get_contents('php://input');

    // Decode JSON and store new var
    $inputData = json_decode($rawInput, true);

    if ($inputData === null && json_last_error() !== JSON_ERROR_NONE) {
        errorResponse('Invalid JSON input.');
    }

    // Extract and validate the input data
    $title = isset($inputData['title']) ? trim($inputData['title']) : ''; // Turnary operator to check if title is set
    $parent_id = isset($inputData['parent_id']) ? (int)$inputData['parent_id'] : null; // Turnary operator to check if parent_id is set

    // Mkae sure the title is not empty
    if (empty($title)) {
        errorResponse('Title is required.');
    }

    // If there is a parent_id present, check if that ID exists in the database
    if ($parent_id !== null) {
        try {
            $stmt = $dbh->prepare('SELECT COUNT(*) FROM ToDoTable WHERE id = :parent_id');
            $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
            $stmt->execute();
            $parentExists = $stmt->fetchColumn();

            if (!$parentExists) {
                errorResponse('Parent item does not exist.');
            }
        } catch (PDOException $e) {
            errorResponse('Database error: ' . $e->getMessage());
        }
    }

    try {
        $stmt = $dbh->prepare('INSERT INTO ToDoTable (title, parent_id) VALUES (:title, :parent_id)');
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_INT | PDO::PARAM_NULL);

        $stmt->execute();

        
        $newItemId = $dbh->lastInsertId();

        
        echo json_encode(['success' => true, 'id' => $newItemId]);
    } catch (PDOException $e) {
        errorResponse('Database error: ' . $e->getMessage());
    }
}
?>
