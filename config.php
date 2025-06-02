<?php
/**
 * Configuration file for NepalBooks Update Server
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load .env file
loadEnv(__DIR__ . '/.env');

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'nepalbooks_updates');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// File storage configuration
define('DATA_FILE', __DIR__ . '/data/releases.json');
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// Admin credentials
define('ADMIN_USERNAME', getenv('ADMIN_USERNAME') ?: 'admin');
define('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'admin123');

// Server settings
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost/nepalbooks/upd');
define('API_BASE', getenv('API_BASE') ?: BASE_URL . '/api');

// Update channels
$CHANNELS = ['stable', 'beta'];

// Ensure data directory exists
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// Ensure uploads directory exists
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Create releases file if it doesn't exist
if (!file_exists(DATA_FILE)) {
    $defaultData = [
        'stable' => [
            [
                'tag_name' => 'v1.1.2',
                'version' => '1.1.2',
                'published_at' => '2025-06-02T10:00:00Z',
                'body' => "Updated Services",
                'mandatory' => false,
                'channel' => 'stable',
                'assets' => [
                    [
                        'platform' => 'win',
                        'browser_download_url' => BASE_URL . '/uploads/nepalbooks-1.1.2-win.exe',
                        'name' => 'nepalbooks-1.1.2-win.exe'
                    ]
                ]
            ]
        ],
        'beta' => [
            [
                'tag_name' => 'v1.1.0-beta.1',
                'version' => '1.1.0-beta.1',
                'published_at' => '2023-02-10T14:30:00Z',
                'body' => "Beta release with new features\n- New dashboard\n- Cloud sync (beta)\n- Dark mode improvements",
                'mandatory' => false,
                'channel' => 'beta',
                'assets' => [
                    [
                        'platform' => 'win',
                        'browser_download_url' => BASE_URL . '/uploads/nepalbooks-1.1.0-beta.1-win.exe',
                        'name' => 'nepalbooks-1.1.0-beta.1-win.exe'
                    ],
                    [
                        'platform' => 'mac',
                        'browser_download_url' => BASE_URL . '/uploads/nepalbooks-1.1.0-beta.1-mac.dmg',
                        'name' => 'nepalbooks-1.1.0-beta.1-mac.dmg'
                    ],
                    [
                        'platform' => 'linux',
                        'browser_download_url' => BASE_URL . '/uploads/nepalbooks-1.1.0-beta.1-linux.AppImage',
                        'name' => 'nepalbooks-1.1.0-beta.1-linux.AppImage'
                    ]
                ]
            ]
        ]
    ];
    
    file_put_contents(DATA_FILE, json_encode($defaultData, JSON_PRETTY_PRINT));
} 