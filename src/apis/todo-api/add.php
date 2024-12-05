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


function addTodo($title, $parent_id = null){
    global $dbh;

    // Mkae sure the title is not empty
    if (empty($title)) {
        errorResponse('Title is required.');
    }

    // Check if the parent_id exists
    if ($parent_id !== null) {
        try {
            $stmt = $dbh->prepare('SELECT COUNT(*) FROM ToDoTable WHERE id = :parent_id');
            
            // Execute the statement wsith the parameters
            $stmt->execute([
                ':parent_id' => $parent_id
            ]);
            
            $parentExists = $stmt->fetchColumn();
    
            if (!$parentExists) {
                errorResponse('Parent item does not exist.');
            }
        } catch (PDOException $e) {
            errorResponse('Database error: ' . $e->getMessage());
        }
    }

    try {
        $stmt = $dbh->prepare('INSERT INTO ToDoTable (title, parent_id) 
                                VALUES (:title, :parent_id)');
        
        $stmt->execute([
            ':title' => $title,
            ':parent_id' => $parent_id
        ]);

        $newItemId = $dbh->lastInsertId();

        // Return a success response with the last inserted ID
        echo json_encode(['success' => true, 'id' => $newItemId]);
    } catch (PDOException $e) {
        errorResponse('Database error: ' . $e->getMessage());
    }
}
?>
