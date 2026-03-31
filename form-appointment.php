<?php

@include_once __DIR__ . '/mailgun-config.php';

$smtp_host  = getenv('MAILGUN_SMTP_HOST') ?: '';
$smtp_user  = getenv('MAILGUN_SMTP_USER') ?: '';
$smtp_pass  = getenv('MAILGUN_SMTP_PASS') ?: '';
$smtp_port  = (int)(getenv('MAILGUN_SMTP_PORT') ?: 587);
$from_email = getenv('MAILGUN_FROM_EMAIL')  ?: 'noreply@loganexpresscare.com.au';
$from_name  = getenv('MAILGUN_FROM_NAME')   ?: 'Logan Express Care';
$admin_email = getenv('MAILGUN_ADMIN_EMAIL') ?: 'info@loganexpresscare.com.au';

$errorMSG = "";

if (empty($_POST["fname"])) {
	$errorMSG = "First Name is required. ";
} else {
	$fname = $_POST["fname"];
}

if (empty($_POST["lname"])) {
	$errorMSG = "Last Name is required. ";
} else {
	$lname = $_POST["lname"];
}

if (empty($_POST["phone"])) {
	$errorMSG .= "Phone is required. ";
} else {
	$phone = $_POST["phone"];
}

if (empty($_POST["email"])) {
	$errorMSG .= "Email is required. ";
} else {
	$email = $_POST["email"];
}

if (empty($_POST["services"])) {
	$errorMSG .= "services is required. ";
} else {
	$services = $_POST["services"];
}

if (empty($_POST["date"])) {
	$errorMSG .= "Date is required. ";
} else {
	$date = $_POST["date"];
}

if (empty($_POST["message"])) {
	$errorMSG .= "Message is required. ";
} else {
	$message = $_POST["message"];
}

if ($errorMSG !== "") {
	echo $errorMSG;
	exit;
}

$subject = 'Book Appointment from site';

$html = "
<p><strong>First Name:</strong> " . htmlspecialchars($fname, ENT_QUOTES, 'UTF-8') . "</p>
<p><strong>Last Name:</strong> " . htmlspecialchars($lname, ENT_QUOTES, 'UTF-8') . "</p>
<p><strong>Phone:</strong> " . htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') . "</p>
<p><strong>Email:</strong> " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</p>
<p><strong>Services:</strong> " . htmlspecialchars($services, ENT_QUOTES, 'UTF-8') . "</p>
<p><strong>Date:</strong> " . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . "</p>
<p><strong>Message:</strong> " . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</p>
";

$success = false;

if ($smtp_host !== '' && $smtp_user !== '' && $smtp_pass !== '') {
	$base = __DIR__ . '/PHPMailer/src/';
	require_once $base . 'PHPMailer.php';
	require_once $base . 'SMTP.php';
	require_once $base . 'Exception.php';

	try {
		$mailer = new PHPMailer\PHPMailer\PHPMailer(true);
		$mailer->isSMTP();
		$mailer->Host       = $smtp_host;
		$mailer->SMTPAuth   = true;
		$mailer->Username   = $smtp_user;
		$mailer->Password   = $smtp_pass;
		$mailer->Port       = $smtp_port;
		$mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
		$mailer->CharSet    = 'UTF-8';
		$mailer->isHTML(true);
		$mailer->setFrom($from_email, $from_name);
		$mailer->addAddress($admin_email);
		$mailer->Subject = $subject;
		$mailer->Body    = $html;
		$mailer->send();
		$success = true;
	} catch (\Throwable $e) {
		error_log('form-appointment SMTP error: ' . $e->getMessage());
	}
}

echo $success ? "success" : "Something went wrong :(";
?>
