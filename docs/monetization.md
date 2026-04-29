# Monetization Module: Pharmacy Advertisements

## Overview
The Pharmacy Advertisements module is designed to help the MaaSMS platform generate revenue by sending targeted product ads (e.g., vitamins, supplements, pharmacy products) to mothers via SMS.

## How it Works
1.  **Pharmacy Self-Management**: Organizations marked as `is_pharmacy` can create and manage their own advertisements directly from their dashboard. These organizations are restricted from adding or managing mother records.
2.  **Smart Targeting**:
    *   **Trimester Targeting**: Ads can target specific trimesters (1, 2, or 3) for a broader reach across a pregnancy stage.
    *   **Week Targeting**: Ads can target specific pregnancy week ranges for more granular delivery.
    *   **Geographic Area Matching**: In addition to radius-based proximity, ads are matched to mothers residing in the same administrative **Area** (managed via the hierarchical geolocation system).
3.  **Delivery Engine**: The `ads:send` console command runs periodically to match active ads with eligible mothers and dispatch SMS messages using the `SendSmsAdJob`.
4.  **Delivery Tracking & History**:
    *   **Ad History Logs**: Every sent advertisement is logged in the `ad_histories` table, capturing the specific message, timestamp, and delivery status.
    *   **API Visibility**: Detailed responses from Africa's Talking are stored for each attempt, allowing admins to debug delivery failures.
    *   **Manual Resend**: Admins can re-trigger delivery for failed or missed messages directly from the Ad History dashboard.
    *   **Analytics**: Real-time tracking of `total_sent` and recent reaches on the dashboard.

## Model Details
- **Ad Configuration**: `App\Models\PharmacyAd`
- **Delivery Log**: `App\Models\AdHistory`
- **Geographic Scope**: `App\Models\District` / `App\Models\Area`

## Admin Interface
- **Ad Management**: Dedicated interface for Super Admins and Pharmacy Admins to manage campaigns.
- **Ad History Dashboard**: A specialized Livewire page for monitoring deliveries, viewing API responses, and resending messages.

## Compliance & Privacy
To ensure a positive user experience, it is recommended to:
- Limit the frequency of ads (e.g., no more than one per week).
- Provide a clear opt-out mechanism in the SMS if required by local regulations.
