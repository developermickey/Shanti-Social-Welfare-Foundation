<?php
declare(strict_types=1);

header('Content-Type: application/json');

$to = 'mukeshpathak345@gmail.com';
$siteName = 'Shanti Social Welfare Foundation';

function json_response(bool $success, string $message, int $status = 200): void
{
    http_response_code($status);
    echo json_encode([
        'success' => $success,
        'message' => $message,
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Only POST requests are allowed.', 405);
}

// Honeypot field for simple spam protection.
if (!empty($_POST['website'] ?? '')) {
    json_response(true, 'Thank you. Your message has been received.');
}

$name = trim((string)($_POST['name'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$service = trim((string)($_POST['service'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($name === '' || $phone === '' || $email === '' || $service === '' || $message === '') {
    json_response(false, 'Please fill in all required fields.', 422);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(false, 'Please enter a valid email address.', 422);
}

$safeName = preg_replace('/[\r\n]+/', ' ', $name);
$safeEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
$subject = 'New enquiry from ' . $siteName;

$body = "New website enquiry\n\n";
$body .= "Name: {$safeName}\n";
$body .= "Phone: {$phone}\n";
$body .= "Email: {$safeEmail}\n";
$body .= "Service Needed: {$service}\n\n";
$body .= "Message:\n{$message}\n";

$headers = [
    'From: ' . $siteName . ' <no-reply@shantifoundation.in>',
    'Reply-To: ' . $safeName . ' <' . $safeEmail . '>',
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion(),
];

$sent = mail($to, $subject, $body, implode("\r\n", $headers));

if (!$sent) {
    json_response(false, 'Mail could not be sent. Please call +91-9111755922 or email us directly.', 500);
}

json_response(true, 'Thank you. Your message has been sent successfully.');
