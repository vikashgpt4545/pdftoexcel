<?php
/**
 * PDFtoExcel.in - Server-Side Conversion API Endpoint
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

// Rate limiting: Max 60 conversion requests per minute per IP
if (!Database::checkRateLimit('convert', 60, 60)) {
    http_response_code(429);
    echo json_encode(['status' => 'error', 'message' => 'Rate limit exceeded. Please wait a moment.']);
    exit;
}

$rawInput = file_get_contents('php://input');
$jsonInput = json_decode($rawInput, true);
$input = is_array($jsonInput) ? $jsonInput : $_POST;

$toolId = preg_replace('/[^a-zA-Z0-9_-]/', '', $input['tool_id'] ?? 'pdf-to-excel');
$format = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', $input['format'] ?? 'xlsx')));
$fileSize = (int)($input['file_size'] ?? 0);

// Backward-compatible file size extraction if sent via multipart
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $fileSize = (int)$_FILES['file']['size'];
}

// Check file size (max 100MB)
if ($fileSize > 100 * 1024 * 1024 || $fileSize < 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid file size (max 100MB)']);
    exit;
}

$status = strtolower(trim(preg_replace('/[^a-zA-Z0-9_-]/', '', $input['status'] ?? 'success')));
if (!in_array($status, ['success', 'error'])) {
    $status = 'success';
}
$errorRaw = $input['error_message'] ?? $input['error_msg'] ?? null;
$errorMsg = $errorRaw ? substr(trim($errorRaw), 0, 500) : null;

// Log conversion statistics in database
Database::logConversion($toolId, $fileSize, $format, $status, $errorMsg);

echo json_encode([
    'status' => 'success',
    'message' => 'Conversion logged successfully',
    'tool' => $toolId,
    'format' => $format,
    'size' => $fileSize,
    'conversion_status' => $status
]);
exit;
