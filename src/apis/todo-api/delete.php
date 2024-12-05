<?php
// delete.php

/*
 * File Name: delete.php
 * Description: Handles deleting to-do items from the ToDoTable, including any hierarchical child items.
 */

require_once __DIR__ . '/../../db/db.php';

header('Content-Type: application/json');

// Function to return an error message as JSON

function deleteTodoItem($id){
    global $dbh;
    // Validate the id
    if (empty($id)) {
        errorResponse('ID is required.');
    }

    try {
        // Check if the item exists
        $stmt = $dbh->prepare('SELECT COUNT(*) FROM ToDoTable WHERE id = :id');
        $stmt->execute(
            [':id' => $id]);
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


