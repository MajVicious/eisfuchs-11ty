<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Import the Dotenv class from the vlucas/phpdotenv package
use Dotenv\Dotenv;

//Load Composer's autoloader
require 'vendor/autoload.php';

// Initialize Dotenv
$dotenv = Dotenv::createImmutable(__DIR__);  // Use __DIR__ to reference the current directory
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars($_POST['message']);    

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      // Use this in production
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'w0122a15.kasserver.com';               //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $_ENV['SMTP_USER'];                    //SMTP username
        $mail->Password   = $_ENV['SMTP_PASS'];                     //SMTP password
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
        
        // Redirect to the Thank You page after the email is sent
        header('Location: /danke/');
        exit();  // Make sure to call exit after the redirect to stop further execution
 
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}