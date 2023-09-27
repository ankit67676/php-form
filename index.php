<?php
session_start();
session_unset();
session_destroy();
$serviceNumber = "";
$resultMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serviceNumber = $_POST['service-number'];
    $modalName = $_POST['modal-name'];

    if (!empty($serviceNumber)) {
        if (!preg_match('/^[A-Za-z]{3}\d{10}$/', $serviceNumber)) {
            $resultMessage = "The service number is invalid , Please email abc@gmail.com for warranty registration.";
        } else {
            session_start();
            $_SESSION['service-number'] = $serviceNumber;
            $_SESSION['modal-name'] = $modalName;

            // Redirect to the next step
            header("Location: step2.php");
            exit;
        }
    } else {
        $resultMessage = "The service number is invalid , Please email abc@gmail.com for warranty registration.";
    }
} else {
    $resultMessage = "";
    session_unset();
    session_destroy();
};
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <h1>Warranty Registration Form</h1>
        <form action="index.php" method="post">
            <div class="field-box">
                <label for="service">Installation Service Order No : </label>
                <input class="input-field" type="text" name="service-number" id="service" />
            </div>
            <div class="field-box">
                <label for="modal">Modal Name : </label>
                <select class="input-field" id="modal" name="modal-name">
                    <option value="LTW" selected>LTW</option>
                    <option value="Aero">AERO</option>
                </select>
            </div>
            <input type="submit" class="button" value="Next" />
        </form>
    </div>
    <div class="modal-container">
        <div class="overlay"></div>
        <div class="result-message">
            <img src="error.png" width="100" />
            <?php echo $resultMessage; ?>
        </div>
    </div>
    <?php

    if ($resultMessage) {
        echo '<script>
        document.querySelector(".modal-container").style.display="block";
        document.querySelector(".modal-container").addEventListener("click", function(){
            document.querySelector(".modal-container").style.display="none";
        });
    </script>';
    }

    ?>
</body>


</html>