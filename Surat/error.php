<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error Page</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arvo">
  <style>
    body {
      background-color: #f4f5f9;
      font-family: 'Arvo', serif;
    }

    .page_404 {
      padding: 40px 0;
      background-color: #f4f5f9;
      text-align: center;
    }

    .four_zero_four_bg {
      background-image: url('https://cdn.dribbble.com/users/1010064/screenshots/7047881/media/694b3a6499bfb8f7b8786c832ce4434e.gif');
      background-size: cover;
      background-position: center;
      height: 600px;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #fff;
      position: relative;
    }

    .four_zero_four_bg h1, .four_zero_four_bg h3 {
      margin: 0;
      font-size: 80px;
      z-index: 1;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
    }

    .contant_box_404 {
      position: relative;
      z-index: 2;
      margin-top: -50px;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    }

    .contant_box_404 h3 {
      font-size: 24px;
      color: #333;
    }

    .contant_box_404 p {
      color: #666;
    }

    .link_404 {
      color: #fff!important;
      padding: 10px 20px;
      background: #39ac31;
      margin: 20px 0;
      display: inline-block;
      text-decoration: none;
      border-radius: 5px;
    }

    .link_404:hover {
      background: #2e8b57;
    }
  </style>
  <script> 
    var countdown = 5;
    function updateCountdown() {
      var countdownElement = document.getElementById('countdown');
      if (countdown > 0) {
        countdownElement.textContent = countdown;
        countdown--;
        setTimeout(updateCountdown, 1000);
      } else {
        window.location.href = 'tambah_surat2';
      }
    }

    setTimeout(updateCountdown, 1000);
  </script>
</head>

<body>
  <section class="page_404">
    <div class="container">
      <div class="row"> 
        <div class="col-sm-12">
          <div class="col-sm-10 col-sm-offset-1 text-center">
            <div class="four_zero_four_bg">
            </div>
            <div class="contant_box_404">
              <h3>  
                <?php
                if (isset($_GET['error'])) {
                  $error = $_GET['error'];
                  if ($error == 'filesize') {
                    echo 'File terlalu besar, unggah file maksimal 10 MB.';
                  }elseif($error == 'filetype') {
                    echo 'File yang diunggah bukan PDF. Harap unggah file dalam format PDF.';
                  }
                } else {
                  echo "File terlalu besar, unggah file maksimal 10 MB.";
                }
                ?>
              </h3>
              <p>Anda akan dialihkan ke halaman tambah surat dalam <span id="countdown">6</span> detik</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>
</html>
