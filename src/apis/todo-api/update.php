<?php
/*
 * File Name: update.php
 * Description: Handles adding new to-do items to the ToDoTable, supporting hierarchical relationships.
 * Sources:
 *    - Quizzer PDO Code (for creating SQL tables
 *   - https://www.php.net/manual/en/pdo.lastinsertid.php (for getting the last inserted ID)
 *    - chatgpt.com (for understanding the structure of the add function)
 */
function updateTodo($inputData) {
    global $dbh;

    header('Content-Type: application/json');

    // Extract and validate the input data
    $id = isset($inputData['id']) ? (int)$inputData['id'] : null;
    $completed = isset($inputData['completed']) ? (bool)$inputData['completed'] : null;
    $title = isset($inputData['title']) ? trim($inputData['title']) : null;
    $parent_id = isset($inputData['parent_id']) ? (int)$inputData['parent_id'] : null;
    $due_date = isset($inputData['due_date']) ? $inputData['due_date'] : null;

    // Validate the id
    if (empty($id)) {
        errorResponse('ID is required.');
    }

    try {
        // Check if the item exists
        $stmt = $dbh->prepare('SELECT * FROM ToDoTable WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            errorResponse('To-Do item not found.');
        }

        // Build the UPDATE query dynamically based on provided fields
        $fieldsToUpdate = [];
        $params = [':id' => $id];

        if ($completed !== null) {
            $fieldsToUpdate[] = 'completed = :completed';
            $params[':completed'] = $completed;
        }

        if ($title !== null) {
            $fieldsToUpdate[] = 'title = :title';
            $params[':title'] = $title;
        }

        if ($parent_id !== null) {
            $fieldsToUpdate[] = 'parent_id = :parent_id';
            $params[':parent_id'] = $parent_id;
        }

        if ($due_date !== null) {
            $fieldsToUpdate[] = 'due_date = :due_date';
            $params[':due_date'] = $due_date;
        }

        if (empty($fieldsToUpdate)) {
            errorResponse('No valid fields provided for update.');
        }

        $fieldsToUpdateStr = implode(', ', $fieldsToUpdate);

        // Prepare and execute the UPDATE statement
        $stmt = $dbh->prepare("UPDATE ToDoTable SET $fieldsToUpdateStr WHERE id = :id");
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->execute();

        // Return a success response
        echo json_encode(['success' => true, 'id' => $id]);
    } catch (PDOException $e) {
        errorResponse('Database error: ' . $e->getMessage());
    }
}

?>
