<?php
include_once "vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$serialNumber = "";
$puchaseDate = "";
$invoice = "";
$warranty = "";
$errorMessage = "";
$successMessage="";
// $loading=false;


session_start();

if (empty($_SESSION['service-number'])) {
    header("Location: index.php");
    exit;
}

$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$database = $_ENV['DB_NAME'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$database", $username, $password);

    if (
        $_SERVER["REQUEST_METHOD"] == "POST"
    ) {
        $invoiceFile = $_FILES["invoice"];
        $warrantyFile = $_FILES["warranty"];

        $serialNumber = $_POST['serial-number'];
        $purchaseDate = $_POST['date'];
        $invoice = $_POST['invoice'];
        $warranty = $_POST['warranty'];

        $invoiceFileType = pathinfo($invoiceFile["name"], PATHINFO_EXTENSION);
        $warrantyFileType = pathinfo($warrantyFile["name"], PATHINFO_EXTENSION);

        if (!empty($serialNumber) && !empty($purchaseDate) && $invoiceFileType == "pdf" && $warrantyFileType == "pdf") {
            // $loading=true;
            $serviceNumber = $_SESSION['service-number'];
            $email = $_SESSION['email'];
            $phone = $_SESSION['phone'];
            $name = $_SESSION['name'];
            $address = $_SESSION['address'];
            $city = $_SESSION['city'];
            $pincode = $_SESSION['pincode'];
            $state = $_SESSION['state'];
            $modalName = $_SESSION['modal-name'];
            $invoiceFile = $_FILES["invoice"];
            $warrantyFile = $_FILES["warranty"];


            $createTableSQL = "CREATE TABLE IF NOT EXISTS formData (
                id SERIAL PRIMARY KEY,
                service_number VARCHAR(50),
                email VARCHAR(255),
                phone VARCHAR(20),
                name VARCHAR(50),
                address VARCHAR(255),
                city VARCHAR(50),
                pincode VARCHAR(10),
                state VARCHAR(50),
                modal_name VARCHAR(255),
                serial_number VARCHAR(50),
                purchase_date VARCHAR(50),
                invoice VARCHAR(50),
                warranty VARCHAR(50)
            )";

            if (!$pdo->exec($createTableSQL)) {
                $errorInfo = $pdo->errorInfo();
                if ($errorInfo[0] !== '00000') {
                    echo "Table creation failed: " . $errorInfo[2];
                }
            }

            
            $insertSQL = "INSERT INTO formData (
                service_number, email, phone, name, address, city, pincode, state, modal_name,
                serial_number, purchase_date, invoice, warranty
            ) VALUES (
                :serviceNumber, :email, :phone, :name, :address, :city, :pincode, :state, :modalName,
                :serialNumber, :purchaseDate, :invoiceFile, :warrantyFile
            )";

            $stmt = $pdo->prepare($insertSQL);

            // binding parameters
            $stmt->bindParam(':serviceNumber', $serviceNumber);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':pincode', $pincode);
            $stmt->bindParam(':state', $state);
            $stmt->bindParam(':modalName', $modalName);
            $stmt->bindParam(':serialNumber', $serialNumber);
            $stmt->bindParam(':purchaseDate', $purchaseDate);
            $stmt->bindParam(':invoiceFile',$_FILES["invoice"]["name"]);
            $stmt->bindParam(':warrantyFile',$_FILES["warranty"]["name"]);

            $stmt->execute();

            try{
                $invoiceFilePath = $invoiceFile["tmp_name"];
                $warrantyFilePath = $warrantyFile["tmp_name"];

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->SMTPAuth = true; 
                $mail->Host = "smtp.gmail.com";
                $mail->SMTPSecure = "ssl";
                $mail->Port = 465;
                $mail->Username =  $_ENV['SENDER_EMAIL'];
                $mail->Password = $_ENV['AUTH_PASSWORD'];

                $mail->setFrom($_ENV['SENDER_EMAIL']);
                $mail->addAddress("singhankit8066@gmail.com");
                $mail->isHTML(true);
                $mail->addAttachment($invoiceFilePath);
                $mail->addAttachment($warrantyFilePath);

                $mail->Subject = "User Details";
                $mail->Body = "Installation Service Order No. {$serviceNumber} <br>
                               Modal Name: {$modalName} <br>
                               Name : {$name} <br>
                               Email Address : {$email} <br>
                               Mobile Number : {$phone} <br>
                               Address : {$address} <br>
                               City : {$city} <br>
                               State : {$state} <br>
                               Pincode : {$pincode} <br>
                               Serial Number : {$serialNumber} <br>
                               Purchase Date : {$purchaseDate} <br>
                ";

                $mail->send();
                $successMessage="Thank you for sharing the documents with us. Our team will verify the details and get back to you within 7 working days. FFIPL reserves the right to reject the warranty application if the registration terms & conditions are not met. Please refer to the product’s user manual for detailed warranty terms & conditions.";

                $errorMessage="";
                
            }catch(Exception $e){
                echo "Can't send mail: " . $e->getMessage();
                $errorMessage="Can't complete your request";
                $successMessage="";
                
            }
            session_start();
            session_unset();
            session_destroy();
            
        } else {
            $errorMessage = "All fields are required";
            $successMessage="";
            
        };
    };
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    
    exit;
}

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
        <form action="step3.php" method="post" enctype="multipart/form-data">
            <div class="flex-div">
                <div class="field-box">
                    <label for="serial">Serial Number : </label>
                    <input class="input-field" type="text" name="serial-number" id="serial" />
                </div>
                <div class="field-box">
                    <label for="date">Purchase Date : </label>
                    <input class="input-field" type="date" name="date" id="date" />
                </div>
            </div>
            <div class="field-box">
                <label for="invoice">Invoice (PDF) : </label>
                <input class="file-field" type="file" accept="application/pdf" name="invoice" id="invoice" />
            </div>
            <div class="field-box">
                <label for="warranty">Life Time Warranty Registration Form (PDF): </label>
                <input class="file-field" type="file" accept="application/pdf" name="warranty" id="warranty" />
            </div>
            <button type='submit' class='button'>
                Submit
                <!-- <img src="loading.svg" class="loading-image" width="100" /> -->
            </button>
        </form>
    </div>
    <div class="modal-container">
        <div class="overlay"></div>
        <div class="result-message">
            <?php 
                if($errorMessage){
                    echo "<img src='error.png' width='100' />";
                }elseif($successMessage){
                    echo "<img src='success.png' width='100' />";
                }
            ?>
            <?php 
                if($errorMessage){
                    echo $errorMessage;
                }elseif($successMessage){
                    echo $successMessage;
                } 
            ?>
        </div>
    </div>
    <?php
    if ($successMessage) {
        echo '<script>
        document.querySelector(".modal-container").style.display="block";
        document.querySelector(".modal-container").addEventListener("click", function(){
            document.querySelector(".modal-container").style.display="none";
            window.location = "index.php";
        });
    </script>';
    }

    if ($errorMessage) {
            echo '<script>
            document.querySelector(".modal-container").style.display="block";
            document.querySelector(".modal-container").addEventListener("click", function(){
                document.querySelector(".modal-container").style.display="none";
            });
        </script>';
    }

    // if($loading){
    //     echo '<script>
    //         document.querySelector(".button").style.display="none";
    //         document.querySelector(".loading-image").style.display="none";
    //     </script>';
    // }else{
    //      echo '<script>
    //         document.querySelector(".loading-image").style.display="none";
    //     </script>';
    // }

    ?>
</body>

</html>