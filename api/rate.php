<?php
/**
 * PDFtoExcel.in - Rating & User Feedback Endpoint
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$pageUrl = filter_var($input['url'] ?? '/', FILTER_SANITIZE_URL);
$rating = (int)($input['rating'] ?? 5);
$comments = isset($input['comments']) ? substr(trim($input['comments']), 0, 500) : null;

if ($rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid rating value']);
    exit;
}

Database::logRating($pageUrl, $rating, $comments);

echo json_encode([
    'status' => 'success',
    'message' => 'Thank you for your rating!',
    'rating' => $rating
]);
exit;
