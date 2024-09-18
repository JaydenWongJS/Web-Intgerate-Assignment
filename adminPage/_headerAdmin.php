
<!DOCTYPE html>
<html>

<head>
    <title><?= $title ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/../css/adminApp.css">
  <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
  <link rel="icon" type="image/x-icon" href="/../image/logo.png">
</head>

<body>
    <!--Flash Message-->
    <!--Ron-->
    <div id="info"><?= temp('info') ?></div>