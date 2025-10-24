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

	// MESSAGE
	if (empty($_POST["message"])) {
		$errorMSG .= "Message is required. ";
	} else {
		$message = $_POST["message"];
	}

	$subject = 'Contact Inquiry from Website';

	//$EmailTo = "info@yourdomain.com"; // Replace with your email.
    $EmailTo = "awaikentechnology@gmail.com";
    
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