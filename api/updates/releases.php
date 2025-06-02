<?php
/**
 * API endpoint for getting all releases
 * 
 * Endpoint: /api/updates/releases/{channel}
 */

// Include required files
require_once '../../config.php';
require_once '../../functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request (for CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Check if channel is provided
if (!isset($_GET['channel'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Channel parameter is required']);
    exit;
}

$channel = $_GET['channel'];

// Validate channel
if (!in_array($channel, $CHANNELS)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid channel. Must be "stable" or "beta".']);
    exit;
}

// Get all releases
$releases = getReleases($channel);

if (empty($releases)) {
    http_response_code(404);
    echo json_encode(['error' => 'No releases found for this channel.']);
    exit;
}

// Return the releases
echo json_encode($releases); 