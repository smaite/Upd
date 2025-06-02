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

// Get all releases
$stableReleases = getReleases('stable');
$betaReleases = getReleases('beta');

// Process delete request
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $channel = $_POST['channel'] ?? '';
    $version = $_POST['version'] ?? '';
    
    if (deleteRelease($channel, $version)) {
        $message = "Release $version in $channel channel deleted successfully.";
        
        // Refresh the releases
        $stableReleases = getReleases('stable');
        $betaReleases = getReleases('beta');
    } else {
        $error = "Failed to delete release $version.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NepalBooks Update Server</title>
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
                        <a class="nav-link active" href="index.php">Admin Dashboard</a>
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
                        <h4>Admin Dashboard</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-end mb-3">
                            <a href="add_release.php" class="btn btn-primary">Add New Release</a>
                        </div>
                        
                        <ul class="nav nav-tabs" id="releaseTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="stable-tab" data-bs-toggle="tab" data-bs-target="#stable" type="button" role="tab">
                                    Stable Releases
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="beta-tab" data-bs-toggle="tab" data-bs-target="#beta" type="button" role="tab">
                                    Beta Releases
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content mt-3" id="releaseTabsContent">
                            <!-- Stable Releases -->
                            <div class="tab-pane fade show active" id="stable" role="tabpanel">
                                <?php if (empty($stableReleases)): ?>
                                <div class="alert alert-info">No stable releases found.</div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Version</th>
                                                <th>Published</th>
                                                <th>Mandatory</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($stableReleases as $release): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($release['version']); ?></td>
                                                <td><?php echo date('Y-m-d', strtotime($release['published_at'])); ?></td>
                                                <td>
                                                    <?php if ($release['mandatory']): ?>
                                                    <span class="badge bg-danger">Yes</span>
                                                    <?php else: ?>
                                                    <span class="badge bg-secondary">No</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="edit_release.php?channel=stable&version=<?php echo urlencode($release['version']); ?>" class="btn btn-sm btn-primary">Edit</a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                        data-channel="stable" data-version="<?php echo htmlspecialchars($release['version']); ?>">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Beta Releases -->
                            <div class="tab-pane fade" id="beta" role="tabpanel">
                                <?php if (empty($betaReleases)): ?>
                                <div class="alert alert-info">No beta releases found.</div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Version</th>
                                                <th>Published</th>
                                                <th>Mandatory</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($betaReleases as $release): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($release['version']); ?></td>
                                                <td><?php echo date('Y-m-d', strtotime($release['published_at'])); ?></td>
                                                <td>
                                                    <?php if ($release['mandatory']): ?>
                                                    <span class="badge bg-danger">Yes</span>
                                                    <?php else: ?>
                                                    <span class="badge bg-secondary">No</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="edit_release.php?channel=beta&version=<?php echo urlencode($release['version']); ?>" class="btn btn-sm btn-primary">Edit</a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                        data-channel="beta" data-version="<?php echo htmlspecialchars($release['version']); ?>">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this release? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <form method="post" action="index.php">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="channel" id="deleteChannel">
                        <input type="hidden" name="version" id="deleteVersion">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set up delete modal
        document.getElementById('deleteModal').addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const channel = button.getAttribute('data-channel');
            const version = button.getAttribute('data-version');
            
            document.getElementById('deleteChannel').value = channel;
            document.getElementById('deleteVersion').value = version;
        });
    </script>
</body>
</html> 