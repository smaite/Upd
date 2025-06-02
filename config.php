<?php
/**
 * Configuration file for NepalBooks Update Server
 */

// Database configuration (if using a database)
define('DB_HOST', 'localhost');
define('DB_NAME', 'nepalbooks_updates');
define('DB_USER', 'root');
define('DB_PASS', '');

// File storage configuration
define('DATA_FILE', __DIR__ . '/data/releases.json');
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// Admin credentials
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); // Change this in production!

// Server settings
define('BASE_URL', 'http://localhost/nepalbooks/upd');
define('API_BASE', BASE_URL . '/api');

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
                'tag_name' => 'v1.0.0',
                'version' => '1.0.0',
                'published_at' => '2023-01-15T10:00:00Z',
                'body' => "Initial stable release\n- Feature 1\n- Feature 2\n- Bug fixes",
                'mandatory' => false,
                'channel' => 'stable',
                'assets' => [
                    [
                        'platform' => 'win',
                        'browser_download_url' => BASE_URL . '/uploads/nepalbooks-1.0.0-win.exe',
                        'name' => 'nepalbooks-1.0.0-win.exe'
                    ],
                    [
                        'platform' => 'mac',
                        'browser_download_url' => BASE_URL . '/uploads/nepalbooks-1.0.0-mac.dmg',
                        'name' => 'nepalbooks-1.0.0-mac.dmg'
                    ],
                    [
                        'platform' => 'linux',
                        'browser_download_url' => BASE_URL . '/uploads/nepalbooks-1.0.0-linux.AppImage',
                        'name' => 'nepalbooks-1.0.0-linux.AppImage'
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