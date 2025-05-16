document.getElementById('inventoryToggle').addEventListener('click', function() {
  var submenu = document.getElementById('inventorySubmenu');
  var icon = document.querySelector('#inventoryToggle i');

  // Check if the submenu is currently visible
  var isSubMenuVisible = submenu.style.display === 'block';

  if (!isSubMenuVisible) {
    submenu.style.display = 'block';
    icon.classList.add('fa-rotate-90'); // Add the rotation class
  } else {
    submenu.style.display = 'none';
    icon.classList.remove('fa-rotate-90'); // Remove the rotation class
  }
});
