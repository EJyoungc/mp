# Changelog

All notable changes to the MaaSMS project will be documented in this file.

## [1.12.0] - 2026-10-21
### Added
- **Comprehensive Mother Profile & Clinical Dashboard**:
  - Completely redesigned `MotherLivewire` and view with an appealing AdminLTE layout, breadcrumbs, and maternal hero card.
  - Added real-time pregnancy tracker with gestational age calculation (weeks and days), trimester breakdown, Estimated Due Date (EDD) countdown, and a 40-week visual progress bar.
  - Implemented multi-session/infant management: full CRUD for infant sequence and LMP records, automatic sequence calculation, and clean session deletion with confirmation modal.
  - Added dedicated **Messages Received** section with live search, trimester filtering, delivery status filtering, message type toggle (Health Tips vs Targeted Pharmacy Ads), and an interactive SMS inspector modal displaying formatted gateway payload data.
  - Added quick **Direct SMS Dispatch** modal allowing doctors and clinical staff to send ad-hoc SMS messages directly to mothers with real-time character count and delivery tracking.
  - Added quick SMS resend action for failed or unconfirmed health tips.
  - Added detailed clinical & socio-demographic profile tab covering demographics, anthropometric measurements, obstetric risk factors, and chronic medical conditions.
- **Automated Feature Tests**:
  - Created `MotherProfileAndDetailsTest` with 5 Pest test cases covering profile rendering, active pregnancy calculations, session CRUD, messages section filtering, custom SMS dispatch, and modal cancel convention compliance.

## [1.11.0] - 2026-10-21
### Added
- **Districts & Areas Management Dashboard (System Admin)**:
  - Created `DistrictsAreasLivewire` component and view (`/districts-areas`) restricted to system administrators.
  - Added summary metric cards for Total Districts, Total Areas, Organizations Mapped, and Users/Mothers Mapped.
  - Implemented tabbed navigation with full CRUD for both Districts and Areas, including live name search, district dropdown filtering, and separate pagination.
  - Added cascading delete confirmation modals displaying linked counts and safety warnings.
  - Added quick shortcuts: 'Add Area' directly from a district row, and 'View Areas' to immediately switch and filter the Areas tab.
- **On-the-Fly Quick Add District & Area in Forms**:
  - Added inline '+' quick-action buttons and dedicated creation modals for adding new Districts across all major forms:
    - `MothersManagementLivewire` (Mothers registry)
    - `OrganizationsLivewire` (Organization registration/edit - added both Quick Add District and Quick Add Area)
    - `UserCreateLivewire` & `UserEditLivewire` (via `components/inputs/users/mother.blade.php`)
  - Newly created districts and areas are automatically selected upon creation, enabling seamless workflow without leaving the form.
- **Sidebar Navigation**:
  - Added 'Districts & Areas' navigation item under the System Admin menu with active state tracking.
- **Automated Feature Tests**:
  - Added `DistrictsAndAreasManagementTest` with 7 Pest test cases covering access control, District/Area CRUD, search/filtering, and on-the-fly district creation within forms.

## [1.10.0] - 2026-10-21
### Added
- **Organization User Provisioning & Roles Assignment**:
  - Implemented optional 'Assign Initial Administrator' toggle and form fields inside the organization creation modal in OrganizationsLivewire, automatically creating the lead administrator, assigning the admin role and organization ownership, auto-generating secure credentials, and verifying the user.
  - Added dedicated 'Add Staff Member' modal within the OrganizationProfileLivewire dashboard under the Staff & Members tab, allowing System Admins and Org Admins to create new staff members (Admins, Doctors, Practitioners) with custom or auto-generated passwords.
  - Added full inline editing of existing staff members (name, email, phone, role, active status, optional password reset) directly from the Staff & Members table.
  - Enforced pharmacy restriction: prevent assigning clinical roles (e.g. Doctor) to staff belonging to a pharmacy organization.
- **Custom User Credentials Dispatch & In-App Viewer**:
  - Created UserCredentialsMail Mailable and responsive HTML/Markdown email template delivering account login instructions, email, role, organization name, and clear temporary password.
  - Implemented in-app credentials modal with convenient 'Copy' buttons in both OrganizationsLivewire and OrganizationProfileLivewire, guaranteeing instant access to login credentials even if email delivery is delayed or queued.
- **Model & Mass-Assignment Enhancements**:
  - Added is_active to fillable on the User model to allow proper status updates across admin interfaces.
- **Automated Feature Tests**:
  - Expanded OrganizationManagementAndProfileTest with comprehensive Pest tests covering initial administrator creation during org registration, staff addition/editing from profile dashboard, email credential dispatch, and pharmacy clinical role restriction validation.

## [1.9.0] - 2026-10-20
### Added
- **Organization Profile Dashboard**:
  - Implemented OrganizationProfileLivewire component and view (/organizations/{id}/profile) providing a dedicated dashboard for organization details.
  - Added hero profile card with facility classification (Clinic vs Pharmacy), address, district/area, contact info, and owner metadata.
  - Added metric cards displaying counts for Total Staff, Registered Mothers, Active Tips / Ads, and Pending Verifications.
  - Added tabbed layout for Staff & Members, Registered Mothers, and Overview & Activity with live search, role filters, verification filters, and pagination.
  - Added inline actions for System Admins and Org Owners to approve or decline pending member verifications.
- **Enhanced Organizations Directory UI**:
  - Redesigned OrganizationsLivewire with AdminLTE card styling, top summary statistics (Total Organizations, Clinics, Pharmacies, Mothers), and responsive table.
  - Added real-time search across organization name, contact details, district, and area.
  - Added category filtering (Healthcare vs Pharmacy) and district filtering with pagination.
  - Updated the View action button to open the new Organization Profile dashboard.
- **Safe Cascading Deletion with Confirmation**:
  - Added a delete confirmation modal in OrganizationsLivewire with explicit warning about cascading removal of related records.
  - Implemented model-level cascading deletion in Organization::booted() and wrapped Livewire delete actions in database transactions to cleanly purge all related staff users, mothers, medical histories, message logs, tips, advertisements, ad delivery histories, and invitations across all tables.
- **Test Coverage**:
  - Added OrganizationManagementAndProfileTest covering directory search/filter, profile dashboard navigation, staff/mothers tabs, and complete cascading deletion verification.

## [1.8.0] - 2026-06-02
### Added
- **Ad Frequency Scheduling**:
  - Added custom schedule types (`daily`, `weekly`, `monthly`) and limits (`schedule_limit`) to `PharmacyAd` model.
  - Implemented custom database migration to add `schedule_type` and `schedule_limit` columns to `pharmacy_ads` table.
  - Developed custom scheduling logic inside a new console command version 2: `SendAdMessagesV2` (`ads:send-v2`).
  - Added UI elements in `PharmacyAdsManager` Livewire component and Blade view for defining the sending schedule and limits.
- **Dynamic Week Selection**:
  - Converted start week and end week inputs into `<select>` dropdowns inside the ad creation/editing modal.
  - Configured weeks to automatically load and restrict based on the target trimester selected using Livewire `wire:model.live` and lifecycle hook (`updatedTrimesterId`).

### Fixed
- **Testing Assertions & Environment Stability**:
  - Explicitly initialized the `is_active` attribute to `true` inside `PharmacyAdminRestrictionTest` user factories to prevent SQLite's null evaluation from triggering redirection to the `/waiting-approval` page.

## [1.7.0] - 2026-05-06

### Added
- **Bulk Tip Generator**:
  - Implemented high-performance bulk creation tools in both `WeeksLivewire` (Trimester view) and `WeekLivewire` (Weekly view).
  - Added "Select All" toggles for weeks, days, and time ranges to rapidly populate the pregnancy timeline.
- **Tip Management Enhancements**:
  - Added multi-selection and bulk deletion for tips within the weekly management view.
  - Integrated single-item delete actions for individual tips with confirmation.
- **Improved Import Sanitation**:
  - Added automatic phone number sanitation in `MothersImport`. Numbers starting with `265` are now automatically reformatted to the local `0xxxxxxxxx` format.
- **Comprehensive Deletion Cleanup**:
  - Centralized cascading delete logic in the `User` model to ensure that deleting a mother automatically removes her pregnancy history and SMS logs.
  - Implemented automated cleanup of `MessageHistory` when tips are deleted to maintain database integrity.
- **Enhanced Verification**:
  - Added `MothersImportTest` (Unit) to verify phone sanitation logic.
  - Added `MotherDeletionTest` (Feature) to verify centralized cleanup.
  - Added `TrimesterDeletionTest` (Feature) to verify deletion protections for trimesters and weeks.

### Changed
- **Enforced Model Permanence**:
  - Restricted the deletion of **Trimesters** and **Weeks** at the model level to protect the core application structure. Any deletion attempt now throws a managed exception.
  - Cleaned up the `WeeksLivewire` UI to remove all week deletion actions (individual and bulk).
- **Sidebar Access Control**:
  - Restricted access to "Days Time Range" configuration to **System Administrators** only, hiding it from Admins, Doctors, and Practitioners.

### Fixed
- **Data Integrity Constraints**:
  - Resolved SQL integrity violations by ensuring `MessageHistory` records are properly handled (deleted) when their parent tips are removed.
  - Added `week` and `trimester_id` to the `$fillable` property of the `Week` model to support mass assignment.

## [1.6.0] - 2026-05-05

### Added
- **Bulk Mother Management**:
  - Implemented multi-selection and bulk organization reassignment for system administrators in the `MothersManagementLivewire` component.
  - Added a "Select All" feature to quickly manage large sets of mother records.
  - Integrated a bulk reassignment modal to update organization IDs for both `User` and `History` records simultaneously.
- **Import Organization Selection**:
  - Added an organization selector to the bulk import modal for system administrators, allowing mothers to be assigned to specific organizations during upload.
- **Enhanced Test Coverage**:
  - Added `MothersManagementLivewireTest` and `MothersManagerTest` (Pest) to verify bulk reassignment, selection logic, and import organization assignment.

### Changed
- **Target Organization Filtering**:
  - Restricted organization selection to non-pharmacy entities when assigning or reassigning mothers, preventing clinical data from being incorrectly associated with pharmacies.

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
