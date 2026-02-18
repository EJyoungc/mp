# Maternal Health Monitoring System (MP) - Documentation

## Overview
This Laravel-based application is designed for monitoring maternal health, providing pregnancy-related tips to mothers via SMS, and managing healthcare practitioners and organizations. It uses Livewire for its dynamic user interface.

## User Roles
The system implements Role-Based Access Control (RBAC) with the following roles:
1. **System Admin**: Full access to the system.
2. **Admin**: Manages users and organizations, excluding system admins.
3. **Doctor**: Can view and manage certain user data.
4. **Mother**: The primary beneficiary of the system (patients).
5. **Practitioner**: Healthcare staff with limited administrative capabilities.

## Key Features

### 1. User & Role Management
- **User Registration**: Support for manual registration and Excel import for mothers.
- **Access Control**: Role-based filtering of user lists and permissions.
- **Account Management**: Activating/deactivating users and password resets via email.
- **Organization Association**: Staff members (Admin, Doctor, Practitioner) must be linked to an organization and verified by the organization's admin.

### 2. Organization Management
- **CRUD Operations**: Create, Read, Update, and Delete organizations.
- **Verification System**: Handles requests from staff members to join an organization.
- **Verification Middleware**: The `IsOrginizationVerified` middleware ensures that users (except System Admins) are linked to a verified organization before they can access the dashboard and other protected features. If they are not linked or verification is pending, they are redirected to an organization selection/check page.

### 3. Maternal Health Tracking
- **Mother Profiles**: Detailed records including DOB, marital status, occupation, and contact information.
- **Pregnancy History**: Tracks infant number and Last Menstrual Period (LMP) date.
- **Week/Day Calculation**: Automatically calculates the current pregnancy week and day based on the LMP.
- **Trimesters & Weeks**: Organizes pregnancy data into trimesters and specific weeks.

### 4. Pregnancy Tips & Messaging System
- **Tip Management**: Educational tips are stored and categorized by trimester, week, and day.
- **SMS Integration**: Uses the **Africa's Talking SDK** to send tips to mothers' mobile phones.
- **Automated Messaging**: A checker system (implemented in `CheckerLivewire`) identifies mothers due for a tip based on their pregnancy stage and defined time ranges.
- **Message History**: Tracks sent, unsent, and failed messages.

### 5. Dashboard
- **Analytics Overview**: Displays counts and lists of mothers, users, messages, and tips.
- **Action Center**: Handles pending organization verification requests and allows for data imports/exports.

## Technical Details

### Models
- **User**: Extends Authenticatable, contains personal and medical history fields.
- **Mother**: Specific model for additional mother-related data (often synced or used in conjunction with the User model).
- **History**: Stores pregnancy history and contains the logic for week/day calculation.
- **Organization**: Represents clinics or health centers.
- **Trimester, Week, Day**: Structural models for organizing tips.
- **Tip**: The content sent to mothers.
- **MessageHistory**: Logs all communication attempts.

### Key Helpers
- **StandardData**: Handles encryption/decryption (using Hashids), password generation, and provides static data like Malawi districts, religions, and education levels.

### Important Livewire Components
- `DashboardLivewire`: Main entry point for admins.
- `UsersLivewire`: Comprehensive user management.
- `MotherLivewire`: Detailed view and history management for a specific mother.
- `CheckerLivewire`: The engine that triggers SMS sending based on pregnancy progress.
- `OrganizationsLivewire`: Management of health centers.

## External Integrations
- **Africa's Talking**: For SMS gateway services.
- **Maatwebsite Excel**: For importing and exporting data via Excel.
- **Jantinnerezo LivewireAlert**: For frontend notifications.
- **Hashids**: For obfuscating IDs in URLs.
