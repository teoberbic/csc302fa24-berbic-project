
<?php
/*
File Name: update.php
Description: The router php f.
Sources: 
    - Quizzer PDO Code (for preparing and excecuting SQL statements)
    - https://www.w3schools.com/php/php_switch.asp (switch statements instead of if-else for cleaner code)
*/
require_once __DIR__ . '/../../db/db.php';
header('Content-Type: application/json');

/**
 * Updates a new idea to the database.
 *
 * @param $ideaId The idea ID (optional for now, might be useful if needed).
 * @param $name The name of the idea.
 * @param $description The description of the idea.
 * @param $category The category of the idea.
 * @param $action_priority The action_priority of the idea.
 *
 * @return JSON response indicating success or failure.
 */

function updateIdea($ideaId, $name, $description, $category, $action_priority) {
    global $dbh;
    try {
        $statement = $dbh->prepare(
            'UPDATE IdeasTable
            Set name = :name, description = :description, category = :category, action_priority = :action_priority
            WHERE id = :id;'
        );
        $statement->execute([
            ':id' => $ideaId,
            ':name' => $name,
            ':description' => $description,
            ':category' => $category,
            ':action_priority' => $action_priority
        ]);

        return json_encode(['success (item updated)' => true]);

    } catch (PDOException $e) {
        return json_encode(['success' => false, 'error' => "Error updating idea: " . $e->getMessage()]);
    }
}


?>
