document.getElementById("logout-link").addEventListener("click", function(event) {
  event.preventDefault(); // Prevent the default link behavior

  // Display a confirmation dialog
  if (confirm("Are you sure you want to log out?")) {
    // If the user confirms, redirect to the logout page
    window.location.href = "logout.php";
  }
});