<?php
/**
 * Mailgun SMTP configuration - DO NOT COMMIT this file.
 *
 * Place this file on hosting at:
 * /home/<user>/public_html/mailgun-config.php
 */

// Mailgun HTTP API (used by queue workers — port 443, works on all hosting)
putenv('MAILGUN_DOMAIN=mg.loganexpresscare.com.au');       // your Mailgun sending domain
putenv('MAILGUN_API_BASE=https://api.mailgun.net');         // EU accounts: https://api.eu.mailgun.net
putenv('MAILGUN_API_KEY=YOUR_MAILGUN_PRIVATE_API_KEY');    // Settings → API Keys → Private API key

// Sender identity
putenv('MAILGUN_FROM_EMAIL=info@loganexpresscare.com.au');
putenv('MAILGUN_FROM_NAME=Logan Express Care');
putenv('MAILGUN_ADMIN_EMAIL=info@loganexpresscare.com.au');

// Site contact details used in outgoing emails
putenv('SITE_PHONE=1300 000 000');
putenv('SITE_URL=https://loganexpresscare.com.au');
