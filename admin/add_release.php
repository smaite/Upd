<?php
// Start session
session_start();

// Include required files
require_once '../config.php';
require_once '../functions.php';

// Check if user is authenticated
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Process form submission
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (empty($_POST['version']) || empty($_POST['tag_name']) || empty($_POST['channel'])) {
        $error = 'Version, tag name, and channel are required.';
    } else {
        $version = $_POST['version'];
        $tagName = $_POST['tag_name'];
        $channel = $_POST['channel'];
        $notes = $_POST['notes'] ?? '';
        $mandatory = isset($_POST['mandatory']) && $_POST['mandatory'] === '1';
        
        // Validate version
        if (!isValidVersion($version)) {
            $error = 'Invalid version format. Please use semantic versioning (e.g., 1.0.0 or 1.0.0-beta.1).';
        } else {
            // Create release data
            $release = [
                'tag_name' => $tagName,
                'version' => $version,
                'published_at' => date('c'),
                'body' => $notes,
                'mandatory' => $mandatory,
                'channel' => $channel,
                'assets' => []
            ];
            
            // Handle file uploads for each platform
            $platforms = ['win', 'mac', 'linux'];
            
            foreach ($platforms as $platform) {
                if (isset($_FILES[$platform]) && $_FILES[$platform]['error'] === UPLOAD_ERR_OK) {
                    $fileUrl = uploadReleaseFile($_FILES[$platform], $platform, $version);
                    
                    if ($fileUrl) {
                        $extension = '';
                        switch ($platform) {
                            case 'win':
                                $extension = '.exe';
                                break;
                            case 'mac':
                                $extension = '.dmg';
                                break;
                            case 'linux':
                                $extension = '.AppImage';
                                break;
                        }
                        
                        $release['assets'][] = [
                            'platform' => $platform,
                            'browser_download_url' => $fileUrl,
                            'name' => 'nepalbooks-' . $version . '-' . $platform . $extension
                        ];
                    }
                }
            }
            
            // Add the release
            if (addRelease($release)) {
                $message = "Release $version added successfully.";
            } else {
                $error = "Failed to add release $version.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Release - NepalBooks Update Server</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">NepalBooks Update Server</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Admin Dashboard</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4>Add New Release</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <form method="post" action="add_release.php" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="version" class="form-label">Version</label>
                                    <input type="text" class="form-control" id="version" name="version" placeholder="1.0.0" required>
                                    <div class="form-text">Use semantic versioning (e.g., 1.0.0 or 1.0.0-beta.1)</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="tag_name" class="form-label">Tag Name</label>
                                    <input type="text" class="form-control" id="tag_name" name="tag_name" placeholder="v1.0.0" required>
                                    <div class="form-text">Usually 'v' followed by the version number</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Channel</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="channel" id="channelStable" value="stable" checked>
                                    <label class="form-check-label" for="channelStable">
                                        Stable
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="channel" id="channelBeta" value="beta">
                                    <label class="form-check-label" for="channelBeta">
                                        Beta
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Release Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="5"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="mandatory" id="mandatory" value="1">
                                    <label class="form-check-label" for="mandatory">
                                        Mandatory Update
                                    </label>
                                    <div class="form-text">If checked, users will be required to install this update.</div>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5>Upload Files</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="win" class="form-label">Windows Installer (.exe)</label>
                                        <input class="form-control" type="file" id="win" name="win">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="mac" class="form-label">macOS Installer (.dmg)</label>
                                        <input class="form-control" type="file" id="mac" name="mac">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="linux" class="form-label">Linux Installer (.AppImage)</label>
                                        <input class="form-control" type="file" id="linux" name="linux">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Add Release</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 