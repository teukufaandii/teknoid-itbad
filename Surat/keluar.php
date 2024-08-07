<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out</title>
    <link rel="icon" type="image/x-icon" href="../logo itbad.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .modal.fade .modal-dialog {
            transform: translate(0, 50px);
            transition: transform 0.3s ease-out;
        }
        .modal.show .modal-dialog {
            transform: translate(0, 0);
        }
    </style>
</head>
<body>
    <!-- Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Berhasil Logout</h5>
                </div>
                <div class="modal-body">
                    Dalam <span id="countdown">2</span> detik, Anda akan diarahkan ke halaman login.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        var modalEl = document.getElementById('logoutModal');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();

        var countdownEl = document.getElementById('countdown');
        var countdown = 2;
        
        var countdownInterval = setInterval(function() {
            countdown--;
            countdownEl.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.location.href = "../"; 
            }
        }, 1000);
    </script>
</body>
</html>
