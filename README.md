# Volunteer Tracker

Volunteer Tracker is an administrator-only Joomla component for planning events, recording volunteer service, tracking roles, and exporting simple reports.

## Requirements

- Joomla 5.x
- PHP 8.1 or newer
- MySQL or MariaDB supported by Joomla 5
- Joomla administrator access to install extensions
- Joomla permissions for `com_volunteertracker` when access is delegated to non-super-user administrators

## Installation

1. Download the latest release ZIP from [Volunteer Tracker Releases](https://github.com/kanebrands/volunteer-tracker/releases).
2. In the Joomla administrator area, go to **System > Install > Extensions**.
3. Upload and install the downloaded `com_volunteertracker` ZIP file.
4. Open **Components > Volunteer Tracker**.
5. Configure Joomla permissions for the component if additional administrators need access.

The installer creates the required Volunteer Tracker database tables automatically.

## Features

- Modern administrator dashboard with summary panels, chart panels, and record tables.
- Event input screen for event name, event date, and event location.
- Volunteer input screen for volunteer name, event, role, and optional hours.
- Fast repeated entry workflow: use **Save** to store a record and immediately continue with a blank input form.
- Event-backed volunteer records so event names and dates stay consistent across reports.
- Unique event enforcement by event name and event date.
- Dashboard charts for hours by event and hours by volunteer, with independent display limits.
- Dashboard cards for role participation and volunteer role counts.
- Volunteer Records table with CSV export, edit actions, and delete actions.
- Event Records table with CSV export, edit actions, and delete actions.
- Event deletion also removes associated volunteer records after confirmation.
- Event Role Report with role/name sorting and CSV export.
- Joomla access-control actions for administer, manage, create, edit, and delete.
- Joomla update metadata for GitHub-hosted release packages.

## Updates

Volunteer Tracker is configured for GitHub-hosted releases:

- Releases: [https://github.com/kanebrands/volunteer-tracker/releases](https://github.com/kanebrands/volunteer-tracker/releases)
- Update feed source: `updates/com_volunteertracker.xml`

Install future release ZIP files through Joomla's extension installer, or use Joomla's extension update workflow once the public update feed is available from the repository.

## Removal

To remove the installed component from Joomla:

1. In the Joomla administrator area, go to **System > Manage > Extensions**.
2. Search for **Volunteer Tracker**.
3. Select the component and choose **Uninstall**.

The uninstall script removes the Volunteer Tracker database tables. Export any volunteer or event data you need before uninstalling.

To remove the source from a development machine, delete this source folder after confirming any needed changes have been committed or backed up.
