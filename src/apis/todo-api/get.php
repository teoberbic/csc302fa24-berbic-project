<?php
    /*
    * File Name: get.php
    * Description: Handles fetching to-do items from the ToDoTable, supporting hierarchical relationships.
    * Sources:
    *    - Quizzer PDO Code (for interacting with SQL tables)
    *    - VSCode Copilot (for comments describing the code)
    */

    header('Content-Type: application/json');

    require_once __DIR__ . '/../../db/db.php'; // Adjusted the path to match the directory structure

    /**
     * Fetches to-do items from the database and builds a hierarchical tree structure.
     *
     * @return array The hierarchical tree of to-do items or outputs a JSON error response on failure.
     */
    function fetchTodos() {
        global $dbh;
        try {
            $stmt = $dbh->prepare(
                'SELECT id, title, completed, parent_id FROM ToDoTable ORDER BY id ASC'
            );
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $tree = buildTree($items);

            echo json_encode(['success' => true, 'todos' => $tree]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
?>


