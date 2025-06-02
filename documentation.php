<?php
// Start session
session_start();

// Include required files
require_once 'config.php';
require_once 'functions.php';

// Check if user is authenticated
$isAdmin = isAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - NepalBooks Update Server</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">NepalBooks Update Server</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="documentation.php">API Documentation</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($isAdmin): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/index.php">Admin Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/logout.php">Logout</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/login.php">Admin Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4>API Documentation</h4>
                    </div>
                    <div class="card-body">
                        <p>This documentation describes the API endpoints available for the NepalBooks Update Server.</p>
                        
                        <div class="alert alert-info">
                            <strong>Base URL:</strong> <?php echo htmlspecialchars(API_BASE); ?>
                        </div>
                        
                        <h5 class="mt-4">Authentication</h5>
                        <p>Most endpoints do not require authentication. However, endpoints that modify data (such as adding a release) require admin authentication.</p>
                        
                        <h5 class="mt-4">Response Format</h5>
                        <p>All responses are in JSON format.</p>
                    </div>
                </div>
                
                <!-- Get Latest Release -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5>Get Latest Release</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Endpoint:</strong> <code>GET /api/updates/latest/{channel}</code>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Description:</strong> Returns the latest release for the specified channel.
                        </div>
                        
                        <div class="mb-3">
                            <strong>Parameters:</strong>
                            <ul>
                                <li><code>channel</code> (path parameter) - The update channel (<code>stable</code> or <code>beta</code>)</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Example Request:</strong>
                            <pre class="bg-light p-3 mt-2"><code>GET <?php echo htmlspecialchars(API_BASE); ?>/updates/latest/stable</code></pre>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Example Response:</strong>
                            <pre class="bg-light p-3 mt-2"><code>{
  "tag_name": "v1.0.0",
  "version": "1.0.0",
  "published_at": "2023-01-15T10:00:00Z",
  "body": "Initial stable release\n- Feature 1\n- Feature 2\n- Bug fixes",
  "mandatory": false,
  "channel": "stable",
  "assets": [
    {
      "platform": "win",
      "browser_download_url": "https://example.com/downloads/nepalbooks-1.0.0-win.exe",
      "name": "nepalbooks-1.0.0-win.exe"
    },
    {
      "platform": "mac",
      "browser_download_url": "https://example.com/downloads/nepalbooks-1.0.0-mac.dmg",
      "name": "nepalbooks-1.0.0-mac.dmg"
    },
    {
      "platform": "linux",
      "browser_download_url": "https://example.com/downloads/nepalbooks-1.0.0-linux.AppImage",
      "name": "nepalbooks-1.0.0-linux.AppImage"
    }
  ]
}</code></pre>
                        </div>
                    </div>
                </div>
                
                <!-- Get All Releases -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5>Get All Releases</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Endpoint:</strong> <code>GET /api/updates/releases/{channel}</code>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Description:</strong> Returns all releases for the specified channel, sorted by version (newest first).
                        </div>
                        
                        <div class="mb-3">
                            <strong>Parameters:</strong>
                            <ul>
                                <li><code>channel</code> (path parameter) - The update channel (<code>stable</code> or <code>beta</code>)</li>
                            </ul>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Example Request:</strong>
                            <pre class="bg-light p-3 mt-2"><code>GET <?php echo htmlspecialchars(API_BASE); ?>/updates/releases/beta</code></pre>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Example Response:</strong>
                            <pre class="bg-light p-3 mt-2"><code>[
  {
    "tag_name": "v1.2.0-beta.2",
    "version": "1.2.0-beta.2",
    "published_at": "2023-05-05T16:45:00Z",
    "body": "Beta release with bug fixes\n- Fixed sync issues\n- Performance improvements\n- UI refinements",
    "mandatory": true,
    "channel": "beta",
    "assets": [...]
  },
  {
    "tag_name": "v1.2.0-beta.1",
    "version": "1.2.0-beta.1",
    "published_at": "2023-04-10T09:15:00Z",
    "body": "Beta release with new features\n- New dashboard\n- Cloud sync (beta)\n- Dark mode improvements",
    "mandatory": false,
    "channel": "beta",
    "assets": [...]
  }
]</code></pre>
                        </div>
                    </div>
                </div>
                
                <!-- Add New Release -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5>Add New Release</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Endpoint:</strong> <code>POST /api/updates/add</code>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Description:</strong> Adds a new release to the specified channel. Requires authentication.
                        </div>
                        
                        <div class="mb-3">
                            <strong>Authentication:</strong> Admin credentials required.
                        </div>
                        
                        <div class="mb-3">
                            <strong>Request Body:</strong>
                            <pre class="bg-light p-3 mt-2"><code>{
  "tag_name": "v1.2.0",
  "version": "1.2.0",
  "published_at": "2023-06-15T12:00:00Z",
  "body": "Release notes here...",
  "mandatory": false,
  "channel": "stable",
  "assets": [
    {
      "platform": "win",
      "browser_download_url": "https://example.com/downloads/app-1.2.0-win.exe",
      "name": "app-1.2.0-win.exe"
    }
  ]
}</code></pre>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Example Response:</strong>
                            <pre class="bg-light p-3 mt-2"><code>{
  "message": "Release added successfully",
  "release": {
    "tag_name": "v1.2.0",
    "version": "1.2.0",
    "published_at": "2023-06-15T12:00:00Z",
    "body": "Release notes here...",
    "mandatory": false,
    "channel": "stable",
    "assets": [...]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
                
                <!-- Integration with NepalBooks -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5>Integration with NepalBooks</h5>
                    </div>
                    <div class="card-body">
                        <p>To integrate this update server with the NepalBooks application, update the <code>UpdateService.ts</code> file with the appropriate server URL:</p>
                        
                        <pre class="bg-light p-3 mt-2"><code>// Custom update server URL
private customServerUrl: string = import.meta.env.PROD 
  ? 'https://your-server-url.com/api/updates' 
  : 'http://localhost/nepalbooks/upd/api/updates';</code></pre>
                        
                        <p class="mt-3">The update service will automatically check for updates, download updates, and handle the installation process.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>NepalBooks Update Server</h5>
                    <p>A simple update server for the NepalBooks application.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; <?php echo date('Y'); ?> NepalBooks</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 