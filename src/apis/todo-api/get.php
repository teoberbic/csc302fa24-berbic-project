<?php
/*
 * File Name: get.php
 * Description: Handles adding new to-do items to the ToDoTable, supporting hierarchical relationships.
 * Sources:
 *    - Quizzer PDO Code (for interacting with SQL tables)
 *    - chatgpt.com (for understanding the structure of the buildtree function)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../db/db.php'; // Had to change the path to this format because it wasnt working in standard format

// Function to build a hierarchical tree
function buildTree(array $elements, $parentId = null) {
    $branch = [];

    foreach ($elements as $element) { // Loop through each element
        if ($element['parent_id'] == $parentId) { // If the parent_id of the element matches the parent_id passed in
            $children = buildTree($elements, $element['id']);  // Recursively call buildTree to get the children of this element
            if ($children) { 
                $element['children'] = $children; // If there are children, add them to the element
            } else {
                $element['children'] = [];
            }
            $branch[] = $element; // Add the element to the branch
        }
    }
    return $branch;
}

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
