# ENDR Analytics Web App

This web application is designed to track and manage ideas and to-do items for your brand. It features an **Ideas Page** and a **To-Do List Page**, each of which allows users to add, update, or delete items. The app also displays to-do items on a calendar on the home page. 

The application is built with **PHP**, **JavaScript (AJAX)**, **HTML/CSS**, and uses **SQLite** as the database.

## Link for Digdug Server
```bash
https://digdug.cs.endicott.edu/~tberbic/csc302fa24-berbic-project/src/public/
```

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
CREATE TABLE IF NOT EXISTS IdeasTable (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    category TEXT,
    action_priority INTEGER,
    date_created TEXT DEFAULT CURRENT_TIMESTAMP,
    date_updated TEXT DEFAULT CURRENT_TIMESTAMP
);
```
### `to-dos` Table
```sql
CREATE TABLE IF NOT EXISTS ToDoTable (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    completed INTEGER DEFAULT 0,
    parent_id integer,
    foreign key (parent_id) references ToDoTable(id) on delete cascade,
    date_created TEXT DEFAULT CURRENT_TIMESTAMP,
    date_updated TEXT DEFAULT CURRENT_TIMESTAMP
);


Database connection: Update db_connection.php to point to your database file if necessary.

```

# Usage

## Starting the Application
1. Launch your local server.
2. Navigate to the following URLs for specific views:
   - **`index.html`**: Calendar view of your to-do items.
   - **`ideas.html`**: Manage your ideas.
   - **`todo.html`**: Manage your to-do items.

## Adding, Updating, and Deleting Items
- Each page includes forms and buttons to add, update, and delete items.  
- The interface allows for seamless management of ideas and to-do items.

## Database Synchronization
- JavaScript and AJAX handle data fetching and synchronization:
  - On page load, items are fetched from the database.
  - Actions such as adding, updating, and deleting send AJAX requests to `router.php`.
  - These requests are routed to the appropriate API endpoints in the `/api` folder.


# API Endpoints

Each API endpoint is located in the `/api` folder. These endpoints handle the core CRUD operations and return responses in JSON format.

## Idea Endpoints

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

## Todo Endpoints

### 1. **Retrieve Todos**
**GET** `/router.php?action=get`  
Retrieves all items from the `TodoTable`.  

**Response**:  
- JSON array of ideas.  
```json
[
    {
        "id": 1,
        "title": "To Do Name",
        "parent_id": "Parent of the To Do (if any)",
        "completed": "false",
        "date_created": "2024-11-18 12:00:00",
        "date_updated": "2024-11-18 12:00:00"
    }
```
---

### 2. **Add a New Todo**
**POST** `/router.php?action=add`  
Adds a new todo.  

**Request Body** (JSON):  
```json
{
    "title": "New To Do Name",
    "parent_id": "Parent of the To Do (if any)",
}
```
**Response**:  
- A success message.  
```json
{
    "success": true
}
```
### 3. **Update an Existing Todo**
**POST** `/router.php?action=update&id={id}`  
Updates an existing todo.
**Request Body** (JSON):  
```json
{
    "title": "Updated Todo Name",
    "Completed": "Updated status",
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
**POST** `/router.php?action=delete&id={id}`  
Updates an existing idea. 
**Request Body**:  
```json
{
    "id": "todo id"
}
```

**Response**:  
- A success message.  
```json
{
    "success": true
}
```



## API Documentation Ideas
| **Action**        | **HTTP Method** | **Endpoint**                            | **Request Parameters**                                                   | **Response Data (Success)**                                                                                   | **Response Data (Error)**                                                      |
|-------------------|-----------------|----------------------------------------|--------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------|
| **Add Idea**      | `POST`          | `router.php?action=add&category=ideas`          | `name` (string, required), `description` (string, required), `category` (string, required), `priority` (string, required) | `{ "success": true, "id": 123 }`                                                                               | `{ "success": false, "error": "Error message" }`                               |
| **Get Ideas**     | `GET`           | `router.php?action=get&category=ideas`          |       | `[ { "id": 1, "name": "Idea 1", "description": "Description", "category": "Category 1", "priority": "High", "created_at": "2024-11-01", "updated_at": "2024-11-02" } ]` | `{ "success": false, "error": "Error message" }`                               |
| **Update Idea**   | `PUT`           | `router.php?action=update&category=ideas`       | `id` (integer, required), `name` (string, optional), `description` (string, optional), `category` (string, optional), `priority` (string, optional) | `{ "success": true, "id": 1 }`                                                                                  | `{ "success": false, "error": "Error message" }`                               |
| **Delete Idea**   | `DELETE`        | `router.php?action=delete&category=ideas`       | `id` (integer, required)                                                 | `{ "success": true, "message": "Idea deleted successfully" }`                                                    | `{ "success": false, "error": "Error message" }`                               |

## API Documentation Todo
| **Action**        | **HTTP Method** | **Endpoint**                            | **Request Parameters**                                                   | **Response Data (Success)**                                                                                   | **Response Data (Error)**                                                      |
|-------------------|-----------------|----------------------------------------|--------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------|
| **Add Todo**      | `POST`          | `router.php?action=add&category=todo`          | `title` (string, required), `parent_id` (integer, optional) | `{ "success": true, "id": 123 }`                                                                               | `{ "success": false, "error": "Error message" }`                               |
| **Get Todos**     | `GET`           | `router.php?action=get&category=todo`          |       | `[ { "id": 1, "title": "Idea 1", "completed": "false", "parent_id": "null", "created_at": "2024-11-01", "updated_at": "2024-11-02" } ]` | `{ "success": false, "error": "Error message" }`                               |
| **Update Todo**   | `PUT`           | `router.php?action=update&category=todo`       | `title` (string, required), `parent_id` (integer, optional), "completed": "false" | `{ "success": true, "id": 1 }`                                                                                  | `{ "success": false, "error": "Error message" }`                               |
| **Delete Todo**   | `DELETE`        | `router.php?action=delete&category=todo`       | `id` (integer, required)                                                 | `{ "success": true, "message": "Idea deleted successfully" }`                                                    | `{ "success": false, "error": "Error message" }`                               |


## Data Model Ideas

### Client-Side Data
- On the client side, data is primarily stored in the browser.
- It is used to manage form submissions, display data in tables, and handle user interactions.

### Idea Form Data
When a user submits a new idea or updates an existing one, the following data is collected:

- **Name** *(string)*: The name or title of the idea.
- **Description** *(string)*: A description of the idea.
- **Category** *(string)*: The category the idea belongs to (e.g., 'Marketing', 'Development').
- **Priority** *(string)*: The action priority level of the idea (e.g., 'Low', 'Medium', 'High').

## Data Model Todo

### Client-Side Data
- On the client side, data is primarily stored in the browser
- It is used to manage form submissions, display data in lists, and handle user interactions.

### Todo Form Data
When a user submits a new to-do item or updates an existing one, the following data is collected:

- **Title** *(string)*: The title of the to-do item.
- **Completed** *(boolean)*: Indicates whether the to-do item has been completed.
- **Parent ID** *(integer, optional)*: The ID of the parent to-do item for hierarchical structuring.

#### Data Relationships
**Hierarchical Structure:** To-do items can have a parent-child relationship, enabling nested tasks. The Parent ID field establishes this relationship.
**Status Tracking:** The Completed field allows tracking whether a task is done.

## Testing
- **UI Testing:** Verified the functionality and appearance of the user interface by manually interacting with it.
Tested workflows like adding, editing, and deleting items in the ideas list.
Checked that buttons and item structures worked as expected.
Ensured error handling for invalid inputs and actions was displayed correctly.
- **Data Flow Testing:** Tested the flow of data between the frontend and backend APIs.
Verified that data entered through the UI was correctly sent to the backend (e.g., adding or updating items).
Ensured that responses from the backend were sent back in the console.
Checked for consistency when data was updated or deleted at different stages in the application.
- **API Testing w/ POSTMAN (test/    - folder)**
All API endpoints have been thoroughly tested using Postman. The tests cover the following endpoints:

  **GET** /router.php?action=get&category=todo: Retrieves all to-do items.
  
  **POST** /router.php?action=add&category=todo: Adds a new to-do item.
    
  **PUT** /router.php?action=update&category=todo: Updates an existing to-do item.
  
  **DELETE** /router.php?action=delete&category=todo: Deletes a to-do item.

  **GET** /router.php?action=get&category=ideas: Retrieves all idea items.
  
  **POST** /router.php?action=add&category=ideas: Adds a new idea item.
    
  **PUT** /router.php?action=update&category=ideas: Updates an existing idea item.
  
  **DELETE** /router.php?action=delete&category=ideas: Deletes a idea item.


  
The Postman collection containing all the API tests is included in the test/ folder as ToDoAPI.postman_collection.json & ideasAPI.postman_collection.json. These collections can be imported into Postman to run the tests.

## Acknowledgments
This project’s structure and functionality were inspired by the Quire task management system, adapted for brand analytics with additional customization by using many outside sources for resource help.

## Ideas Feature 95%

- [x] Create a form to input idea data
- [x] Send the form data to the server using AJAX
- [x] Add server-side logic to handle adding ideas to the database
- [x] Show new ideas in the table once added
- [x] Test add feature
- [x] Test select feature
- [x] Test update feature
- [x] Test del feature
- [ ] Style page better
- [ ] Add buttons to different pages

## To-Do List Feature 50%

- [x] Implement idea creation form
- [x] Have all template blueprint code setup from front to backend (full flow)
- [ ] Integrate idea CRUD functionality
   - [ ] Add feature works
   - [x] Fetch Feature works
   - [ ] Delete Feature works
   - [ ] Update Feature works
- [ ] Style the front-end better (CSS)
- [ ] Test the full flow from front-end to back-end
- [x] Set up database migrations and schema

## Calendar Feature TBD (if I am working on this or not)

- [ ] Implement idea creation form
- [ ] Integrate idea CRUD functionality (create, read, update, delete)
- [ ] Style the front-end (CSS/Bootstrap)
- [ ] Test the full flow from front-end to back-end
- [ ] Set up database migrations and schema
