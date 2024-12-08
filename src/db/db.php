<?php
/*
File Name: db.php
Description: Initializes an SQLite database, creates the IdeasTable and ToDoTable, and ensures server compatibility with error handling.
Sources: 
    - Quizzer PDO Code (for creating SQL tables)
    - https://www.w3schools.com/php/php_switch.asp (switch statements instead of if-else for cleaner code)
    - https://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/ (for understanding the high-level structure of hierarchical parent-child relationships in relational databases)
    - chatgpt.com (for understanding the structure of the buildTree function)
    - VSCode Copilot (for comments describing the code)
*/

// SQLite will look for a file with this name, or create one if it can't find it.
$dbName = 'data.db';

// Determine the data directory for storing the database file
// Check if the URL contains a home directory (for *nix servers like Digdug)
$matches = [];
preg_match('#^/~([^/]*)#', $_SERVER['REQUEST_URI'], $matches);
$homeDir = count($matches) > 1 ? $matches[1] : '';
$dataDir = "/home/$homeDir/www-data";

// If the directory does not exist, use the current directory
if (!file_exists($dataDir)) {
    $dataDir = __DIR__;
}

// Initialize the PDO instance for SQLite
$dbh = new PDO("sqlite:$dataDir/$dbName");

// Set the PDO instance to raise exceptions for database errors
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Enable SQLite's foreign key constraints
$dbh->exec('PRAGMA foreign_keys = ON;'); 
// This ensures that operations like cascading deletes work when parent-child relationships exist.

/**
 * Returns an associative array with an error response.
 *
 * @param string $message The error message to include in the response.
 * @return array An associative array with success and error fields.
 */
function error($message) {
    return [
        'success' => false,
        'error' => $message
    ];
}

/**
 * Creates the necessary tables for the application.
 * - IdeasTable: Stores ideas with metadata like name, description, category, etc.
 * - ToDoTable: Stores hierarchical to-do items with parent-child relationships.
 */
function createTables() {
    global $dbh;

    // Create the IdeasTable
    try {
        $dbh->exec(
            'CREATE TABLE IF NOT EXISTS IdeasTable (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT UNIQUE,
                description TEXT,
                category TEXT,
                action_priority TEXT,
                createdAt DATETIME DEFAULT (DATETIME()),
                updatedAt DATETIME DEFAULT (DATETIME())
            )'
        );
    } catch (PDOException $e) {
        echo "There was an error creating the Ideas Table: " . $e->getMessage();
    }

    // Create the ToDoTable
    try {
        $dbh->exec(
            'CREATE TABLE IF NOT EXISTS ToDoTable (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                completed BOOLEAN DEFAULT FALSE,
                parent_id INTEGER,
                FOREIGN KEY (parent_id) REFERENCES ToDoTable(id) ON DELETE CASCADE
            )'
        );
    } catch (PDOException $e) {
        echo "There was an error creating the ToDoTable: " . $e->getMessage();
    }
}

/**
 * Builds a hierarchical tree structure from a flat array of elements.
 *
 * @param array $elements The flat array of elements to convert.
 * @param int|null $parentId The parent ID to filter the elements by.
 * @return array The hierarchical tree structure.
 */
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

// Call createTables to ensure the tables are created on script execution
createTables();

?>
