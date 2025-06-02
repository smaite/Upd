<?php
/**
 * Helper functions for the NepalBooks Update Server
 */

/**
 * Get all releases for a specific channel
 * 
 * @param string $channel The update channel ('stable' or 'beta')
 * @return array Array of releases
 */
function getReleases($channel) {
    global $CHANNELS;
    
    if (!in_array($channel, $CHANNELS)) {
        return [];
    }
    
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    
    $data = json_decode(file_get_contents(DATA_FILE), true);
    
    if (!isset($data[$channel]) || !is_array($data[$channel])) {
        return [];
    }
    
    // Sort releases by version (newest first)
    usort($data[$channel], function($a, $b) {
        return version_compare($b['version'], $a['version']);
    });
    
    return $data[$channel];
}

/**
 * Get the latest release for a specific channel
 * 
 * @param string $channel The update channel ('stable' or 'beta')
 * @return array|null The latest release or null if none found
 */
function getLatestRelease($channel) {
    $releases = getReleases($channel);
    
    if (empty($releases)) {
        return null;
    }
    
    // Return the first release (already sorted by version)
    return $releases[0];
}

/**
 * Add a new release
 * 
 * @param array $release The release data
 * @return bool True on success, false on failure
 */
function addRelease($release) {
    global $CHANNELS;
    
    if (!isset($release['channel']) || !in_array($release['channel'], $CHANNELS)) {
        return false;
    }
    
    if (!file_exists(DATA_FILE)) {
        $data = [];
        foreach ($CHANNELS as $channel) {
            $data[$channel] = [];
        }
    } else {
        $data = json_decode(file_get_contents(DATA_FILE), true);
        
        // Initialize channels if they don't exist
        foreach ($CHANNELS as $channel) {
            if (!isset($data[$channel])) {
                $data[$channel] = [];
            }
        }
    }
    
    // Add the release to the appropriate channel
    $data[$release['channel']][] = $release;
    
    // Save the data
    return file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Delete a release
 * 
 * @param string $channel The update channel ('stable' or 'beta')
 * @param string $version The version to delete
 * @return bool True on success, false on failure
 */
function deleteRelease($channel, $version) {
    global $CHANNELS;
    
    if (!in_array($channel, $CHANNELS)) {
        return false;
    }
    
    if (!file_exists(DATA_FILE)) {
        return false;
    }
    
    $data = json_decode(file_get_contents(DATA_FILE), true);
    
    if (!isset($data[$channel]) || !is_array($data[$channel])) {
        return false;
    }
    
    // Find and remove the release
    $found = false;
    foreach ($data[$channel] as $key => $release) {
        if ($release['version'] === $version) {
            unset($data[$channel][$key]);
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        return false;
    }
    
    // Reindex the array
    $data[$channel] = array_values($data[$channel]);
    
    // Save the data
    return file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Upload a file for a release
 * 
 * @param array $file The file data from $_FILES
 * @param string $platform The platform (win, mac, linux)
 * @param string $version The version
 * @return string|false The URL of the uploaded file or false on failure
 */
function uploadReleaseFile($file, $platform, $version) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }
    
    // Sanitize the filename
    $filename = sanitizeFilename($file['name']);
    
    // Create a platform-specific filename
    $platformExtension = '';
    switch ($platform) {
        case 'win':
            $platformExtension = '.exe';
            break;
        case 'mac':
            $platformExtension = '.dmg';
            break;
        case 'linux':
            $platformExtension = '.AppImage';
            break;
    }
    
    $newFilename = 'nepalbooks-' . $version . '-' . $platform . $platformExtension;
    $uploadPath = UPLOAD_DIR . $newFilename;
    
    // Move the uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return false;
    }
    
    // Return the URL
    return BASE_URL . '/uploads/' . $newFilename;
}

/**
 * Sanitize a filename
 * 
 * @param string $filename The filename to sanitize
 * @return string The sanitized filename
 */
function sanitizeFilename($filename) {
    // Remove any path info
    $filename = basename($filename);
    
    // Replace spaces with underscores
    $filename = str_replace(' ', '_', $filename);
    
    // Remove any non-alphanumeric characters except for dots, hyphens, and underscores
    $filename = preg_replace('/[^a-zA-Z0-9\.\-\_]/', '', $filename);
    
    return $filename;
}

/**
 * Validate a version string
 * 
 * @param string $version The version to validate
 * @return bool True if valid, false otherwise
 */
function isValidVersion($version) {
    // Simple regex for semantic versioning (e.g., 1.0.0, 1.0.0-beta.1)
    return preg_match('/^(\d+)\.(\d+)\.(\d+)(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+[0-9A-Za-z-]+)?$/', $version);
}

/**
 * Check if a user is authenticated as admin
 * 
 * @return bool True if authenticated, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
}

/**
 * Authenticate a user
 * 
 * @param string $username The username
 * @param string $password The password
 * @return bool True if authenticated, false otherwise
 */
function authenticate($username, $password) {
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
        return true;
    }
    
    return false;
}

/**
 * Log out the current user
 */
function logout() {
    unset($_SESSION['admin']);
    session_destroy();
} 