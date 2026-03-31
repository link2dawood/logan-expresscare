<?php
/**
 * Mailgun configuration - DO NOT COMMIT this file.
 *
 * Place this file on hosting at:
 * /home/<user>/public_html/mailgun-config.php
 *
 * Fill in your Mailgun credentials below.
 */

putenv('MAILGUN_DOMAIN=mg.loganexpresscare.com.au');           // Your Mailgun sending domain
putenv('MAILGUN_API_BASE=https://api.mailgun.net');             // US region; EU: https://api.eu.mailgun.net
putenv('MAILGUN_API_KEY=key-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // Your Mailgun private API key

// Sender identity (from address must belong to your verified Mailgun domain)
putenv('MAILGUN_FROM_EMAIL=noreply@loganexpresscare.com.au');
putenv('MAILGUN_FROM_NAME=Logan Express Care');
putenv('MAILGUN_ADMIN_EMAIL=info@loganexpresscare.com.au');
