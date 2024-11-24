
<?php
/*
File Name: delete.php
Description: Handles deleting ideas from the IdeasTable.
Sources: 
    - Quizzer PDO Code (for deleting from SQL tables)
*/
require_once __DIR__ . '/../../db/db.php';
header('Content-Type: application/json');

/**
 * deletes a new idea to the database.
 *
 * @param $ideaId The idea ID (optional for now, might be useful if needed).
 *
 * @return JSON response indicating success or failure.
 */

function deleteIdea($ideaId) {
    global $dbh;
    try {
        $statement = $dbh->prepare(
            'DELETE FROM ideasTable WHERE id = :id'
        );
        $statement->execute([
            ':id' => $ideaId,
        ]);

        return json_encode(['success (item deleted)' => true, 'message' => 'Idea deleted successfully.']);

    } catch (PDOException $e) {
        return json_encode(['success' => false, 'error' => "Error deleting idea: " . $e->getMessage()]);
    }
}


?>
