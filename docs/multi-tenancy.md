# Multi-Tenancy & Role-Based Access Control (RBAC)

## Architecture Overview

MaaSMS uses a "Single Database, Shared Schema" multi-tenant approach where **Organizations act as Teams**. Data isolation is achieved by associating records with an `organization_id`.

### The Team Lifecycle
1. **Creation**: A user can register and create a new Organization. This user becomes the **Owner**.
2. **Joining**: Users can register and request to join an existing Organization. Their status is set to `pending`.
3. **Approval**: The Organization Owner must review and approve membership requests via the dashboard. Access to organization data is restricted until this approval is granted.

### The `BelongsToOrganization` Trait
The core of the multi-tenancy logic resides in `app/Traits/BelongsToOrganization.php`. This trait:
1.  **Automatic Scoping**: Adds a global scope to models that automatically filters queries by the authenticated user's `organization_id`.
2.  **Automatic Association**: Automatically sets the `organization_id` when a new record is created.
3.  **System Admin Bypass**: Allows users with the `system-admin` role to see data across all organizations.

**Applicable Models**: `Tip`, `History`, `MessageHistory`, `User`.

---

## Role Hierarchy & Permissions

### 1. System Admin
- **Scope**: Global (All Organizations).
- **Primary Actions**: Onboarding new organizations, managing global system settings, and moderating Global Templates.
- **Can See**: All users, all mothers, all tips.

### 2. Organization Admin
- **Scope**: Organization-Specific.
- **Primary Actions**: Managing doctors, practitioners, and mothers within their own institution. Approving organization-specific tips.
- **Can See**: All staff and mothers registered to their organization.

### 3. Doctor
- **Scope**: Organization-Specific.
- **Primary Actions**: Managing mother health records, approving/editing tips created by practitioners.
- **Can See**: All mothers in their organization.

### 4. Practitioner
- **Scope**: Organization-Specific.
- **Primary Actions**: Registering mothers, creating health tips (require approval).
- **Can See**: Mothers they have registered or those assigned to their organization.

### 5. Mother
- **Scope**: Self-Specific.
- **Primary Actions**: Viewing their own pregnancy journey.
- **Can See**: Only their own records and messages.

---

## Tip Management Workflow

To ensure clinical accuracy and institutional quality, tips follow a strict lifecycle:

1.  **Creation**: A Practitioner creates a tip. It is automatically saved with `status = 'pending_approval'`.
2.  **Review**: A Doctor or Org Admin reviews the tip in the "Tips" section of the specific week view.
3.  **Approval**: The authorized user clicks "Approve". The tip's status changes to `approved` and the `approved_by` field is updated.
4.  **Delivery**: The `app:check-messages` command only dispatches tips with the `approved` status.

---

## Template Sharing

MaaSMS allows organizations to learn from each other through "Global Templates":

- **Marking as Template**: Only a **System Admin** can mark an existing organizational tip as a `Global Template`.
- **Using Templates**: When viewing a specific week, users will see a "Global Templates" section. Clicking "Use as Template" will copy the content to their own organization's scope as a new draft, allowing them to customize it for their patients.
