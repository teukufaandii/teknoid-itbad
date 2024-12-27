<?php
session_start();
include __DIR__ . '/Maintenance/Middleware/index.php';

// Retrieve the message and redirect URL from the query parameters
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Login Successful!';
$redirectUrl = isset($_GET['redirectUrl']) ? htmlspecialchars($_GET['redirectUrl']) : 'Surat/dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Success</title>
    <link rel="icon" type="image/x-icon" href="../logo itbad.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* Add custom animation if needed */
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
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login Success</h5>
                </div>
                <div class="modal-body">
                    <?php echo $message; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        var modalEl = document.getElementById('loginModal');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();

        setTimeout(function() {
            window.location.href = "<?php echo $redirectUrl; ?>";
        }, 2000);
    </script>
</body>
</html>
