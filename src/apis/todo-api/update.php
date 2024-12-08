<?php
    /*
    * File Name: update.php
    * Description: Handles updating existing to-do items in the ToDoTable, supporting hierarchical relationships.
    * Sources:
    *    - Quizzer PDO Code (for creating SQL tables)
    *    - https://www.php.net/manual/en/pdo.lastinsertid.php (for getting the last inserted ID)
    *    - chatgpt.com (for understanding the structure of the update function)
    *    - VSCode Copilot (for comments describing the code)
    */

    header('Content-Type: application/json');

    /**
     * Updates an existing to-do item in the database.
     *
     * @param array $inputData The input data containing fields to update.
     *
     * @return void Outputs a JSON response indicating success or failure.
     */
    function updateTodo($inputData) {
        global $dbh;

        // Extract and validate the input data from the request payload
        $id = isset($inputData['id']) ? (int)$inputData['id'] : null; // Extract and cast the 'id' to an integer, or set it to null if not provided
        $completed = isset($inputData['completed']) ? (bool)$inputData['completed'] : null; // Extract and cast 'completed' to a boolean, or set it to null
        $title = isset($inputData['title']) ? trim($inputData['title']) : null; // Extract 'title', trim whitespace, or set it to null if not provided
        $parent_id = isset($inputData['parent_id']) ? (int)$inputData['parent_id'] : null; // Extract and cast 'parent_id' to an integer, or set it to null
        $due_date = isset($inputData['due_date']) ? $inputData['due_date'] : null; // Extract 'due_date' as is, or set it to null if not provided

        // Validate the 'id' field
        if (empty($id)) {
            errorResponse('ID is required.'); // Send an error response if 'id' is missing or empty
        }

        try {
            // Check if the item exists in the database
            $stmt = $dbh->prepare('SELECT * FROM ToDoTable WHERE id = :id'); // Prepare the SELECT query
            $stmt->execute([':id' => $id]); // Execute the query with the 'id' parameter
            $item = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the item data

            if (!$item) {
                errorResponse('To-Do item not found.'); // Send an error response if the item does not exist
            }

            // Build the UPDATE query dynamically based on provided fields
            $fieldsToUpdate = []; // Initialize an array to hold fields to update
            $params = [':id' => $id]; // Initialize parameters with the 'id'

            if ($completed !== null) {
                $fieldsToUpdate[] = 'completed = :completed'; // Add 'completed' to fields to update
                $params[':completed'] = $completed; // Add its value to parameters
            }

            if ($title !== null) {
                $fieldsToUpdate[] = 'title = :title'; // Add 'title' to fields to update
                $params[':title'] = $title; // Add its value to parameters
            }

            if ($parent_id !== null) {
                $fieldsToUpdate[] = 'parent_id = :parent_id'; // Add 'parent_id' to fields to update
                $params[':parent_id'] = $parent_id; // Add its value to parameters
            }

            if ($due_date !== null) {
                $fieldsToUpdate[] = 'due_date = :due_date'; // Add 'due_date' to fields to update
                $params[':due_date'] = $due_date; // Add its value to parameters
            }

            // If no valid fields are provided, send an error response
            if (empty($fieldsToUpdate)) {
                errorResponse('No valid fields provided for update.');
            }

            // Convert the fields to a string for the SQL query
            $fieldsToUpdateStr = implode(', ', $fieldsToUpdate);

            // Prepare and execute the UPDATE statement
            $stmt = $dbh->prepare("UPDATE ToDoTable SET $fieldsToUpdateStr WHERE id = :id"); // Prepare the UPDATE query
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value); // Bind each parameter to the query
            }
            $stmt->execute(); // Execute the prepared statement

            // Return a success response
            echo json_encode(['success' => true, 'id' => $id]);
        } catch (PDOException $e) {
            errorResponse('Database error: ' . $e->getMessage()); // Send an error response if a database error occurs
        }
    }

?>
