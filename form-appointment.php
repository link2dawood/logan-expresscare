<?php

@include_once __DIR__ . '/mailgun-config.php';

$mg_domain   = getenv('MAILGUN_DOMAIN')      ?: '';
$mg_api_base = rtrim(getenv('MAILGUN_API_BASE') ?: 'https://api.mailgun.net', '/');
$mg_api_key  = getenv('MAILGUN_API_KEY')     ?: '';
$from_email  = getenv('MAILGUN_FROM_EMAIL')  ?: 'noreply@loganexpresscare.com.au';
$from_name   = getenv('MAILGUN_FROM_NAME')   ?: 'Logan Express Care';
$admin_email = getenv('MAILGUN_ADMIN_EMAIL') ?: 'info@loganexpresscare.com.au';

$errorMSG = "";

// FIRSTNAME
if (empty($_POST["fname"])) {
	$errorMSG = "First Name is required. ";
} else {
	$fname = $_POST["fname"];
}

// LASTNAME
if (empty($_POST["lname"])) {
	$errorMSG = "Last Name is required. ";
} else {
	$lname = $_POST["lname"];
}

// PHONE
if (empty($_POST["phone"])) {
	$errorMSG .= "Phone is required. ";
} else {
	$phone = $_POST["phone"];
}

// EMAIL
if (empty($_POST["email"])) {
	$errorMSG .= "Email is required. ";
} else {
	$email = $_POST["email"];
}

// SERVICES
if (empty($_POST["services"])) {
	$errorMSG .= "services is required. ";
} else {
	$services = $_POST["services"];
}

// DATE
if (empty($_POST["date"])) {
	$errorMSG .= "Date is required. ";
} else {
	$date = $_POST["date"];
}

// MESSAGE
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

if ($mg_domain !== '' && $mg_api_key !== '' && function_exists('curl_init')) {
	$url  = $mg_api_base . '/v3/' . $mg_domain . '/messages';
	$from = $from_name . ' <' . $from_email . '>';
	$ch   = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL            => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST           => true,
		CURLOPT_POSTFIELDS     => array('from' => $from, 'to' => $admin_email, 'subject' => $subject, 'html' => $html),
		CURLOPT_USERPWD        => 'api:' . $mg_api_key,
		CURLOPT_TIMEOUT        => 15,
	));
	$body   = curl_exec($ch);
	$err    = curl_error($ch);
	$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	$success = (!$err && $status >= 200 && $status < 300);
}

if ($success) {
	echo "success";
} else {
	echo "Something went wrong :(";
}
?>
