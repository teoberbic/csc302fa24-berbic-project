<?php
/*
 * File Name: get.php
 * Description: Handles retreiving of items in the IdeasTable.
 * Sources:
 *    - Quizzer PDO Code (for interacting with SQL tables)
 */
require_once __DIR__ . '/../../db/db.php';
header('Content-Type: application/json');

function getIdeas() {
    global $dbh;
    $ideas = [];
    try {
        $statement = $dbh->prepare('SELECT * FROM IdeasTable');
        $statement->execute();
        $ideas = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => "There was an error fetching rows from Ideas: $e"]);
        return;
    }

    echo json_encode([
        'success' => true,
        'ideas' => $ideas
    ]);
}
?>
