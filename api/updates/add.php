<?php
/**
 * API endpoint for adding a new release
 * 
 * Endpoint: /api/updates/add
 * Method: POST
 * Requires authentication
 */

// Start session
session_start();

// Include required files
require_once '../../config.php';
require_once '../../functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS request (for CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Check if the user is authenticated
if (!isAdmin()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get the request body
$requestBody = file_get_contents('php://input');
$release = json_decode($requestBody, true);

// Validate the release data
if (!$release || !isset($release['tag_name']) || !isset($release['version']) || !isset($release['channel'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid release data']);
    exit;
}

// Validate the version
if (!isValidVersion($release['version'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid version format']);
    exit;
}

// Validate the channel
if (!in_array($release['channel'], $CHANNELS)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid channel']);
    exit;
}

// Set default values if not provided
if (!isset($release['published_at'])) {
    $release['published_at'] = date('c');
}

if (!isset($release['body'])) {
    $release['body'] = 'No release notes provided.';
}

if (!isset($release['mandatory'])) {
    $release['mandatory'] = false;
}

if (!isset($release['assets'])) {
    $release['assets'] = [];
}

// Add the release
if (addRelease($release)) {
    http_response_code(201);
    echo json_encode(['message' => 'Release added successfully', 'release' => $release]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to add release']);
} 