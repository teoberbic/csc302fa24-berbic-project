<?php
    /*
    File Name: add.php
    Description: This file contains the function to add a new idea to the database.
    Sources: 
        - Quizzer PDO Code (for preparing and executing SQL statements)
        - https://www.w3schools.com/php/php_switch.asp (switch statements instead of if-else for cleaner code)
        - VSCode Copilot (for comments describing the code)
    */
    require_once __DIR__ . '/../../db/db.php'; // Had to change the path to this format because it wasnt working in standard format
    header('Content-Type: application/json');

    /**
     * Adds a new idea to the database.
     *
     * @param $ideaId The idea ID (optional for now, might be useful if needed).
     * @param $name The name of the idea.
     * @param $description The description of the idea.
     * @param $category The category of the idea.
     * @param $action_priority The action priority of the idea.
     *
     * @return JSON response indicating success or failure.
     */
    function addIdea($ideaId, $name, $description, $category, $action_priority) {
        global $dbh;

        try {
            // Check if the idea name already exists
            $checkStatement = $dbh->prepare('SELECT COUNT(*) FROM IdeasTable WHERE name = :name');
            $checkStatement->execute([':name' => $name]);
            $count = $checkStatement->fetchColumn();

            if ($count > 0) {
                return json_encode(['success' => false, 'error' => 'Idea with this name already exists.']);
            }

            // Insert the new idea
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

            $id = $dbh->lastInsertId();

            return json_encode(['success' => true, 'id' => $id]);

        } catch (PDOException $e) {
            return json_encode(['success' => false, 'error' => "Error adding idea: " . $e->getMessage()]);
        }
    }
?>
