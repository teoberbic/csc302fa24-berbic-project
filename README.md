Brand Analytics Web App
This web application is designed to track and manage ideas and to-do items for your brand. It features an Ideas Page and a To-Do List Page, each of which allows users to add, update, or delete items. The app also displays to-do items on a calendar on the home page.
The application is built with PHP, JavaScript (AJAX), HTML/CSS, and uses SQLite as the database.
Table of Contents
Features
File Structure
Installation
Database Setup
Usage
API Endpoints

Features
Ideas Page: View, add, update, and delete ideas with fields for name, description, category, priority, creation date, and last updated date.
To-Do List Page: Manage to-do items in a similar fashion, with integration to display items on a calendar on the home page.
AJAX Integration: JavaScript AJAX requests are used to handle interactions with the PHP API endpoints for a responsive experience.
SQLite Database: Stores ideas and to-do items.

File Structure
/project-root
│
├── index.html                  # Homepage with calendar displaying to-do items
├── ideas.html                  # Ideas page with a table of ideas and a form for CRUD operations
├── todo.html                   # To-do page with similar structure to Ideas page
│
├── api/                        # API Endpoint Files
│   ├── add.php                 # Adds a new item
│   ├── delete.php              # Deletes an item
│   ├── update.php              # Updates an item
│   └── get.php                 # Retrieves all items
│
├── router.php                  # Routes client requests to the appropriate API endpoint
├── db_connection.php           # Establishes SQLite database connection
│
├── assets/                     # Assets folder (for CSS, JS, etc.)
│   ├── js/
│   │   ├── app.js              # Main JavaScript file with AJAX requests
│   │   └── calendar.js         # JavaScript for rendering the calendar
│   └── css/
│       └── styles.css          # Main stylesheet
│
└── README.md                   # Project documentation


Installation
Clone the repository:
bash
git clone https://github.com/yourusername/brand-analytics-app.git
cd brand-analytics-app


Set up a local PHP server (or use XAMPP, WAMP, etc.).
Ensure SQLite is enabled on your PHP setup. This application uses an SQLite database to store ideas and to-do items.

Database Setup
Create an SQLite database: In your project root, create a new file named database.db.
Create required tables by running the following SQL commands:
CREATE TABLE IF NOT EXISTS ideas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    category TEXT,
    priority INTEGER,
    date_created TEXT DEFAULT CURRENT_TIMESTAMP,
    date_updated TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS todos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    due_date TEXT,
    completed INTEGER DEFAULT 0,
    date_created TEXT DEFAULT CURRENT_TIMESTAMP,
    date_updated TEXT DEFAULT CURRENT_TIMESTAMP
);


Database connection: Update db_connection.php to point to your database file if necessary.

Usage
Starting the Application: Launch your local server and navigate to the following URLs:
index.html for the calendar view of your to-do items.
ideas.html for managing ideas.
todo.html for managing to-do items.
Adding, Updating, Deleting Items: Each page provides a form and buttons for adding, updating, and deleting items.
Database Synchronization: JavaScript and AJAX are used to fetch items from the database on page load. Actions such as adding, updating, and deleting send AJAX requests to router.php, which routes requests to the corresponding API endpoints in /api.

API Endpoints
Each API endpoint is located in the /api folder. These endpoints handle the core CRUD operations and return responses in JSON format.
GET /router.php?action=getIdeas
Retrieves all items from the ideas table.
Response: JSON array of ideas.
POST /router.php?action=addIdea
Adds a new idea.
Request body: JSON with name, description, category, and priority.
Response: {"success": true} if successful.
POST /router.php?action=updateIdea&id={id}
Updates an existing idea.
Request body: JSON with name, description, category, and priority.
Response: {"success": true} if successful.
DELETE /router.php?action=deleteIdea&id={id}
Deletes an idea by id.
Response: {"success": true} if successful.

Contributing
Contributions are welcome! Feel free to fork the project and submit a pull request, or suggest new features by opening an issue.

License
This project is licensed under the TB License. See LICENSE for details.

Acknowledgments
This project’s structure and functionality were inspired by the Quire task management system, adapted for brand analytics with additional customization.

