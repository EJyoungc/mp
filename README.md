# MaaSMS - Maternal Health Monitoring System

MaaSMS is an innovative platform designed to empower mothers with vital health insights and pregnancy tips delivered via SMS. Built on a robust multi-tenant architecture, it allows health institutions to manage their staff and patients independently while leveraging shared knowledge through a global template system.

## Key Features

- **Multi-Tenancy**: Complete isolation of data between different health organizations.
- **Hierarchical Role System**:
  - **Super Admin**: System-wide management, organization onboarding, and template moderation.
  - **Org Admin**: Management of users (Doctors, Practitioners, Mothers) within their institution.
  - **Pharmacy Admin**: Specialized role with restricted access to maternal data, focused on ad outreach.
  - **Doctor**: Clinical oversight, mother management, and approval of health tips.
  - **Practitioner**: Registration of mothers and creation of tailored health tips.
  - **Mother**: Automated SMS delivery of pregnancy tips based on their specific journey.
- **Secure Onboarding**: Email invitation system with token-based registration and pre-approval.
- **Hierarchical Geolocation**: Malawi-specific District and Area management for all users and organizations.
- **Member Verification**: Robust approval workflow for new organization members.
- **Monetization Engine**: Automated, area-based pharmacy advertisements targeted by geography and pregnancy stage.
- **Ad Delivery Tracking**: Comprehensive delivery logging with manual resend capabilities and API response inspection.
- **Tip Management Workflow**: Tiered approval process (Practitioner -> Doctor) ensuring clinical accuracy.
- **Template Sharing**: Cross-organization knowledge sharing facilitated by Super Admins.
- **Scalable SMS Delivery**: Integrated with Africa's Talking Gateway using Laravel Queues for high-volume delivery.

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Livewire 3, TailwindCSS, Bootstrap 4 (AdminLTE)
- **Database**: SQLite (Default)
- **SMS Gateway**: Africa's Talking
- **Job Processing**: Redis/Database Queues

## Getting Started

### Prerequisites
- PHP 8.3+
- Composer
- Node.js & NPM

### Installation
1. Clone the repository.
2. Install dependencies: `composer install && npm install`
3. Copy `.env.example` to `.env` and configure your Africa's Talking credentials.
4. Run migrations: `php artisan migrate --seed`
5. Start the queue worker: `php artisan queue:work`
6. Run the dev server: `php artisan serve`

## Documentation
- [Multi-Tenancy & RBAC Guide](docs/multi-tenancy.md)
- [SMS Service Configuration](docs/sms-service.md)

## License
Proprietary for MicroMek / Techlink360.
