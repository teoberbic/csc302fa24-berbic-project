<?php
    // delete.php

    /*
    * File Name: delete.php
    * Description: Handles deleting to-do items from the ToDoTable, including any hierarchical child items.
    * Sources:
    *       - VSCode Copilot (for comments describing the code)
    */

    require_once __DIR__ . '/../../db/db.php';

    header('Content-Type: application/json');

    /**
     * Sends an error response in JSON format and exits the script.
     *
     * @param string $message The error message to include in the response.
     *
     * @return void.
     */
    function errorResponse($message) {
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }

    /**
     * Deletes a to-do item and its hierarchical child items from the database.
     *
     * @param int $id The ID of the to-do item to delete.
     *
     * @return void. Outputs a JSON response indicating success or failure.
     */
    function deleteTodoItem($id) {
        global $dbh;

        // Validate the ID
        if (empty($id)) {
            errorResponse('ID is required.');
        }

        try {
            // Check if the item exists
            $stmt = $dbh->prepare('SELECT COUNT(*) FROM ToDoTable WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $itemExists = $stmt->fetchColumn();

            if (!$itemExists) {
                errorResponse('To-Do item not found.');
            }

            // Delete the item (child items will be deleted automatically if ON DELETE CASCADE is set)
            $stmt = $dbh->prepare('DELETE FROM ToDoTable WHERE id = :id');
            $stmt->execute([':id' => $id]);

            // Return a success response
            echo json_encode(['success' => true, 'message' => 'To-Do item deleted successfully.']);
        } catch (PDOException $e) {
            errorResponse('Database error: ' . $e->getMessage());
        }
    }
?>
