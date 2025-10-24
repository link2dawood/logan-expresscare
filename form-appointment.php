<?php

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

	$subject ='Book Appointment from site';

	$EmailTo = "info@yourdomain.com"; // Replace with your email.

	// prepare email body text
	$Body = "";
	$Body .= "fname: ";
	$Body .= $fname;
	$Body .= "\n";
	$Body .= "lname: ";
	$Body .= $lname;
	$Body .= "\n";
	$Body .= "Phone: ";
	$Body .= $phone;
	$Body .= "\n";
	$Body .= "Email: ";
	$Body .= $email;
	$Body .= "\n";
	$Body .= "services: ";
	$Body .= $services;
	$Body .= "\n";
	$Body .= "Date: ";
	$Body .= $date;
	$Body .= "\n";
	$Body .= "Message: ";
	$Body .= $message;
	$Body .= "\n";

	// send email
	$success = @mail($EmailTo, $subject, $Body, "From:".$email);

	// redirect to success page
	if ($success && $errorMSG == ""){
	   echo "success";
	}else{
		if($errorMSG == ""){
			echo "Something went wrong :(";
		} else {
			echo $errorMSG;
		}
	}

?>