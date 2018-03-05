<?php
require '../libs/phpmailer/PHPMailerAutoload.php';
require_once "MailConfiguration.php";
class Mailer{
   public function Mailer(){

   }

    public function sendMail($subject,$content,$to){
    try
    {
        $mailconfig = MailConfiguration::$mailconfig;
        $host = $mailconfig['Host'];
        $port = $mailconfig['Port'];
        $username = $mailconfig['Username'];
        $password = $mailconfig['Password'];

	$phpmailer= new PHPMailer(true); 

	$phpmailer->SMTPSecure = "tls";
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Debugoutput = 'html';
        $phpmailer -> IsSMTP(); // telling the class to use SMTP
        $phpmailer -> Host = $host; // SMTP server
        $phpmailer -> Port  = $port;// set the SMTP port for the GMAIL server; 465 for ssl and 587 for tls
        $phpmailer -> Username = $username; // Gmail account username
        $phpmailer -> Password = $password;// Gmail account password
        $phpmailer -> SMTPDebug = 0;
        $phpmailer -> SetFrom('support@softcons.net', 'Softcons, Inc.'); //set from name
	$phpmailer -> Subject = $subject;
        /*$phpmailer -> Subject = "Tag4Track";
        $body = "User Name: Aasish\n\r";
        $body = $body . "User EmailID: aasish.jwalapuram@gmail.com\n\r";
        $body = $body . "User Contact Numner: 9966449906\n\r";
        $body = $body . "User Interested In: NA\n\r";
        $body = $body . "User Message: Test Mail\n\r";
        $to = "aasish.jwalapuram@gmail.com";*/
        $body = "<html>
<head>
</head>
<body style='margin:0px;'>
<header>
</header>
".$content."
<div style='width: 100%;color:#2ca8c2;background: #304c7b;font-size:5px;margin-top:10px;margin-bottom:10px;position: fixed;bottom: 0;'>.</div>
Thanks and Regards<br>
Mobveus Mobility Matrix Team<br/>
<img src='http://106.51.226.187:8083/MMM/content/img/Mobveus_Logo.png' width='150' style='width:150px;'/>
</body>
</html>";
        $phpmailer -> MsgHTML($body);

        $phpmailer -> AddAddress($to, "To Name");

        if(!$phpmailer -> Send())
        {
return $phpmailer -> ErrorInfo;
            echo "Mailer Error: " . $phpmailer -> ErrorInfo;
        }
        else
        {
return "Message sent!";
            echo "Message sent!";
        }
    }
    catch (Exception $ex){
        echo $ex ->getMessage();
    }catch (phpmailerException $e) {
      echo $e->errorMessage(); //Pretty error messages from PHPMailer
      echo "<p>Using Username: ".$mail->Username."</p>";
    }
  }
}
?>