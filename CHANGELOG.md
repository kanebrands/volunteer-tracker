# Changelog

All notable changes to Volunteer Tracker are documented here.

## 1.0.15

- Optimized Volunteer Input and Event Input repeated-entry workflow: **Save** now stores the current record and returns to a blank input form for the next entry.
- Updated GitHub release and update metadata for the public `kanebrands/volunteer-tracker` repository.
- Reworked `README.md` for public release consumption and Joomla release ZIP installation.
- Added this changelog.

## 1.0.14

- Removed the displayed "Author" label from the dashboard heading card.
- Made volunteer hours optional.
- Defaulted blank volunteer hours to `0.0`.
- Kept volunteer, event, and role as required volunteer input fields.
- Added Event Role Report sorting by role or volunteer name, with role as the default.

## 1.0.13

- Added an Event Records card below Volunteer Records on the dashboard.
- Added Event Records CSV export.
- Added Event Records edit and delete actions.
- Added event delete confirmation warning that associated volunteer entries will also be deleted.
- Added event deletion behavior that removes all volunteer records tied to the deleted event.
- Updated the dashboard event count to reflect saved event records.

## 1.0.12

- Added Event Input as a separate submenu item.
- Added event records with event name, event date, and event location.
- Required event name and event date for event creation.
- Enforced uniqueness for event name plus event date.
- Changed Volunteer Input to select from saved events instead of using free-form event text.
- Removed event date input from Volunteer Input.
- Displayed events in Volunteer Input as `Event Name - Event Date`.
- Updated Event Role Report to show only volunteer and role columns.
- Updated Event Role Report CSV export to match the displayed columns.

## 1.0.11

- Added Event Role Report submenu.
- Added reports for a selected event.
- Added report CSV export.
- Added volunteer and role listing for event reports.
- Added delete actions for erroneous volunteer entries.

## 1.0.10

- Changed Notes to Role in volunteer records.
- Added role autocomplete based on saved role entries.
- Added dashboard card showing how many unique people have served in a selected role.
- Added dashboard card showing how many times a selected volunteer has served in a selected role.
- Migrated existing notes into role values where possible.

## 1.0.9

- Added autocomplete suggestions for Volunteer and Event fields from saved records.
- Added editing support for existing volunteer entries.

## 1.0.8

- Added independent dashboard chart display limits for event and volunteer charts.
- Added chart limit options for 10, 50, 100, and all records.

## 1.0.7

- Removed "Administrator workspace" text from the dashboard heading.
- Improved Volunteer Input layout so fields align cleanly.

## 1.0.6

- Reworked the dashboard into a modern panel-based layout.
- Added graphic card styling and chart panels.
- Added dashboard title icon and version/author/GitHub heading metadata.
- Split dashboard, volunteer input, and reports into submenu-driven admin areas.
- Removed footer version text from the dashboard.

## 1.0.5

- Added dashboard panel empty states so charts and displays no longer appear blank.
- Added chart failure messaging for unavailable or non-functional chart rendering.

## 1.0.4

- Restyled dashboard summary areas into a more modern visual layout.
- Added version display for the extension.
- Improved dashboard handling for Hours by Event and Hours by Volunteer data.

## 1.0.3

- Fixed Hours by Event and Hours by Volunteer dashboard data population from saved volunteer records.

## 1.0.2

- Added `dist/` to `.gitignore` for generated Joomla release packages.
- Added macOS metadata file patterns to `.gitignore`.
- Added public package README content for requirements, installation, features, and removal.

## 1.0.1

- Fixed Joomla installer SQL path issue by placing install SQL where Joomla expects it.

## 1.0.0

- Initial administrator-only Joomla component for Volunteer Tracker.
- Added volunteer records table and installer SQL.
- Added dashboard entry point.
- Added Volunteer Input for recording volunteer service.
- Added uninstall SQL for removing component data.
