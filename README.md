# ENDR Analytics Web App

This web application is designed to track and manage ideas and to-do items for your brand. It features an **Ideas Page** and a **To-Do List Page**, each of which allows users to add, update, or delete items. The app also displays to-do items on a calendar on the home page. 

The application is built with **PHP**, **JavaScript (AJAX)**, **HTML/CSS**, and uses **SQLite** as the database.

## Table of Contents
1. [Features](#features)
2. [File Structure](#file-structure)
3. [Installation](#installation)
4. [Database Setup](#database-setup)
5. [Usage](#usage)
6. [API Endpoints](#api-endpoints)

## Features

- **Ideas Page**: View, add, update, and delete ideas with fields for name, description, category, priority, creation date, and last updated date.
- **To-Do List Page**: Manage to-do items in a similar fashion, with integration to display items on a calendar on the home page.
- **AJAX Integration**: JavaScript AJAX requests are used to handle interactions with the PHP API endpoints for a responsive experience.
- **SQLite Database**: Stores ideas and to-do items.

## File Structure



```
/src
├── apis/
│   ├── ideas-api/
│   │   ├── add.php         # Adds a new idea
│   │   ├── delete.php      # Deletes an idea
│   │   ├── get.php         # Retrieves all ideas
│   │   └── update.php      # Updates an existing idea
│   └── todo-api/
│       ├── add.php         # Adds a new to-do item
│       ├── delete.php      # Deletes a to-do item
│       ├── get.php         # Retrieves all to-do items
│       └── update.php      # Updates an existing to-do item
├── db/
│   └── db.php              # Database connection file
├── public/
│   ├── home.js             # JavaScript for homepage functionality
│   ├── ideas.html          # Ideas page with a table of ideas
│   ├── index.html          # Homepage with calendar displaying to-do items
│   └── todo.html           # To-do page with a similar structure to Ideas page
└── router.php              # Routes client requests to the appropriate API endpoint
```



## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/brand-analytics-app.git
   cd brand-analytics-app


2. **Set up a local PHP server (or use XAMPP, WAMP, etc.)**:
3. **Ensure SQLite is enabled on your PHP setup. This application uses an SQLite database to store ideas and to-do items**.

# Database Setup

## Create an SQLite Database
1. In your project root, create a new file named `database.db`.

## Create Required Tables
Run the following SQL commands to set up the necessary tables:

### `ideas` Table
```sql
CREATE TABLE IF NOT EXISTS ideas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    category TEXT,
    priority INTEGER,
    date_created TEXT DEFAULT CURRENT_TIMESTAMP,
    date_updated TEXT DEFAULT CURRENT_TIMESTAMP
);
```
### `to-dos` Table
```sql
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

```

## Usage
Starting the Application: Launch your local server and navigate to the following URLs:
index.html for the calendar view of your to-do items.
ideas.html for managing ideas.
todo.html for managing to-do items.
Adding, Updating, Deleting Items: Each page provides a form and buttons for adding, updating, and deleting items.
Database Synchronization: JavaScript and AJAX are used to fetch items from the database on page load. Actions such as adding, updating, and deleting send AJAX requests to router.php, which routes requests to the corresponding API endpoints in /api.

# API Endpoints

Each API endpoint is located in the `/api` folder. These endpoints handle the core CRUD operations and return responses in JSON format.

## Endpoints

### 1. **Retrieve Ideas**
**GET** `/router.php?action=getIdeas`  
Retrieves all items from the `ideas` table.  

**Response**:  
- JSON array of ideas.  
```json
[
    {
        "id": 1,
        "name": "Idea Name",
        "description": "Description of the idea",
        "category": "Category",
        "priority": 1,
        "date_created": "2024-11-18 12:00:00",
        "date_updated": "2024-11-18 12:00:00"
    }
```
---

### 2. **Add a New Idea**
**POST** `/router.php?action=addIdea`  
Adds a new idea.  

**Request Body** (JSON):  
```json
{
    "name": "New Idea Name",
    "description": "Description of the new idea",
    "category": "Category",
    "priority": 1
}
```
**Response**:  
- A success message.  
```json
{
    "success": true
}
```
### 3. **Update an Existing Idea**
**POST** `/router.php?action=updateIdea&id={id}`  
Updates an existing idea.
**Request Body** (JSON):  
```json
{
    "name": "Updated Idea Name",
    "description": "Updated description",
    "category": "Updated category",
    "priority": 2
}
```

**Response**:  
- A success message.  
```json
{
    "success": true
}
```
### 4. **Delete an Existing Idea**
**POST** `/router.php?action=updateIdea&id={id}`  
Updates an existing idea. 
**Request Body**:  
_No request body required._  

**Response**:  
- A success message.  
```json
{
    "success": true
}
```



## API Documentation
| **Action**        | **HTTP Method** | **Endpoint**                            | **Request Parameters**                                                   | **Response Data (Success)**                                                                                   | **Response Data (Error)**                                                      |
|-------------------|-----------------|----------------------------------------|--------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------|
| **Add Idea**      | `POST`          | `/src/apis/ideas-api/add.php`          | `name` (string, required), `description` (string, required), `category` (string, required), `priority` (string, required) | `{ "success": true, "id": 123 }`                                                                               | `{ "success": false, "error": "Error message" }`                               |
| **Get Ideas**     | `GET`           | `/src/apis/ideas-api/get.php`          | None (Optionally, filtering parameters like `category`, `priority`)      | `[ { "id": 1, "name": "Idea 1", "description": "Description", "category": "Category 1", "priority": "High", "created_at": "2024-11-01", "updated_at": "2024-11-02" } ]` | `{ "success": false, "error": "Error message" }`                               |
| **Update Idea**   | `PUT`           | `/src/apis/ideas-api/update.php`       | `id` (integer, required), `name` (string, optional), `description` (string, optional), `category` (string, optional), `priority` (string, optional) | `{ "success": true, "id": 1 }`                                                                                  | `{ "success": false, "error": "Error message" }`                               |
| **Delete Idea**   | `DELETE`        | `/src/apis/ideas-api/delete.php`       | `id` (integer, required)                                                 | `{ "success": true, "message": "Idea deleted successfully" }`                                                    | `{ "success": false, "error": "Error message" }`                               |

## Data Model
Client-Side Data
On the client side, data is primarily stored in the browser, where it is used to manage form submissions, display data in tables, and handle user interactions.

Idea Form Data
When a user submits a new idea or updates an existing one, the following data is collected:

Name (string): The name or title of the idea.
Description (string): A description of the idea.
Category (string): The category the idea belongs to (e.g., 'Marketing', 'Development').
Priority (string): The action priority level of the idea (e.g., 'Low', 'Medium', 'High').

## Contributing
Contributions are welcome! Feel free to fork the project and submit a pull request, or suggest new features by opening an issue.

## License
This project is licensed under the TB License. See LICENSE for details.

## Acknowledgments
This project’s structure and functionality were inspired by the Quire task management system, adapted for brand analytics with additional customization.

## Ideas Feature 80%

- [ ] Create a form to input idea data
- [ ] Send the form data to the server using AJAX
- [ ] Add server-side logic to handle adding ideas to the database
- [ ] Show new ideas in the table once added
- [ ] Test the feature

## To-Do List Feature 20%

- [ ] Implement idea creation form
- [ ] Integrate idea CRUD functionality (create, read, update, delete)
- [ ] Style the front-end (CSS/Bootstrap)
- [ ] Test the full flow from front-end to back-end
- [ ] Set up database migrations and schema

## Calendar Feature 10%

- [ ] Implement idea creation form
- [ ] Integrate idea CRUD functionality (create, read, update, delete)
- [ ] Style the front-end (CSS/Bootstrap)
- [ ] Test the full flow from front-end to back-end
- [ ] Set up database migrations and schema
