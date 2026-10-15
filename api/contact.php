<?php
/**
 * PDFtoExcel.in - Support & Contact Inquiry Endpoint
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

// Rate limiting: Max 5 inquiries per 5 minutes per IP
if (!Database::checkRateLimit('contact', 5, 300)) {
    http_response_code(429);
    echo json_encode(['status' => 'error', 'message' => 'Too many inquiries submitted. Please wait a few minutes before trying again.']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
if (!is_array($input)) {
    $input = $_POST;
}

// Honeypot check for spam bots
if (!empty($input['company_website'])) {
    // Return fake success to deceive automated bots without database insertion
    echo json_encode([
        'status' => 'success',
        'message' => 'Your inquiry has been received.'
    ]);
    exit;
}

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$subject = trim($input['subject'] ?? '');
$message = trim($input['message'] ?? '');

// Validation
if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Please provide a valid name (2 to 100 characters).']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Please provide a valid email address.']);
    exit;
}

if (mb_strlen($subject) < 3 || mb_strlen($subject) > 200) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Please provide a subject line (3 to 200 characters).']);
    exit;
}

if (mb_strlen($message) < 10 || mb_strlen($message) > 3000) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Please provide a message of at least 10 characters (up to 3,000 characters).']);
    exit;
}

$success = Database::logContactInquiry($name, $email, $subject, $message);

if ($success) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Thank you for reaching out! Our engineering team will review your inquiry within 24 business hours.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'We were unable to save your inquiry due to a temporary server issue. Please email support@pdftoexcel.in directly.'
    ]);
}
exit;
