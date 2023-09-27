<?php
$name = "";
$email = "";
$phone = "";
$pincode = "";
$address = "";
$city = "";
$state = "";
$errorMessage = "";
session_start();

if (empty($_SESSION['service-number'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pincode = $_POST['pincode'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];


    if (!empty($name) && !empty($email) && !empty($phone) && !empty($pincode) && !empty($address) && !empty($city) && !empty($state)) {
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['pincode'] = $pincode;
        $_SESSION['city'] = $city;
        $_SESSION['state'] = $state;
        $_SESSION['address'] = $address;
        $_SESSION['phone'] = $phone;

        // Redirect to the next step
        header("Location: step3.php");
        exit;
    } else {
        $errorMessage = "All fields are required";
    }
} else {
    // session_unset();
    // session_destroy();
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
        <form action="step2.php" method="post">
            <div class="flex-div">
                <div class="field-box">
                    <label for="name">Name : </label>
                    <input class="input-field" type="text" name="name" id="name" />
                </div>
                <div class="field-box">
                    <label for="email">Email : </label>
                    <input class="input-field" type="email" name="email" id="email" />
                </div>
            </div>
            <div class="flex-div">
                <div class="field-box">
                    <label for="phone">Phone : </label>
                    <input class="input-field" type="number" name="phone" id="phone" />
                </div>
                <div class="field-box">
                    <label for="pin">Pin Code : </label>
                    <input class="input-field" type="number" name="pincode" id="pin" />
                </div>
            </div>
            <div class="field-box column-box">
                <label for="address">Address : </label>
                <textarea rows="5" class="text-field" name="address" id="address"></textarea>
            </div>
            <div class="flex-div">
                <div class="field-box">
                    <label for="city">City : </label>
                    <input class="input-field" type="text" name="city" id="city" />
                </div>
                <div class="field-box">
                    <label for="state">State : </label>
                    <input class="input-field" type="text" name="state" id="state" />
                </div>
            </div>
            <input type="submit" class="button" value="Next" />
        </form>
    </div>
    <div class="modal-container">
        <div class="overlay"></div>
        <div class="result-message">
            <img src="error.png" width="100" />
            <?php echo $errorMessage; ?>
        </div>
    </div>
    <?php

    if ($errorMessage) {
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