<?php

    /*
    * File Name: add.php
    * Description: Handles adding new to-do items to the ToDoTable, supporting hierarchical relationships.
    * Sources:
    *    - Quizzer PDO Code (for creating SQL tables)
    *    - https://www.php.net/manual/en/pdo.lastinsertid.php (for getting the last inserted ID)
    *    - chatgpt.com (for understanding the structure of the add function)
    *    - VSCode Copilot (for comments describing the code)
    */

    require_once __DIR__ . '/../../db/db.php'; // Adjusted the path to match the directory structure

    header('Content-Type: application/json');


    /**
     * Adds a new to-do item to the ToDoTable.
     *
     * @param string $title The title of the to-do item.
     * @param int|null $parent_id The ID of the parent to-do item (optional).
     *
     * @return void. Outputs a JSON response indicating success or failure.
     */
    function addTodo($title, $parent_id = null) {
        global $dbh;

        // Ensure the title is not empty
        if (empty($title)) {
            errorResponse('Title is required.');
        }

        // Check if the parent_id exists, if provided
        if ($parent_id !== null) {
            try {
                $stmt = $dbh->prepare('SELECT COUNT(*) FROM ToDoTable WHERE id = :parent_id');
                
                // Execute the statement with the parameters
                $stmt->execute([':parent_id' => $parent_id]);
                $parentExists = $stmt->fetchColumn();
        
                if (!$parentExists) {
                    errorResponse('Parent item does not exist.');
                }
            } catch (PDOException $e) {
                errorResponse('Database error: ' . $e->getMessage());
            }
        }

        // Insert the new to-do item into the database
        try {
            $stmt = $dbh->prepare('INSERT INTO ToDoTable (title, parent_id) VALUES (:title, :parent_id)');
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
