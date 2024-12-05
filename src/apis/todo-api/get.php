<?php
/*
 * File Name: get.php
 * Description: Handles adding new to-do items to the ToDoTable, supporting hierarchical relationships.
 * Sources:
 *    - Quizzer PDO Code (for interacting with SQL tables)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../db/db.php'; // Had to change the path to this format because it wasnt working in standard format


function fetchTodos() {
    global $dbh;
    try {
        $stmt = $dbh->prepare(
            'SELECT id, title, completed, parent_id FROM ToDoTable ORDER BY id ASC'
        );
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tree = buildTree($items);

        return $tree;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
