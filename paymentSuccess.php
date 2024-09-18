<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Payment Successful Page
    </title>
    <link rel="stylesheet" href="css/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="app.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="icon" type="image/x-icon" href="/image/logo.png">
</head>

<body>
    <div class="paymentSuccesContainer">
        <h1 class="success">Payment Successful</h1>
        <div class="tqMessage">
            <p>Yong ,Thank You For Your Purchase</p>
            <p>You will be redirected to My Orders Page in 5 seconds</p>
        </div>


        <div class="countdown">
            <div class="loader"></div>
            <span class="timer">5</span>
        </div>
    </div>

    <script>
        var timerElement = document.querySelector(".timer");
        var timeLeft = 5; // Set countdown time

        var countdownInterval = setInterval(function () {
            timeLeft--;
            timerElement.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                window.location.href = "myOrder.php";
            }
        }, 1000); // Update every second
    </script>
</body>

</html>