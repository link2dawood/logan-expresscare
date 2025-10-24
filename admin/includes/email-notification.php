<?php
/**
 * Email Notification System
 * Logan Express Care - Application Status Notifications
 */

class EmailNotification {
    private $fromEmail = 'noreply@loganexpresscare.com.au';
    private $fromName = 'Logan Express Care';
    
    /**
     * Send application status notification
     */
    public function sendStatusNotification($userEmail, $userName, $status, $notes = '') {
        $subject = $this->getSubject($status);
        $message = $this->getMessage($userName, $status, $notes);
        
        $headers = [
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'Content-Type: text/html; charset=UTF-8',
            'X-Mailer: PHP/' . phpversion()
        ];
        
        return mail($userEmail, $subject, $message, implode("\r\n", $headers));
    }
    
    /**
     * Get email subject based on status
     */
    private function getSubject($status) {
        switch ($status) {
            case 'approved':
                return 'Application Approved - Welcome to Logan Express Care!';
            case 'rejected':
                return 'Application Update - Logan Express Care';
            case 'incomplete':
                return 'Additional Information Required - Logan Express Care';
            default:
                return 'Application Status Update - Logan Express Care';
        }
    }
    
    /**
     * Get email message based on status
     */
    private function getMessage($userName, $status, $notes = '') {
        $baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
        
        switch ($status) {
            case 'approved':
                return $this->getApprovedMessage($userName, $notes);
            case 'rejected':
                return $this->getRejectedMessage($userName, $notes);
            case 'incomplete':
                return $this->getIncompleteMessage($userName, $notes, $baseUrl);
            default:
                return $this->getDefaultMessage($userName, $status, $notes);
        }
    }
    
    /**
     * Approved application message
     */
    private function getApprovedMessage($userName, $notes) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Application Approved</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #273A29; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .success-icon { font-size: 48px; color: #28a745; margin-bottom: 20px; }
                .btn { display: inline-block; background: #E2A76F; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Congratulations!</h1>
                </div>
                <div class='content'>
                    <div style='text-align: center;'>
                        <div class='success-icon'>✓</div>
                        <h2 style='color: #273A29;'>Application Approved</h2>
                    </div>
                    
                    <p>Dear " . htmlspecialchars($userName) . ",</p>
                    
                    <p>We are pleased to inform you that your application to join Logan Express Care has been <strong>approved</strong>!</p>
                    
                    <p>Welcome to our team of dedicated healthcare professionals. We are excited to have you on board and look forward to working with you.</p>
                    
                    " . (!empty($notes) ? "<p><strong>Additional Notes:</strong><br>" . nl2br(htmlspecialchars($notes)) . "</p>" : "") . "
                    
                    <p>Our HR team will be in touch with you shortly to discuss the next steps in your onboarding process.</p>
                    
                    <p>If you have any questions, please don't hesitate to contact us.</p>
                    
                    <p>Best regards,<br>
                    <strong>Logan Express Care Team</strong></p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>Logan Express Care | Providing Quality Healthcare Services</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Rejected application message
     */
    private function getRejectedMessage($userName, $notes) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Application Update</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #273A29; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Application Update</h1>
                </div>
                <div class='content'>
                    <p>Dear " . htmlspecialchars($userName) . ",</p>
                    
                    <p>Thank you for your interest in joining Logan Express Care. After careful consideration, we regret to inform you that we are unable to proceed with your application at this time.</p>
                    
                    " . (!empty($notes) ? "<p><strong>Feedback:</strong><br>" . nl2br(htmlspecialchars($notes)) . "</p>" : "") . "
                    
                    <p>We encourage you to reapply in the future when you meet our current requirements.</p>
                    
                    <p>Thank you for your understanding.</p>
                    
                    <p>Best regards,<br>
                    <strong>Logan Express Care Team</strong></p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>Logan Express Care | Providing Quality Healthcare Services</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Incomplete application message
     */
    private function getIncompleteMessage($userName, $notes, $baseUrl) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Additional Information Required</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #273A29; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .btn { display: inline-block; background: #E2A76F; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Additional Information Required</h1>
                </div>
                <div class='content'>
                    <p>Dear " . htmlspecialchars($userName) . ",</p>
                    
                    <p>Thank you for submitting your application to Logan Express Care. We have reviewed your application and require some additional information to complete the process.</p>
                    
                    " . (!empty($notes) ? "<p><strong>Required Information:</strong><br>" . nl2br(htmlspecialchars($notes)) . "</p>" : "") . "
                    
                    <p>Please log in to your account and complete the required information. Your application will be reviewed again once all required information is provided.</p>
                    
                    <div style='text-align: center;'>
                        <a href='" . $baseUrl . "/staff-onboarding.php' class='btn'>Complete Application</a>
                    </div>
                    
                    <p>If you have any questions, please don't hesitate to contact us.</p>
                    
                    <p>Best regards,<br>
                    <strong>Logan Express Care Team</strong></p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>Logan Express Care | Providing Quality Healthcare Services</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Default message
     */
    private function getDefaultMessage($userName, $status, $notes) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Application Status Update</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #273A29; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Application Status Update</h1>
                </div>
                <div class='content'>
                    <p>Dear " . htmlspecialchars($userName) . ",</p>
                    
                    <p>Your application status has been updated to: <strong>" . ucfirst(str_replace('_', ' ', $status)) . "</strong></p>
                    
                    " . (!empty($notes) ? "<p><strong>Notes:</strong><br>" . nl2br(htmlspecialchars($notes)) . "</p>" : "") . "
                    
                    <p>If you have any questions, please don't hesitate to contact us.</p>
                    
                    <p>Best regards,<br>
                    <strong>Logan Express Care Team</strong></p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>Logan Express Care | Providing Quality Healthcare Services</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
?>
