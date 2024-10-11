<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

// Initialize Dotenv
$dotenv = Dotenv/Dotenv::createImmutable(__DIR__);  // Use __DIR__ to reference the current directory
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars($_POST['message']);    

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Use this in production
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'w0122a15.kasserver.com';               //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $_ENV['SMTP_USER'];                    //SMTP username
        $mail->Password   = $_ENV['SMTP_PASSWORD'];                     //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('noreply@eisfuchs-leipzig.de', 'Eisfuchs-Leipzig');
        $mail->addAddress('info@eisfuchs-leipzig.de', 'Eisfuchs-Leipzig');     //Add a recipient
        $mail->addReplyTo($email, $name);                                      // Use the user's email for replies
        $mail->addBCC('antrack.lars@gmail.com');

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Website - Neue Anfrage';
        $mail->Body    = "Ihr habt eine Nachricht von $name. Email: $email. Nachricht: $message";
        $mail->AltBody = "Ihr habt eine Nachricht von $name. Email: $email. Nachricht: $message";

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}