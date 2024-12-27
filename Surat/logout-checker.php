<?php
$date = date('c');
?>

<script>
  const sessTimeout = 600;
  let sessOut = new Date(Date.now() + sessTimeout * 1000); 

  function checkSession() {
    const timeNow = new Date();
    if (timeNow > sessOut) {
      alert("Sesi Anda Telah Habis. Silahkan Login Kembali");
      window.location.href = "../index.php";
    }
  }

  // auto logout
  setInterval(checkSession, 10000); 

  // detect user activity
  $(document).on('click keydown keyup keypress', function() {
    sessOut = new Date(Date.now() + sessTimeout * 1000); 
  });
</script>