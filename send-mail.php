<?php

@include_once __DIR__ . '/mailgun-config.php';

$DEBUG_MODE     = isset($_REQUEST['debug']) && $_REQUEST['debug'] === '1';
$DEBUG_LOG_FILE = __DIR__ . '/send-mail-debug.log';
$QUEUE_FILE     = __DIR__ . '/consultation-queue.jsonl';
$MAIL_LOG_FILE  = __DIR__ . '/consultation-mail.log';

function isAjaxRequest()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function debugLog($message, $context = null)
{
    global $DEBUG_MODE, $DEBUG_LOG_FILE;
    if (!$DEBUG_MODE) return;
    $entry = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    if ($context !== null) {
        $json = json_encode($context);
        if ($json !== false) $entry .= ' | ' . $json;
    }
    @file_put_contents($DEBUG_LOG_FILE, $entry . PHP_EOL, FILE_APPEND);
}

function respondSuccess()
{
    if (isAjaxRequest()) { echo 'success'; exit; }
    header('Location: thankyou.php');
    exit;
}

function respondError($statusLine, $debugMessage = '')
{
    global $DEBUG_MODE;
    if (isAjaxRequest()) {
        header($statusLine);
        echo ($DEBUG_MODE && $debugMessage !== '') ? 'error:' . $debugMessage : 'error';
        exit;
    }
    header('Location: single-page.php?status=error');
    exit;
}

function queueSubmission($data, $reason)
{
    global $QUEUE_FILE;
    $entry = array('queued_at' => date('c'), 'reason' => $reason, 'payload' => $data);
    $json  = json_encode($entry);
    if ($json === false) return false;
    return @file_put_contents($QUEUE_FILE, $json . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
}

function logMailLine($message)
{
    global $MAIL_LOG_FILE;
    @file_put_contents($MAIL_LOG_FILE, '[' . date('c') . '] ' . $message . PHP_EOL, FILE_APPEND);
}

register_shutdown_function(function () {
    $error = error_get_last();
    if (!$error) return;
    $fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR);
    if (in_array($error['type'], $fatalTypes, true)) debugLog('Fatal shutdown error', $error);
});

debugLog('Request started', array(
    'method'    => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '',
    'ajax'      => isAjaxRequest(),
    'post_keys' => array_keys($_POST),
));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('HTTP/1.1 405 Method Not Allowed', 'invalid_method');
}

$payload = array(
    'userType'        => isset($_POST['userType'])        ? $_POST['userType']        : '',
    'serviceInterest' => isset($_POST['serviceInterest']) ? $_POST['serviceInterest'] : '',
    'fullName'        => isset($_POST['fullName'])        ? $_POST['fullName']        : '',
    'phoneNumber'     => isset($_POST['phoneNumber'])     ? $_POST['phoneNumber']     : '',
    'emailAddress'    => isset($_POST['emailAddress'])    ? $_POST['emailAddress']    : '',
    'contactMethod'   => isset($_POST['contactMethod'])   ? $_POST['contactMethod']   : '',
    'contactTime'     => isset($_POST['contactTime'])     ? $_POST['contactTime']     : '',
    'message'         => isset($_POST['message'])         ? $_POST['message']         : '',
);

try {
    if (!queueSubmission($payload, 'queued')) {
        logMailLine('Queue write failed');
    } else {
        debugLog('Submission queued');
    }
    respondSuccess();
} catch (Exception $e) {
    error_log('send-mail.php error: ' . $e->getMessage());
    debugLog('Exception caught', array('exception' => $e->getMessage()));
    respondError('HTTP/1.1 500 Internal Server Error', $e->getMessage());
}
exit;
