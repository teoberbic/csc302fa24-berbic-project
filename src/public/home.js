/*
File Name: home.js
Description: This file contains the code for the home page of the application.
*/
$(document).ready(function(){
    // Load existing questions if any.

    // Add listeners to the buttons.
    $(document).on('click', '#ideas-page', function(){
        window.location.href = "ideas.html";
    });
    $(document).on('click', '#todo-page', function(){
        window.location.href = "todo.html";
    });
});