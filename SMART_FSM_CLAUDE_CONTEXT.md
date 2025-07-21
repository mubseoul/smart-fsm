# Smart FSM - Claude AI Context File

## Project Identity

-   **Name**: Smart FSM (Field Service Management System)
-   **Type**: Laravel 9 SaaS Application
-   **Purpose**: Multi-tenant field service management platform for businesses
-   **Architecture**: MVC with role-based multi-tenancy

## When AI Should Use This Context

Use this context when:

-   Modifying existing Smart FSM features
-   Adding new functionality to the system
-   Debugging issues in the codebase
-   Understanding the business logic and workflows
-   Implementing integrations or customizations

## User Roles & Hierarchy

### 5 Main User Roles

1. **Super Admin** (`super admin`)

    - **Hierarchy**: Top level (`parent_id: 0`)
    - **Purpose**: Platform administrator with full system access
    - **Key Permissions**: Manage all organizations, system settings, subscriptions, can impersonate users
    - **Default Login**: superadmin@gmail.com / 123456

2. **Owner** (`owner`)

    - **Hierarchy**: Organization level (`parent_id: super_admin_id`)
    - **Purpose**: Organization owner/administrator
    - **Key Permissions**: Full organizational access, user/role management, business operations, subscription management
    - **Default Login**: owner@gmail.com / 123456
    - **Registration**: New registrations automatically get this role

3. **Manager** (`manager`)

    - **Hierarchy**: Organization employee (`parent_id: owner_id`)
    - **Purpose**: Operations manager within organization
    - **Key Permissions**: Client management, work orders, assets, invoices, estimations, service appointments
    - **Default Login**: manager@gmail.com / 123456

4. **Client** (`client`)

    - **Hierarchy**: Organization customer (`parent_id: owner_id`)
    - **Purpose**: Customer/client of the organization
    - **Key Permissions**: Contact management, notes, view own work orders/invoices, submit requests

5. **Custom Staff Roles**
    - **Examples**: Technician, Field Worker, Support Staff
    - **Creation**: Can be created by owners with custom permissions
    - **Hierarchy**: Organization level (`parent_id: owner_id`)

### Role Hierarchy Structure

```
Super Admin (parent_id: 0)
└── Owner (parent_id: super_admin_id)
    ├── Manager (parent_id: owner_id)
    ├── Staff/Custom Roles (parent_id: owner_id)
    └── Client (parent_id: owner_id)
```

### Role Management

-   **Permission System**: Uses Spatie Laravel Permission package
-   **Multi-Tenant**: Each organization has isolated roles/users via `parent_id`
-   **Default Creation**: Roles created via `DefaultDataUsersTableSeeder`
-   **Excluded Types**: `tenant`, `maintainer` are system-reserved

## Database Structure Overview

### Core Tables (32 total)

#### User Management

-   **users**: Central user table with hierarchical structure (`parent_id`)
    -   Fields: id, name, email, type, profile, phone_number, lang, subscription, subscription_expire_date, parent_id, email_verified_at, email_verification_token, password, twofa_secret, is_active
-   **roles**: Spatie roles with `parent_id` for multi-tenancy
-   **permissions**: Spatie permissions system
-   **model_has_roles**: User-role assignments
-   **model_has_permissions**: User-permission assignments

#### Business Core

-   **client_details**: Extended client information (addresses, company info)
-   **assets**: Equipment/asset tracking with warranty and hierarchy
-   **service_parts**: Service catalog with SKU, pricing, units
-   **wo_requests**: Work order requests from clients
-   **work_orders**: Actual work orders with assignments and scheduling
-   **wo_types**: Work order categorization
-   **estimations**: Cost estimates before work orders
-   **invoices**: Billing after work completion

#### System Configuration

-   **settings**: Key-value config store (per-organization via `parent_id`)
-   **subscriptions**: SaaS subscription plans with user/feature limits
-   **notifications**: Email template system for business processes
-   **logged_histories**: User activity tracking with IP/device info

#### Content Management

-   **pages**: CMS pages (terms, privacy, etc.)
-   **faqs**: FAQ system
-   **home_pages**: Landing page content sections
-   **auth_pages**: Authentication page customization

## Registration Flow Details

### Route: `http://127.0.0.1:8000/register`

#### Process Flow:

1. **Display Form** (`RegisteredUserController::create()`)

    - Check `register_page` setting (on/off)
    - Load terms & conditions page
    - Set locale from admin user

2. **Form Submission** (`RegisteredUserController::store()`)

    - **Validation**: reCAPTCHA (optional), name, unique email, confirmed password
    - **User Creation**: type='owner', lang='english', subscription=1, parent_id=1
    - **Role Assignment**: Assign 'owner' role via Spatie
    - **Auto-Login**: Immediate authentication
    - **Default Setup**: Create email templates + client role via helpers

3. **Email Verification** (optional via `owner_email_verification` setting)
    - Generate SHA1 token, send verification email
    - If enabled: logout user, redirect to login with message
    - If disabled: set verified, send welcome email, redirect to dashboard

#### Key Settings Dependencies:

-   `register_page`: Enable/disable registration
-   `google_recaptcha`: reCAPTCHA validation
-   `owner_email_verification`: Email verification requirement
-   SMTP settings: For email sending

## Company Information Management

### How Owners Save Company Information

**Route**: `/settings` (GET) and `/settings/company` (POST)  
**Controller**: `SettingController::companyData()`  
**Permission Required**: `manage company settings`  
**View**: `resources/views/settings/index.blade.php` (Company Settings tab)

#### Company Information Fields

**Required Fields:**

-   `company_name`: Company/Organization name
-   `company_email`: Company email address
-   `company_phone`: Company phone number
-   `company_address`: Company physical address
-   `timezone`: System timezone selection

**Optional Fields:**

-   `CURRENCY_SYMBOL`: Currency symbol (default: $)
-   Number prefixes for system entities:
    -   `client_number_prefix`: Client numbering (default: #CLI-000)
    -   `estimation_number_prefix`: Estimation numbering (default: #EST-000)
    -   `workorder_number_prefix`: Work order numbering (default: #WO-000)
    -   `invoice_number_prefix`: Invoice numbering (default: #INV-000)

**Format Settings:**

-   `company_date_format`: Date display format options
    -   `M j, Y` (Jan 15, 2024)
    -   `y-m-d` (24-01-15)
    -   `d-m-y` (15-01-24)
    -   `m-d-y` (01-15-24)
-   `company_time_format`: Time display format options
    -   `g:i A` (12-hour format)
    -   `H:i` (24-hour format)

#### Storage Mechanism

**Database Table**: `settings`  
**Storage Method**: Key-value pairs with multi-tenancy via `parent_id`

```php
// SettingController::companyData() method
foreach ($settings as $key => $val) {
    \DB::insert(
        'insert into settings (`value`, `name`,`parent_id`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
        [
            $val,
            $key,
            parentId(), // Current organization ID
        ]
    );
}
```

#### Usage Throughout System

**Helper Function**: `settings()` - Retrieves all settings for current organization
**Access Pattern**: `$settings = settings(); $companyName = $settings['company_name'];`

**Used In:**

-   **Email Templates**: Company info in all notification emails
-   **Invoices**: Company details on invoice headers
-   **Work Orders**: Company information on work order documents
-   **System Branding**: Company name in headers/footers
-   **Number Generation**: Prefixes for all numbered entities

#### Settings Integration

Company information integrates with other settings categories:

-   **General Settings**: App name, logos, copyright
-   **SMTP Settings**: Email configuration
-   **Payment Settings**: Currency and payment gateways
-   **Theme Settings**: UI customization

#### Multi-Tenant Isolation

-   Each organization (`parent_id`) has independent company settings
-   Settings are automatically filtered by `parentId()` helper
-   No cross-organization data leakage
-   Default values provided via `settingsKeys()` helper

#### Validation Rules

```php
$validator = \Validator::make($request->all(), [
    'company_name' => 'required',
    'company_email' => 'required',
    'company_phone' => 'required',
    'company_address' => 'required',
    'timezone' => 'required',
]);
```

#### File Upload Handling (Logos)

**Owner-specific logos** (handled in `generalData()` method):

-   Company logo: `{parent_id}_logo.png`
-   Favicon: `{parent_id}_favicon.png`
-   Light logo: `{parent_id}_light_logo.png`
-   Storage path: `storage/upload/logo/`

This system ensures each organization can maintain their unique branding and company information while sharing the same application infrastructure.

## SaaS Subscription System

### Overview

Smart FSM operates as a **multi-tenant SaaS platform** with a comprehensive subscription system that controls access, features, and user limits per organization.

### Subscription Structure

**Database Table**: `subscriptions`

**Core Fields:**

-   `title`: Subscription plan name (e.g., "Basic", "Pro", "Enterprise")
-   `package_amount`: Price in system currency (float)
-   `interval`: Billing cycle options
    -   `Monthly`: 1-month subscriptions
    -   `Quarterly`: 3-month subscriptions
    -   `Yearly`: 12-month subscriptions
    -   `Unlimited`: No expiration
-   `user_limit`: Maximum users per organization (0 = unlimited)
-   `client_limit`: Maximum clients per organization (0 = unlimited)
-   `enabled_logged_history`: Enable/disable user activity logging (0/1)

### User Subscription Management

**User Table Fields:**

-   `subscription`: Subscription ID (foreign key)
-   `subscription_expire_date`: Expiration date (auto-calculated)

**Default Assignment:**

-   New registrations get subscription ID: 1 (Basic plan)
-   1-month trial period automatically set

### Payment System

#### Supported Payment Methods

1. **Stripe** (`STRIPE_PAYMENT = 'on'`)

    - Credit/debit card processing
    - Real-time payment verification
    - Automatic subscription activation

2. **PayPal** (`paypal_payment = 'on'`)

    - PayPal account or card payments
    - Sandbox/Live mode support
    - OAuth integration

3. **Flutterwave** (`flutterwave_payment = 'on'`)

    - African payment gateway
    - Multiple payment methods
    - API-based verification

4. **Bank Transfer** (`bank_transfer_payment = 'on'`)

    - Manual payment with receipt upload
    - Admin approval required
    - Pending → Success/Reject workflow

5. **Manual Assignment**
    - Super Admin can assign any subscription
    - Bypass payment process
    - Immediate activation

#### Payment Processing Flow

```php
// Core payment flow
1. User selects subscription plan
2. Apply coupon (if available) → Coupon::couponApply()
3. Process payment via chosen gateway
4. Create PackageTransaction record
5. Call assignSubscription() helper
6. Update user limits and expiry date
7. Activate/deactivate users based on limits
```

### Subscription Assignment (`assignSubscription()` Helper)

**Key Function**: `app/Helper/helper.php::assignSubscription($id)`

**Process:**

1. **Find Subscription**: Retrieve subscription by ID
2. **Update User**: Set subscription ID and calculate expiry date
3. **Set Expiry Date**:
    - Monthly: +1 month
    - Quarterly: +3 months
    - Yearly: +1 year
    - Unlimited: No expiry
4. **Enforce User Limits**:
    - Get all organization users (excluding super admin/owner)
    - If `user_limit = 0`: Activate all users
    - If `user_limit > 0`: Activate users up to limit, deactivate excess

### Coupon System

**Database Table**: `coupons`
**Features:**

-   **Discount Types**: Fixed amount or percentage
-   **Applicable Packages**: Comma-separated subscription IDs
-   **Usage Limits**: Maximum redemptions per coupon
-   **Validity Period**: Expiration date
-   **Status**: Active/Inactive

**Coupon Application**: `Coupon::couponApply($subscriptionId, $couponCode)`

-   Validates coupon existence and status
-   Checks package applicability
-   Verifies usage limits and expiry
-   Calculates discounted price

### Transaction Management

**Database Table**: `package_transactions`
**Tracked Data:**

-   User ID, Subscription ID, Amount
-   Payment method and status
-   Transaction IDs and receipts
-   Card details (last 4 digits, expiry)
-   Holder name and timestamps

**Transaction Creation**: `PackageTransaction::transactionData($paymentData)`

### Feature Control System

**Pricing Feature Toggle**: `pricing_feature` setting

-   **On**: Enforce subscription limits strictly
-   **Off**: Allow unlimited usage (bypass limits)

**Usage in Controllers:**

```php
$pricing_feature_settings = getSettingsValByIdName(1, 'pricing_feature');
if ($pricing_feature_settings == 'on') {
    // Check and enforce subscription limits
    $totalUser = $authUser->totalUser();
    $subscription = Subscription::find($authUser->subscription);
    if ($totalUser >= $subscription->user_limit && $subscription->user_limit != 0) {
        return redirect()->back()->with('error', __('Your user limit is over, please upgrade your subscription.'));
    }
}
```

### Default Subscription Setup

**Created in Seeder**: `DefaultDataUsersTableSeeder.php`

```php
$subscriptionData = [
    'title' => 'Basic',
    'package_amount' => 0,        // Free plan
    'interval' => 'Monthly',
    'user_limit' => 10,           // 10 users max
    'client_limit' => 10,         // 10 clients max
    'enabled_logged_history' => 1, // Enable logging
];
```

### Subscription Routes & Permissions

**Routes:**

-   `/subscriptions` - View available plans (owners)
-   `/subscriptions/{id}` - Purchase subscription
-   `/subscription/transaction` - Payment history
-   Payment processing routes for each gateway

**Required Permissions:**

-   `manage pricing packages` - Super admin subscription management
-   `buy pricing packages` - Owner subscription purchase
-   `manage pricing transaction` - View payment history

### Multi-Tenant Considerations

-   **Payment Settings**: Per-organization via `parent_id`
-   **Subscription Limits**: Applied per organization
-   **Transaction History**: Isolated by user/organization
-   **Feature Access**: Controlled by subscription + pricing_feature setting

### Subscription Status Display

**User Model Method**: `SubscriptionLeftDay()`

-   Shows remaining days for current subscription
-   Handles unlimited subscriptions
-   Color-coded display (green = active, red = expired)

This subscription system provides complete SaaS functionality with flexible pricing, multiple payment options, and granular access control while maintaining multi-tenant data isolation.

## Trial System Implementation

### Overview

Smart FSM includes a comprehensive **trial system** that allows admins to control trial periods for subscription packages. The system provides both **global trial settings** and **per-subscription trial configuration**.

### Database Structure

**Subscriptions Table Fields:**

-   `trial_enabled`: Integer (1 = enabled, 0 = disabled)
-   `trial_days`: Integer (1-365 days for trial period)

### Admin Control Features

#### Global Trial Settings (Super Admin)

**Location**: Settings → General Settings → Trial System Settings
**Fields:**

-   **Enable Trial System**: Global on/off toggle for trial functionality
-   **Default Trial Days**: Default trial period for new subscriptions (1-365 days)

**Settings Storage:**

```php
'trial_system_enabled' => 'on/off'     // Global trial system toggle
'default_trial_days' => '30'           // Default trial days
```

#### Per-Subscription Trial Settings

**Location**: Subscriptions → Create/Edit Package
**Fields:**

-   **Enable Trial Period**: Per-subscription trial toggle
-   **Trial Days**: Custom trial duration (1-365 days)

### Model Methods

**Subscription Model Methods:**

```php
$subscription->hasTrialEnabled()           // Check if trial is enabled
$subscription->getTrialDays()              // Get trial days (0 if disabled)
$subscription->getTrialDurationText()      // Human readable format
```

### Trial Logic Implementation

#### Registration Process

```php
// Get default subscription and calculate trial expiry
$defaultSubscription = Subscription::find(1);
$trialDays = $defaultSubscription->getTrialDays();
$subscriptionExpireDate = $trialDays > 0
    ? Carbon::now()->addDays($trialDays)
    : Carbon::now()->addMonths(1); // fallback
```

#### Subscription Assignment

```php
// assignSubscription() helper function logic
if ($subscription->hasTrialEnabled()) {
    $user->subscription_expire_date = Carbon::now()->addDays($subscription->trial_days);
} else {
    // Use regular interval (Monthly/Quarterly/Yearly/Unlimited)
}
```

### User Interface Features

#### Subscription Display

-   **Pricing Table**: Shows trial badges and duration
-   **Package Cards**: Display "X days Free Trial" labels
-   **Features List**: Includes "Trial Period" as a feature comparison

#### Admin Management

-   **Create/Edit Forms**: Trial settings with JavaScript validation
-   **Settings Page**: Global trial system configuration
-   **Validation**: Trial days required when trial enabled (1-365 days)

### Key Benefits

1. **Flexible Trial Management**: Admins can enable/disable trials per subscription
2. **Custom Trial Periods**: Different trial durations for different packages
3. **Global Control**: System-wide trial settings for consistency
4. **User Experience**: Clear trial indicators throughout the interface
5. **Automatic Calculation**: Trial expiry dates calculated automatically

### Integration Points

**Registration Controller**: Uses trial settings for new user expiry dates
**Helper Functions**: `assignSubscription()` and `assignManuallySubscription()` handle trial logic
**Subscription Views**: Display trial information in pricing tables
**Settings Management**: Global trial configuration in admin settings

This trial system provides complete flexibility for SaaS trial management while maintaining the existing subscription structure and multi-tenant isolation.

## Critical System Concepts

### Multi-Tenancy Hierarchy

```
Super Admin (parent_id: 0)
└── Owner (parent_id: super_admin_id)
    ├── Manager (parent_id: owner_id)
    ├── Staff (parent_id: owner_id)
    └── Client (parent_id: owner_id)
```

### Core Business Flow

```
Client Request → WO Request → Estimation → Work Order → Service Appointment → Invoice → Payment
```

### Key Models & Relationships

-   **User**: Central to multi-tenancy, has roles and permissions
-   **WorkOrder**: Core business entity, links to clients, assets, services
-   **Asset**: Equipment/property being serviced
-   **ServicePart**: Services and parts catalog with pricing
-   **Subscription**: SaaS billing and user limits
-   **PackageTransaction**: Payment processing and history

## Essential Functions & Helpers

### Must-Use Helper Functions

-   `parentId()`: Returns current organization ID - USE EVERYWHERE for data filtering
-   `settings()`: System configuration - use for app behavior
-   `workOrderPrefix()`: Generate proper work order numbers
-   `assignSubscription($id)`: Handle subscription changes and user limits

### Permission Patterns

Always check permissions before operations:

```php
if (\Auth::user()->can('manage work order')) {
    // Operation code
} else {
    return redirect()->back()->with('error', __('Permission Denied.'));
}
```

### Data Filtering Pattern

Always filter by organization:

```php
WorkOrder::where('parent_id', parentId())->get();
User::where('parent_id', parentId())->where('type', 'client')->get();
```

## Key Routes & Controllers

### Primary Route Groups

-   `/users` - UserController (staff management)
-   `/client` - ClientController (customer management)
-   `/wo-request` - WORequestController (service requests)
-   `/workorder` - WorkOrderController (work assignments)
-   `/asset` - AssetController (equipment management)
-   `/services-parts` - ServicePartController (catalog management)
-   `/subscriptions` - SubscriptionController (SaaS billing)

### Authentication Routes

-   Custom 2FA implementation in OTPController
-   Email verification system
-   Role-based dashboard routing

## Database Patterns

### Standard Table Structure

All tenant data includes `parent_id` field for organization filtering.

### Key Migrations

-   `create_users_table.php`: Multi-tenant user structure
-   `create_work_orders_table.php`: Core business entity
-   `create_subscriptions_table.php`: SaaS billing structure
-   Permission tables from Spatie package

## Frontend Patterns

### Blade Template Structure

-   `layouts/admin.blade.php`: Main admin layout
-   `layouts/landing.blade.php`: Public pages layout
-   `admin/menu.blade.php`: Dynamic navigation based on permissions

### UI Components

-   Bootstrap 5 with custom CSS
-   jQuery for interactions
-   Dynamic forms with validation
-   Multi-language support via `__()` helper

## Payment System

### Supported Gateways

-   Stripe (credit cards)
-   PayPal
-   Flutterwave
-   Bank Transfer (manual approval)

### Payment Flow

1. User selects subscription
2. Payment processed via chosen gateway
3. PackageTransaction record created
4. assignSubscription() called to update limits
5. User access updated based on subscription

## Security Considerations

### XSS Protection

All routes use XSS middleware - maintain this pattern.

### File Uploads

-   Store in `storage/upload/` with proper validation
-   Generate unique filenames
-   Validate file types and sizes

### Role-Based Access

-   Every controller method should check permissions
-   Use Gates for complex permission logic
-   Respect parent_id hierarchy in all queries

## Language & Localization System

### Supported Languages (13 Languages)

Smart FSM provides comprehensive **multi-language support** with **13 fully translated languages**:

1. **English** (Default) - `english`
2. **Arabic** - `arabic` (RTL support)
3. **Spanish** - `spanish`
4. **French** - `french`
5. **German** - `german`
6. **Italian** - `italian`
7. **Dutch** - `dutch`
8. **Japanese** - `japanese`
9. **Polish** - `polish`
10. **Portuguese** - `portuguese`
11. **Russian** - `russian`
12. **Danish** - `danish`

### Language System Architecture

#### File Structure

```
resources/lang/
├── english.json (706 translation keys)
├── arabic.json (705 translation keys)
├── spanish.json (705 translation keys)
├── french.json (705 translation keys)
├── german.json (705 translation keys)
├── italian.json (705 translation keys)
├── dutch.json (705 translation keys)
├── japanese.json (705 translation keys)
├── polish.json (705 translation keys)
├── portuguese.json (705 translation keys)
├── russian.json (705 translation keys)
├── danish.json (705 translation keys)
├── english/ (directory with installer translations)
├── arabic/ (directory with installer translations)
└── [other language directories]
```

#### Translation Pattern

```php
{{ __('Text to translate') }}
```

#### User Language Management

-   **User Field**: `users.lang` stores user's preferred language
-   **Default**: New users get 'english' as default language
-   **Switching**: Users can change language via header dropdown
-   **Route**: `GET /language/{lang}` - `SettingController::languageChange()`
-   **Persistence**: Language preference saved to user profile

### Language Features

#### RTL (Right-to-Left) Support

-   **Arabic Language**: Full RTL layout support
-   **CSS Classes**: Automatic RTL styling
-   **JavaScript**: `layout_rtl_change()` function handles RTL switching
-   **HTML Attributes**: `dir="rtl"` and `lang="ar"` set automatically

#### Dynamic Language Loading

```php
// Set application locale based on user preference
\App::setLocale(\Auth::user()->lang);

// Guest users get super admin's language
$user = \App\Models\User::find(1);
\App::setLocale($user->lang);
```

#### Language Detection

```php
// Custom::languages() method scans language directories
public static function languages()
{
    $directory = base_path() . '/resources/lang/';
    $allFiles = glob($directory . "*", GLOB_ONLYDIR);
    // Returns array of available language codes
}
```

### User Interface Integration

#### Header Language Selector

-   **Location**: Top navigation bar (language icon)
-   **Display**: Dropdown with all available languages
-   **Active State**: Current language highlighted
-   **Route**: `/language/{lang}` for switching

#### Language Context Setting

-   **Authentication**: Language set on login (`XSS` middleware)
-   **Registration**: Language set for registration pages
-   **Guest Access**: Uses super admin's language setting
-   **Persistence**: Language preference maintained across sessions

### Translation Coverage

#### Comprehensive Translation Areas

-   **Authentication**: Login, registration, password reset
-   **User Management**: User creation, roles, permissions
-   **Business Operations**: Work orders, assets, invoices
-   **System Settings**: All configuration screens
-   **Email Templates**: All notification emails
-   **UI Elements**: Buttons, labels, messages, validation
-   **Error Messages**: Complete error handling
-   **Success Messages**: All confirmation messages

#### Translation Statistics

-   **English**: 706 translation keys (reference language)
-   **Other Languages**: 705 translation keys each
-   **Coverage**: ~99.9% translation completeness
-   **Maintenance**: Regular updates with new features

### Technical Implementation

#### Middleware Integration

```php
// XSS Middleware sets user language and timezone
if (\Auth::check()) {
    \App::setLocale(\Auth::user()->lang);
    $timezone = getSettingsValByName('timezone');
    \Config::set('app.timezone', $timezone);
}
```

#### Database Storage

-   **User Language**: Stored in `users.lang` field
-   **Default Value**: 'english' for new registrations
-   **Admin Override**: Super admin language affects guest users

#### Email Localization

-   **Template Translation**: All email templates support localization
-   **Dynamic Content**: Email content translated based on recipient's language
-   **Multi-tenant**: Each organization can have different language preferences

### Language Management Workflow

#### For Users

1. **Registration**: Gets default language ('english')
2. **Language Selection**: Choose from header dropdown
3. **Instant Switch**: Language changes immediately
4. **Persistence**: Preference saved to database

#### For Administrators

1. **System Language**: Super admin language affects guest users
2. **User Management**: Can see users' language preferences
3. **Email Templates**: Translated based on recipient language
4. **Multi-tenant**: Each organization maintains language preferences

This comprehensive language system ensures **Smart FSM can serve global markets** with native language support for diverse user bases across different regions and cultures.

## Common Development Tasks

### Adding New Work Order Status

1. Update `WorkOrder::$status` array
2. Add language translations
3. Update status change logic in controller
4. Update UI status displays

### Adding New Permission

1. Add to permissions array in DefaultDataUsersTableSeeder
2. Add to role permissions in seeder
3. Use in controller with `\Auth::user()->can()`
4. Add to navigation menu conditions

### Adding New Payment Gateway

1. Create payment method in PaymentController
2. Add settings configuration
3. Update subscription views
4. Handle webhooks if needed

## Testing Considerations

### Default Users (from seeder)

-   Super Admin: superadmin@gmail.com / 123456
-   Owner: owner@gmail.com / 123456
-   Manager: manager@gmail.com / 123456

### Test Data

-   Default subscription plan created
-   Sample permissions and roles
-   Basic system settings

## Performance Notes

### Optimization Points

-   Always use parent_id filtering to limit data scope
-   Implement proper indexing on parent_id columns
-   Use eager loading for relationships
-   Cache system settings where appropriate

## Error Handling Patterns

### Standard Error Response

```php
return redirect()->back()->with('error', __('Error message'));
```

### Success Response

```php
return redirect()->route('route.name')->with('success', __('Success message'));
```

## Integration Points

### Email System

-   SMTP configuration in settings
-   Template-based notifications
-   Email verification system

### File Storage

-   Local storage by default
-   Configurable for cloud storage
-   Proper file organization in upload directories

This context file should be referenced whenever working with the Smart FSM codebase to ensure consistency with existing patterns and proper understanding of the system architecture.
