<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Support both JSON and form POST
if (!$data) {
    $data = $_POST;
}

$fname   = htmlspecialchars(trim($data['firstname'] ?? ''));
$lname   = htmlspecialchars(trim($data['lastname'] ?? ''));
$email   = trim($data['email'] ?? '');
$phone   = htmlspecialchars(trim($data['phone-number'] ?? $data['phonenumber'] ?? ''));
$message = htmlspecialchars(trim($data['message'] ?? ''));

// Validate
if (empty($fname) || empty($lname) || empty($email) || empty($phone) || empty($message)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
    exit;
}

$to      = 'info@willskrakt.com';
$subject = 'New Contact Form Submission from ' . $fname . ' ' . $lname;

$body  = "You have received a new message from your website contact form.\r\n\r\n";
$body .= "Name:    $fname $lname\r\n";
$body .= "Email:   $email\r\n";
$body .= "Phone:   $phone\r\n\r\n";
$body .= "Message:\r\n$message\r\n";

$headers  = "From: Willskrakt Website <noreply@willskrakt.com>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['status' => 'success', 'message' => 'Your message has been sent successfully! We\'ll get back to you soon.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to send message. Please try emailing us directly at info@willskrakt.com']);
}
