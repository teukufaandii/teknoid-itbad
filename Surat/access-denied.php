<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - Teknoid</title>
    <link rel="icon" href="../logo itbad.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .container {
            height: 100vh;
        }
        .display-flex {
            display: flex;
        }
        .flex-column {
            flex-direction: column;
        }
        .align-items-center {
            align-items: center;
        }
        .justify-content-center {
            justify-content: center;
        }
        .text-center {
            text-align: center;
        }
        h1 {
            font-size: 48px;
            margin-bottom: 10px;
        }
        img{
            width: 400px;
            height: 400px;
        }
    </style>
</head>
<body>
    <div class="container display-flex flex-column align-items-center justify-content-center text-center">
        <img src="../locked.png" alt="" srcset="">
        <h1>Sepertinya anda tidak memiliki akses ke halaman ini</h1>
        <h3 href="index.php">Silahkan kembali ke halaman utama</h3>
        <button class="btn btn-primary mt-3" onclick="history.back()">Kembali</button>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>