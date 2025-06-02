<?php
// Start session for potential authentication
session_start();

// Include configuration and helper functions
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in for admin features
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

// Get all releases
$stableReleases = getReleases('stable');
$betaReleases = getReleases('beta');

// Get latest releases
$latestStable = getLatestRelease('stable');
$latestBeta = getLatestRelease('beta');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NepalBooks Update Server</title>
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="documentation.php">API Documentation</a>
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
                        <h4>NepalBooks Update Server</h4>
                    </div>
                    <div class="card-body">
                        <p>This server provides updates for the NepalBooks application. It supports both stable and beta update channels.</p>
                        <p>For integration with your application, please see the <a href="documentation.php">API Documentation</a>.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Stable Channel -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5>Stable Channel</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($latestStable): ?>
                        <h6>Latest Version: <?php echo htmlspecialchars($latestStable['version']); ?></h6>
                        <p>Released: <?php echo date('F j, Y', strtotime($latestStable['published_at'])); ?></p>
                        <div class="mb-3">
                            <strong>Release Notes:</strong>
                            <pre class="release-notes"><?php echo htmlspecialchars($latestStable['body']); ?></pre>
                        </div>
                        <h6>Downloads:</h6>
                        <ul class="list-group">
                            <?php foreach ($latestStable['assets'] as $asset): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($asset['name']); ?>
                                <a href="<?php echo htmlspecialchars($asset['browser_download_url']); ?>" class="btn btn-sm btn-primary">Download</a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p>No stable releases available.</p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="releases.php?channel=stable" class="btn btn-outline-success">View All Stable Releases</a>
                    </div>
                </div>
            </div>

            <!-- Beta Channel -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5>Beta Channel</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($latestBeta): ?>
                        <h6>Latest Version: <?php echo htmlspecialchars($latestBeta['version']); ?></h6>
                        <p>Released: <?php echo date('F j, Y', strtotime($latestBeta['published_at'])); ?></p>
                        <div class="mb-3">
                            <strong>Release Notes:</strong>
                            <pre class="release-notes"><?php echo htmlspecialchars($latestBeta['body']); ?></pre>
                        </div>
                        <h6>Downloads:</h6>
                        <ul class="list-group">
                            <?php foreach ($latestBeta['assets'] as $asset): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($asset['name']); ?>
                                <a href="<?php echo htmlspecialchars($asset['browser_download_url']); ?>" class="btn btn-sm btn-primary">Download</a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p>No beta releases available.</p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="releases.php?channel=beta" class="btn btn-outline-warning">View All Beta Releases</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Endpoints -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5>API Endpoints</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <strong>GET /api/updates/latest/stable</strong>
                                <p class="mb-0">Get the latest stable release</p>
                            </li>
                            <li class="list-group-item">
                                <strong>GET /api/updates/latest/beta</strong>
                                <p class="mb-0">Get the latest beta release</p>
                            </li>
                            <li class="list-group-item">
                                <strong>GET /api/updates/releases/stable</strong>
                                <p class="mb-0">Get all stable releases</p>
                            </li>
                            <li class="list-group-item">
                                <strong>GET /api/updates/releases/beta</strong>
                                <p class="mb-0">Get all beta releases</p>
                            </li>
                        </ul>
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