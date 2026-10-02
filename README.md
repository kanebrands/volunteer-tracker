# Volunteer Tracker

Volunteer Tracker is an administrator-only Joomla component for recording volunteer hours by person and event. It provides a dashboard for summary totals, charts, searchable records, record editing, deletion, and CSV export.

## Minimum Requirements

- Joomla 5.x
- PHP 8.1 or newer
- MySQL or MariaDB supported by Joomla 5
- Administrator access to install Joomla extensions
- A user account with the needed Joomla permissions for `com_volunteertracker`

## Installation

1. Clone or download this source folder.
2. Create the installable package from the project root:

   ```sh
   mkdir -p dist
   rm -f dist/com_volunteertracker-1.0.14.zip
   zip -r dist/com_volunteertracker-1.0.14.zip admin media updates com_volunteertracker.xml -x "*.DS_Store"
   ```

3. In the Joomla administrator area, go to **System > Install > Extensions**.
4. Upload and install `dist/com_volunteertracker-1.0.14.zip`.
5. Open **Components > Volunteer Tracker**.
6. Configure Joomla permissions for the component if non-super-user administrators need access.

The component installer creates the `#__volunteertracker_entries` database table automatically.

## Capabilities and Features

- Administrator-only dashboard for volunteer tracking.
- Add and edit volunteer records with volunteer name, event name, event date, hours, and role.
- Autocomplete volunteer, event, and role inputs from prior saved records.
- Generate event reports listing volunteers and assigned roles, with CSV export.
- Delete selected records with Joomla permission checks.
- Summary metrics for total volunteers, total events, cumulative hours, and average hours per entry.
- Bar chart of hours by event.
- Pie chart of hours by volunteer.
- CSV export of the dashboard table.
- Joomla access-control actions for administer, manage, create, edit, and delete.
- Joomla update-server metadata for GitHub-hosted release packages.
- Install, update, and uninstall SQL scripts for the component table.

## Release Packages

Release ZIP files should be saved in the `dist/` folder. That folder is intentionally ignored by Git so generated packages are not committed with the source.

Before publishing a GitHub release, update these placeholder URLs with the real GitHub owner and repository:

- `com_volunteertracker.xml`
- `updates/com_volunteertracker.xml`

## Removal

To remove the installed component from Joomla:

1. In the Joomla administrator area, go to **System > Manage > Extensions**.
2. Search for **Volunteer Tracker**.
3. Select the component and choose **Uninstall**.

The uninstall script drops the `#__volunteertracker_entries` table. Export any volunteer data you need before uninstalling.

To remove the source from a development machine, delete this source folder after confirming any needed changes have been committed or backed up. Generated release packages can be removed by deleting the ignored `dist/` folder.
