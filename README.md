# NepalBooks PHP Update Server

A PHP-based update server with a GUI for managing updates for the NepalBooks application. This server provides a user-friendly interface for managing releases and an API for the application to check for updates.

## Features

- Web-based GUI for managing releases
- Admin dashboard for adding, editing, and deleting releases
- Separate stable and beta update channels
- Version comparison using semantic versioning
- Platform-specific downloads (Windows, macOS, Linux)
- Support for mandatory updates
- RESTful API for application integration

## Requirements

- PHP 7.4 or higher
- Apache web server with mod_rewrite enabled
- Write permissions for the `data` and `uploads` directories

## Installation

1. Upload the files to your web server
2. Make sure the `data` and `uploads` directories are writable by the web server
3. Update the `config.php` file with your server settings
4. Access the server through your web browser

## Directory Structure

```
upd/
├── admin/                 # Admin interface
│   ├── add_release.php    # Add new release
│   ├── edit_release.php   # Edit existing release
│   ├── index.php          # Admin dashboard
│   ├── login.php          # Admin login
│   └── logout.php         # Admin logout
├── api/                   # API endpoints
│   └── updates/           
│       ├── add.php        # Add new release via API
│       ├── latest.php     # Get latest release
│       └── releases.php   # Get all releases
├── assets/                # Static assets
│   ├── css/               # CSS files
│   └── js/                # JavaScript files
├── data/                  # Data storage
│   └── releases.json      # Release data
├── uploads/               # Upload storage
├── .htaccess              # Apache configuration
├── config.php             # Server configuration
├── functions.php          # Helper functions
├── index.php              # Main page
└── README.md              # Documentation
```

## API Endpoints

### Get Latest Release

```
GET /api/updates/latest/{channel}
```

Returns the latest release for the specified channel (stable or beta).

### Get All Releases

```
GET /api/updates/releases/{channel}
```

Returns all releases for the specified channel, sorted by version (newest first).

### Add New Release

```
POST /api/updates/add
```

Adds a new release to the specified channel. Requires authentication.

Example request body:
```json
{
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
}
```

## Admin Interface

The admin interface is accessible at `/admin` and requires authentication. Default credentials are:

- Username: admin
- Password: admin123

**Important:** Change these credentials in the `config.php` file before deploying to production.

## Integration with NepalBooks

This update server is designed to work with the UpdateService in the NepalBooks application. The service will check for updates from this server and handle the download and installation process. 