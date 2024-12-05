<?php
/*
File Name: db.php
Description: Initializes an SQLite database, creates the IdeasTable, and ensures server compatibility with error handling.
Sources: 
    - Quizzer PDO Code (for creating SQL tables)
    - https://www.w3schools.com/php/php_switch.asp (switch statements instead of if-else for cleaner code)
    - https://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/ (for understanding the high level structure of hierarchical parent child relationships in relational databases)
    - chatgpt.com (for understanding the structure of the buildtree function)
*/

// SQLite will look for a file with this name, or create one if it can't find it.
$dbName = 'data.db';


// Leave this alone. It checks if you have a directory named www-data in
// you home directory (on a *nix server). If so, the database file is
// sought/created there. Otherwise, it uses the current directory.
// The former works on digdug where I've set up the www-data folder for you;
// the latter should work on your computer.
$matches = [];
preg_match('#^/~([^/]*)#', $_SERVER['REQUEST_URI'], $matches);
$homeDir = count($matches) > 1 ? $matches[1] : '';
$dataDir = "/home/$homeDir/www-data";
if(!file_exists($dataDir)){
    $dataDir = __DIR__;
}
$dbh = new PDO("sqlite:$dataDir/$dbName");
// Set our PDO instance to raise exceptions when errors are encountered.
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// tells the database that i can have child items connected to my parent items
$dbh->exec('PRAGMA foreign_keys = ON;'); // Without this, SQLite will not look at foreign keys 
// This will enable a cascading delete so ff you delete a parent item, the database can automatically delete all its child items.

/**
 * Returns an associative array with two fields:
 *  - success: false
 *  - error:  $message
 * 
 * @return An associative array describing the error.
 */
function error($message){
    return [
        'success' => false, 
        'error' => $message
    ];
}

/**
 * Creates all of the tables for this project:
 *  - IdeasTable
 *  - ToDoTable
 */
function createTables(){
    global $dbh;

// Create the Ideas table.
try {
    $dbh->exec('create table if not exists IdeasTable(' .
        'id integer primary key autoincrement, ' .
        'name text UNIQUE, ' .
        'description text, ' .
        'category text, ' .
        'action_priority text, ' .
        'createdAt datetime default(datetime()), ' .
        'updatedAt datetime default(datetime()))');
    } catch (PDOException $e) {
        echo "There was an error creating the Ideas Table: " . $e->getMessage();
    }


// Create the ToDoTable
try {
    $dbh->exec('create table if not exists ToDoTable(' .
        'id integer primary key autoincrement, ' .
        'title text not null, ' .
        'completed boolean default false, ' .
        'parent_id integer, ' .
        'foreign key (parent_id) references ToDoTable(id) on delete cascade)');
} catch (PDOException $e) {
    echo "There was an error creating the ToDoTable: " . $e->getMessage();
}



}

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
createTables();

?>