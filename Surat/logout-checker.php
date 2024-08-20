<?php
$date = date('c'); // ISO 8601 format (e.g., 2023-03-16T14:30:00+07:00)
?>

<script>
  const sessTimeout = 600; // 10 minutes in seconds
  let sessOut = new Date(Date.now() + sessTimeout * 1000); // set timeout in milliseconds

  function checkSession() {
    const timeNow = new Date();
    if (timeNow > sessOut) {
      alert("Sesi Anda Telah Habis. Silahkan Login Kembali");
      window.location.href = "../index.php";
    }
  }

  // auto logout
  setInterval(checkSession, 100000); // hapus 0 nya 1 klo udh beres

  // detect user activity
  $(document).on('click keydown keyup keypress', function() {
    sessOut = new Date(Date.now() + sessTimeout * 1000); // reset timeout
  });
</script>