$(document).ready(function(){
    // Load existing questions if any.

    // Add listeners to the buttons.
    $(document).on('click', '#ideas-page', function(){
        console.log("Worked")
        window.location.href = "ideas.html";
    });
    $(document).on('click', '#todo-page', function(){
        console.log("Worked 2")
        window.location.href = "todo.html";
    });
});