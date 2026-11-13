# Barangay ID Scanner

A browser-based resident directory and ID scanner for recording visitor, event, and certificate-request check-ins and check-outs.

## Current tech stack

- **PHP, HTML5, CSS, and vanilla JavaScript** — `index.php` serves the UI; application data and logic remain in browser JavaScript.
- **html5-qrcode 2.3.8** — camera QR/barcode scanning, currently loaded from the unpkg CDN.
- **Browser APIs** — `localStorage` for data, `FileReader` for resident photo previews, and `Blob` for CSV/JSON downloads.
- **XAMPP/Apache** — serves the PHP entry page. There is no PHP data backend or database connection in the current app.

## Features

- Scan a QR/barcode with a camera or enter an ID manually/use a USB scanner.
- Add, edit, search, and delete residents.
- Log residents in and out with a purpose.
- Search and delete log entries.
- Export resident and log data as CSV; back up and restore app data as JSON.

## Run locally

1. Copy the project into your XAMPP web root, for example `htdocs/Baranggay/`.
2. Start Apache in the XAMPP control panel.
3. Open `http://localhost/Baranggay/` (or `http://localhost/Baranggay/index.php`) in a modern browser.
4. Allow camera access when prompted. Camera access generally requires HTTPS or localhost.

Open the page through XAMPP/Apache; opening `index.php` directly as a file will not execute PHP. Camera access generally requires HTTPS or localhost. The QR-scanning library is fetched from a CDN, so scanning requires an internet connection unless that library is hosted locally.

## Data storage and limitations

The current app stores residents and logs in the browser's `localStorage` under `brgy_residents` and `brgy_logs`. Data is local to that browser and origin; it is not shared across devices or users. Clearing browser data can remove it. JSON backup and restore are the available transfer mechanism.

The current app has no user accounts, roles, server-side validation, centralized database, or multi-user synchronization. Do not treat it as a production system for sensitive resident data without adding access controls, secure server-side storage, backups, and an appropriate privacy/security review.

## Recommended finalization stack

For a XAMPP-based multi-user version, a straightforward next step is:

- **Frontend:** retain the existing HTML/CSS/JavaScript UI.
- **Backend:** PHP 8.2+ with PDO and prepared statements for API/database access.
- **Database:** MariaDB (the database commonly bundled with XAMPP); the included SQL also works with MySQL 8+.
- **Photo storage:** store uploaded image files outside public upload paths or in controlled storage, and keep only their relative path in the database.

This is a recommendation, not an implemented backend. The existing page continues to use `localStorage` until it is explicitly updated to call server endpoints.

## Database schema

The initial schema is in [`schema.sql`](./schema.sql). It defines:

- `residents` — resident ID number, name, contact details, and an optional photo path.
- `attendance_logs` — check-in/out time, purpose, and resident identity snapshots. Deleting a resident leaves their log history intact.

To create the database with XAMPP:

1. Open phpMyAdmin at `http://localhost/phpmyadmin/`.
2. Choose **Import**, select `schema.sql`, and run the import. Alternatively, execute the file in the SQL tab.
3. Confirm that the `barangay_id_scanner` database and both tables were created.

The schema is not connected to the page yet. To complete that integration, implement PHP endpoints for resident CRUD, attendance logs, and photo uploads; use PDO prepared statements; then replace the browser-only read/write functions in `index.php` with calls to those endpoints. Convert existing JSON backups when migrating local data. Store timestamps consistently in UTC and convert them for display.

## Data mapping for a backend

| Browser data | Database column |
|---|---|
| Resident `id` | `residents.id` |
| Resident `idNumber` | `residents.id_number` |
| Resident `fullName` | `residents.full_name` |
| Resident `address` | `residents.address` |
| Resident `contact` | `residents.contact` |
| Resident `photo` data URL | Save the image as a file; store its relative path in `residents.photo_path` |
| Log `id` | `attendance_logs.id` |
| Log `residentId` | `attendance_logs.resident_id` |
| Resident name and ID at log time | `attendance_logs.resident_name` and `attendance_logs.resident_id_number` |
| Log `timestamp` | `attendance_logs.occurred_at` |
| Log `status` | `attendance_logs.status` (`in` or `out`) |
| Log `purpose` | `attendance_logs.purpose` |
