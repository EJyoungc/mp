# Changelog

All notable changes to the MaaSMS project will be documented in this file.

## [1.5.1] - 2026-05-04

### Fixed
- **Excel Processing**: Resolved a "Class 'ZipArchive' not found" error during bulk import by verifying server-side PHP extension configuration.
- **Bulk Import Row Logic**: Adjusted Excel preview and import logic to correctly skip template rows. The system now starts reading data from **Row 4**, aligning with the "Mothers" template (Headings, Sample Data, and Descriptions/Comments).

### Added
- **Import Validation Feedback**: Added a Livewire warning alert if no data is found in the uploaded Excel file starting from the expected data row (Row 4).

## [1.5.0] - 2026-04-29

### Removed
- **Coordinate-Based Tracking**:
  - Removed `latitude` and `longitude` fields from `User`, `Organization`, `PharmacyAd`, and `MessageHistory` models.
  - Eliminated browser Geolocation API scripts from the registration workflow.
  - Removed `radius_km` from `PharmacyAd` model.
- **Proximity Logic**:
  - Deleted the `calculateDistance` (Haversine) method from the pharmacy ad dispatch system.

### Changed
- **Refined Area-Based Targeting**:
  - Replaced coordinate proximity with strict geographic `Area` matching for pharmacy advertisements.
  - Migrated advertisement delivery logging from the generic `MessageHistory` table to the dedicated `AdHistory` table.
  - Updated `app:send-pharmacy-ads` and `app:send-pharmacy-ads-broadcast` to operate without coordinate data and log to `AdHistory`.
- **Registration Enhancements**:
  - Added **District** and **Area** selection to the "Create a New Organization" workflow.
  - Implemented client-side dynamic filtering for Areas during organization creation.
  - Standardized `organization_verify` status to `approved` for organization creators and invited users.
- **Dashboard UI Update**:
  - Replaced "Reached Locations" (coordinates) with "Reached Areas" in the monetization analytics.
  - Removed radius inputs and coordinate display from advertisement management modals.
  - Added Edit functionality for Pharmacy Advertisements with dynamic modal titles and loading states.

### Fixed
- **Ad Resend Logic**: Updated `AdHistoriesLivewire` and `SendSmsAdJob` to use `sendSmsGeneric`, ensuring compatibility with the `AdHistory` model.
- **Ad Dispatch Stability**: Added null checks for organizational data and fixed SQL integrity constraints (missing `tip_id`) during ad creation.
- **Registration Validation**: Resolved an "invalid organization id" error when creating a new organization during signup by making the join-related `organization_id` nullable.
- **Data Consistency**: Fixed a typo in the `MothersImport` class affecting vacuum delivery data (`vacuum` -> `vacum`).

## [1.4.0] - 2026-04-29

### Added
- **Hierarchical Geolocation System**:
  - `District` and `Area` models for structured location tracking.
  - `LocationSeeder` containing all 28 Malawi districts and major areas.
  - `district_id` and `area_id` tracking for both `User` (Mothers/Staff) and `Organization` (Clinics/Pharmacies).
- **Ad Delivery & History Tracking**:
  - `AdHistory` model to track every ad sent to mothers.
  - `AdHistoriesLivewire` dashboard for monitoring ad delivery status.
  - **Resend Capability**: Manual re-triggering of failed or missed ad messages.
  - **API Response Inspector**: Modal to view raw Africa's Talking responses for ad delivery.
- **Area-Based Ad Targeting**:
  - `ads:send` command to automatically match active pharmacy ads with mothers in the same geographic `Area`.
  - `SendSmsAdJob` for asynchronous ad delivery via Africa's Talking.
- **Dynamic Form Enhancements**:
  - Dependent dropdowns for District/Area selection in all registration forms.
  - "Quick Add Area" modal integrated into Mother and Organization creation workflows.
  - Updated Excel Import/Export to support District and Area fields.

### Changed
- Refactored `SmsService` to generically handle `MessageHistory` and `AdHistory` models.
- Updated `MothersManagementLivewire`, `UserCreateLivewire`, and `UserEditLivewire` with location tracking logic.
- Standardized organization management to include geographic assignment.

## [1.3.0] - 2026-04-22
...
### Added
- **Pharmacy Admin Restrictions**:
  - `RestrictPharmacyAdminMiddleware` to block pharmacy admins from accessing mothers, organizations, and trimester settings.
  - Sidebar links dynamically hidden for pharmacy admins.
- **User Approval System**:
  - `approve()` and `decline()` functionality in `UsersLivewire` for organization admins.
  - UI indicators and action buttons for pending member verifications.
- **Email Invitation System**:
  - `Invitation` model and migration for tracking secure registration invites.
  - `InvitationMail` for sending registration links with unique tokens.
  - "Invite User" workflow integrated into `UsersLivewire`.
  - Token-based registration in `CreateNewUser` action, enabling pre-approval and automatic organization assignment.
- **Test Coverage**:
  - `PharmacyAdminRestrictionTest` (Pest) covering RBAC and route restrictions.

### Changed
- Restricted pharmacy admins to only adding/inviting **practitioners**.
- Refactored `UsersLivewire` to support member verification and invitations.
- Fixed `tests/Pest.php` to use the correct `uses()` syntax.

## [1.2.0] - 2026-04-17

### Added
- **Monetization Module**:
  - `PharmacyAd` model and migration for managing targeted product advertisements.
  - Ability for Super Admins to create and manage week-targeted SMS ads.
  - Ad performance tracking (`total_sent`).
- **Enhanced Dashboard Analytics**:
  - Comprehensive, role-specific "Small Box" stats for all user levels.
  - **Super Admin**: Global view of all Organizations, Mothers, and system-wide Delivery Rates.
  - **Org Admin/Doctor**: Metrics for staff counts (Doctors/Practitioners) and institutional success rates.
  - **Real-time Delivery Rate**: Dynamic calculation of SMS success percentages based on `MessageHistory`.
- **UI Workflow Improvements**:
  - New dedicated "Pharmacy Advertisements" management interface for Super Admins.
  - Improved "Organization Requests" table with integrated actions and status badges.

### Changed
- Refactored `DashboardLivewire` to include advanced analytics and ad management logic.
- Standardized modal management for creation workflows.

## [1.1.0] - 2026-04-17

### Added
- **Multi-Tenancy Architecture**: Implemented `BelongsToOrganization` trait to scope `Tip`, `History`, and `MessageHistory` models by organization.
- **Hierarchical RBAC**: 
  - New role helper methods in `User` model (`isSystemAdmin`, `isOrgAdmin`, `isDoctor`, etc.).
  - Automated filtering in Livewire components based on user organization and role.
- **Tip Approval Workflow**:
  - Tips now have statuses: `draft`, `pending_approval`, `approved`.
  - Practitioners create pending tips; Doctors and Admins can approve them.
  - Creator and Approver tracking added to each tip.
- **Template Sharing**:
  - System Admins can mark tips as "Global Templates".
  - Organizations can browse global templates and copy them to their own institution.
- **Scalable Messaging**: 
  - Integrated Laravel Queues (`SendSmsTipJob`) for all SMS deliveries.
  - Chunked database processing in `CheckMessages` command for better performance.

### Removed
- **Manual SMS Trigger**: Removed `CheckerLivewire` component as the system is now fully automated via background tasks.
- **Hardcoded Credentials**: Eliminated all instances of hardcoded API keys in favor of environment variables.
- **Timezone Inconsistency**: Standardized `Carbon` usage across messaging services to ensure correct tip delivery timing.

### Changed
- Refactored `DashboardLivewire`, `WeekLivewire`, `MotherLivewire`, and `CheckerLivewire` to use new RBAC and multi-tenancy logic.
- Standardized phone number formatting in `SmsService`.

## [1.0.0] - Initial Release
- Basic pregnancy tracking and health tips.
- Direct Africa's Talking SMS integration.
- User management with basic role assignment.
