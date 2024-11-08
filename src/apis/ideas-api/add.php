
<?php

require_once __DIR__ . '/../../db/db.php';
header('Content-Type: application/json');

/**
 * Adds a new idea to the database.
 *
 * @param $ideaId The idea ID (optional for now, might be useful if needed).
 * @param $name The name of the idea.
 * @param $description The description of the idea.
 * @param $category The category of the idea.
 * @param $action_priority The action_priority of the idea.
 *
 * @return JSON response indicating success or failure.
 */

function addIdea($ideaId, $name, $description, $category, $action_priority) {
    global $dbh;

    try {
        $statement = $dbh->prepare(
            'INSERT INTO IdeasTable (name, description, category, action_priority) 
             VALUES (:name, :description, :category, :action_priority)'
        );
        $statement->execute([
            ':name' => $name,
            ':description' => $description,
            ':category' => $category,
            ':action_priority' => $action_priority
        ]);

        // Get the last inserted ID to confirm success
        $id = $dbh->lastInsertId();

        return json_encode(['success' => true, 'id' => $id]);

    } catch (PDOException $e) {
        return json_encode(['success' => false, 'error' => "Error adding idea: " . $e->getMessage()]);
    }
}

?>
