# Maternal Health Monitoring System (MaaSMS) - Documentation

## Overview
This Laravel-based application is designed for monitoring maternal health, providing pregnancy-related tips to mothers via SMS, and managing healthcare practitioners and organizations. It uses a multi-tenant architecture to support multiple independent health institutions.

## User Roles
The system implements Role-Based Access Control (RBAC) with the following roles:
1. **System Admin**: Full access to the system, manages organizations and monetization.
2. **Admin / Org Admin**: Manages users and settings within their organization.
    - **Pharmacy Admin**: Specialized admin role for pharmacy organizations. Restricted from maternal health data; can only manage practitioners and pharmacy ads.
3. **Doctor**: Clinical oversight, manages mothers and approves health tips.
4. **Practitioner**: Healthcare staff who register mothers and create tips (require approval).
5. **Mother**: Patients who receive tailored pregnancy tips via SMS.

## Key Features

### 1. Multi-Tenant Architecture (Team System)
- **Organizations as Teams**: Each institution (clinic or pharmacy) acts as a team with an `owner_id`.
- **Membership Approval**: Users joining an existing organization are placed in a `pending` state until approved by the owner or a system admin.
- **Restricted Access**: Unapproved members are blocked via the `EnsureUserIsApproved` middleware.
- **Data Isolation**: All sensitive data (Mothers, Tips, History) is scoped by `organization_id`.

### 2. Pregnancy Tips & Approval Workflow
- **Tip Lifecycle**: Practitioners create tips -> Doctors review and approve -> System dispatches.
- **Organization-Specific Tips**: Each institution can have its own tailored content.
- **Global Templates**: Shared tips marked by System Admins for cross-org reuse.

### 3. Automated Messaging System
- **Engine**: Powered by the `app:check-messages` console command and Laravel Queues.
- **Background Processing**: Messages are dispatched to the `SendSmsTipJob` for asynchronous, scalable delivery.
- **Reliability**: Includes automatic retries and exponential backoff for failed SMS attempts.
- **History Tracking**: Comprehensive logging in `MessageHistory` including raw API responses.

### 4. Monetization (Location-Based Pharmacy Ads)
- **Pharmacy Management**: Organizations marked as `is_pharmacy` can manage their own product advertisements. 
- **Role Restrictions**: Pharmacy admins are restricted from:
    - Accessing **Mother** records or histories.
    - Managing **Organization** details (beyond their own).
    - Configuring **Trimesters**, **Weeks**, or **Day Ranges**.
    - Creating/Inviting any role other than **Practitioner**.
- **Smart Targeting**: Ads are delivered based on:
    - **Pregnancy Stage**: Specific week ranges OR targeted trimesters (1, 2, or 3).
    - **Area-Based Targeting**: Ads are delivered to mothers residing in the same geographic `Area` as the pharmacy's organization.
    - **Outreach Analytics**: 
        - Pharmacy owners can track "Recent Reaches" and view which areas their ads have reached.
        - **Ad Delivery History**: Dedicated tracking in `AdHistory` with resend capabilities and API log inspection.
    - **Automation**: The `app:send-pharmacy-ads` command handles matching and delivery based on District/Area assignments.

    ### 5. Geolocation System
    - **Structured Data**: Hierarchical tracking using `District` and `Area` models.
    - **Malawi Coverage**: Pre-seeded with all 28 districts and their primary administrative areas.
    - **Data Association**: Districts and Areas are linked to:
        - **Mothers**: For targeted health tips and pharmacy advertisements.
        - **Organizations**: For defining the service and ad targeting zones.
    - **Dynamic Forms**: Registration workflows include dependent dropdowns and "Quick Add" modals for new locations.

    ### 6. Email Invitation System
    - **Secure Onboarding**: Admins can invite new staff via email.
    - **Token-Based Registration**: Invitations include a unique token that expires after 7 days.
    - **Pre-Approval**: Users registering via an invite link are automatically linked to the organization, assigned their intended role, and marked as `verified`.

    ### 7. Member Approval Workflow
    - **Manual Verification**: Users joining an organization manually (without an invite) must be approved by the Org Admin or System Admin.
    - **Pending State**: Unapproved members are restricted to the `waiting-approval` screen.
    - **Actionable UI**: Admins can approve or decline pending members directly from the User Management dashboard.

    ### 8. Dashboard & Analytics
    - **Role-Based Views**: Dynamic dashboards providing the most relevant KPIs for each user role.
    - **SMS Analytics**: Real-time tracking of delivery success rates and volume for both Health Tips and Pharmacy Ads.

    ## Technical Details

    ### Models
    - **User**: Core identity model with role-based helper methods and location tracking.
    - **District / Area**: Geographic models for structured location data.
    - **History**: Pregnancy progress tracking and week/day calculation logic.
    - **Organization**: Tenant model (Clinics/Pharmacies) with location and owner data.
    - **Tip**: Content model with approval status tracking.
    - **PharmacyAd**: Monetization model for targeted product messages.
    - **Invitation**: Secure onboarding model for token-based invites.
    - **AdHistory**: Delivery log for pharmacy advertisements.
    - **MessageHistory**: Audit log for health tip communication attempts.

    ### Core Services
    - **SmsService**: Centralized service for Africa's Talking integration, supporting multiple history models.
    ...
### External Integrations
- **Africa's Talking**: Primary SMS gateway.
- **Maatwebsite Excel**: Bulk data import/export.
- **Hashids**: URL ID obfuscation for security.
