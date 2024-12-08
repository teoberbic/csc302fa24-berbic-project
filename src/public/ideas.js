/*
File Name: ideas.js
Description: Contains the JavaScript functionality for the project.
Sources: 
    - https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js (for DOM manipulation)
    - Quizzer code (for the structure of the code)
    - Tododles code (for the structure of CRUD operations)
    - https://chatgpt.com (for the structure of the selectIdea function)
    - VSCode Copilot (for comments describing the code)
*/
$(document).ready(function(){

  fetchIdeas();

  $(document).on('click', '#add-idea', addIdea);
  $(document).on('click', '#remove-idea', removeIdea);
  $(document).on('click', '#update-idea', updateIdea);
   // Add listeners to the buttons.
   $(document).on('click', '#home-page', function(){
    window.location.href = "index.html";
    });
    $(document).on('click', '#todo-page', function(){
        window.location.href = "todo.html";
    });
});


let selectedIdeaId = null;



/**
 * Fetches and displays all ideas from the database.
 *
 * @return JSON response containing the list of ideas or an error message.
 */
function fetchIdeas() {
  $.ajax({
    url: '../router.php?action=get&category=ideas',
    method: 'GET',
    success: function(response) {
      console.log(`Status: 200`); // Log the status code
      console.log(response); // Log the response data

      // Check if the response is structured correctly
      if (response.success && Array.isArray(response.ideas)) {
        renderTable(response.ideas);
      } else {
        console.error("Invalid response structure:", response);
        renderTable([]); // Optionally pass an empty array if the structure is incorrect
      }
    },
    error: function(xhr, status, error) {
      console.error('Error fetching ideas:', error);
      alert('An error occurred while fetching ideas. Please try again.');
    }
  });
}

/**
 * Renders ideas into the table by populating rows with the given data.
 *
 * @param ideas An array of ideas to be displayed in the table.
 *
 * @return void.
 */
function renderTable(ideas) {
  const tableBody = document.getElementById("ideas-table").querySelector("tbody");
  tableBody.innerHTML = ""; // Clear existing rows

  // Check if the ideas array is empty
  if (ideas.length === 0) {
      const row = document.createElement("tr");
      row.innerHTML = `<td colspan="7">No ideas available.</td>`; // Display an info message
      tableBody.appendChild(row);
      return; // Exit the function since there are no ideas to display
  }

  ideas.forEach(idea => {
      const row = document.createElement("tr");
      row.innerHTML = `
          <td>${idea.name}</td>
          <td>${idea.description}</td>
          <td>${idea.category}</td>
          <td>${idea.action_priority}</td>
          <td>${idea.createdAt}</td>
          <td>${idea.updatedAt}</td>
          <td><button onclick="selectIdea(${idea.id})">Select</button></td>
      `;
      tableBody.appendChild(row);
  });
}


/**
 * Adds a new idea to the database.
 *
 * @return void. Refreshes the ideas table upon successful addition.
 */
function addIdea() {
  const name = document.getElementById("name").value;
  const description = document.getElementById("description").value;
  const category = document.getElementById("category").value;
  const priority = document.getElementById("action_priority").value;

  $.ajax({
    url: '../router.php?action=add&category=ideas',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({ name, description, category, priority }),
    success: function() {
        fetchIdeas(); // Refresh the ideas table
        // TODO add alert if same name prsent
    },
    error: function(xhr, status, error) {
      console.error('Error adding idea:', error);
      alert('An error occurred while adding the idea. Please try again.');
    }
  });
}

/**
 * Selects an idea by its ID and populates the form fields for updating.
 *
 * @param id The ID of the idea to be selected and loaded into the form.
 *
 * @return void. Populates the form fields with the selected idea's data or logs an error if not found.
 */
function selectIdea(id) {
  selectedIdeaId = id;
  
  // Find the idea row in the table
  const idea = Array.from(document.getElementById("ideas-table").querySelectorAll("tr"))
                      .find(row => {
                        const button = row.querySelector("button");
                        return button && button.getAttribute("onclick") === `selectIdea(${id})`;
                      });

  // Check if the idea row was found
  if (!idea) {
    console.error(`Idea with ID ${id} not found.`);
    return;
  }

  // Populate the form fields with the selected idea's data
  document.getElementById("name").value = idea.cells[0].textContent;
  document.getElementById("description").value = idea.cells[1].textContent;
  document.getElementById("category").value = idea.cells[2].textContent;
  document.getElementById("action_priority").value = idea.cells[3].textContent;
}

/**
 * Updates an existing idea in the database.
 *
 * @return void. Refreshes the ideas table and clears the selected idea ID upon successful update.
 */
function updateIdea() {
  if (!selectedIdeaId) {
    console.log("Please select an idea to update.");
    return;
  }

  const name = document.getElementById("name").value;
  const description = document.getElementById("description").value;
  const category = document.getElementById("category").value;
  const priority = document.getElementById("action_priority").value;

  $.ajax({
    url: `../router.php?action=update&category=ideas&id=${selectedIdeaId}`,
    method: 'PUT',
    contentType: 'application/json',
    data: JSON.stringify({ name, description, category, priority }),
    success: function() {
      fetchIdeas(); // Refresh table
      selectedIdeaId = null; // Clear selected ID
    },
    error: function(xhr, status, error) {
      console.error('Error updating idea:', error);
      alert('An error occurred while updating the idea. Please try again.');
    }
  });
}


/**
 * Deletes the selected idea from the database.
 *
 * @return void. Refreshes the ideas table and clears the selected idea ID upon successful deletion.
 */
function removeIdea() {
  if (!selectedIdeaId) {
    alert("Please select an idea to remove.");
    return;
  }

  $.ajax({
    url: `../router.php?action=delete&category=ideas&id=${selectedIdeaId}`,
    method: 'DELETE',
    success: function() {
      fetchIdeas(); // Refresh table
      selectedIdeaId = null; // Clear selected ID
    },
    error: function(xhr, status, error) {
      console.error('Error deleting idea:', error);
      alert('An error occurred while deleting the idea. Please try again.');
    }
  });
}
