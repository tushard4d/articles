<?php
session_start();
session_unset();
session_destroy();

// Clear client-side login flag
echo "<script>
  localStorage.removeItem('isAdminLoggedIn');
  localStorage.removeItem('adminName');
  window.location.href = 'login.php';
</script>";
exit;
?>
