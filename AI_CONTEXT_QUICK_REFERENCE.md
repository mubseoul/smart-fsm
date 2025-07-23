# Smart FSM - AI Quick Reference

> **For AI Assistants**: This file contains the most critical information about Smart FSM for quick context loading.

## 🏗️ System Architecture

-   **Type**: Laravel 9 SaaS Multi-Tenant Field Service Management
-   **Multi-Tenancy**: Hierarchical via `parent_id` field
-   **Permissions**: Spatie Laravel Permission package
-   **Database**: 32 tables, MySQL/PostgreSQL

## 👥 User Roles (5 Main Types)

| Role            | Type          | Hierarchy                   | Key Purpose                |
| --------------- | ------------- | --------------------------- | -------------------------- |
| **Super Admin** | `super admin` | `parent_id: 0`              | Platform administrator     |
| **Owner**       | `owner`       | `parent_id: super_admin_id` | Organization administrator |
| **Manager**     | `manager`     | `parent_id: owner_id`       | Operations manager         |
| **Client**      | `client`      | `parent_id: owner_id`       | Customer/client            |
| **Custom**      | Various       | `parent_id: owner_id`       | Technician, Staff, etc.    |

## 🏢 Company Information Management

**Route**: `/settings` → Company Settings tab  
**Controller**: `SettingController::companyData()`  
**Permission**: `manage company settings`

### Required Fields

-   `company_name`, `company_email`, `company_phone`, `company_address`, `timezone`

### Optional Fields

-   `CURRENCY_SYMBOL`, number prefixes, date/time formats

### Storage Pattern

```php
// All stored in settings table with parent_id
foreach ($settings as $key => $val) {
    DB::insert('insert into settings (value, name, parent_id) values (?, ?, ?)
               ON DUPLICATE KEY UPDATE value = VALUES(value)',
               [$val, $key, parentId()]);
}
```

### Usage

```php
$settings = settings();  // Get all org settings
$companyName = $settings['company_name'];
```

**Used in**: Email templates, invoices, work orders, system branding

## 🎁 Trial System

**Database Fields**: `trial_enabled`, `trial_days` in subscriptions table

### Global Settings (Super Admin)

-   **Settings → General → Trial System Settings**
-   `trial_system_enabled`: on/off toggle
-   `default_trial_days`: 1-365 days

### Per-Subscription Settings

-   **Trial Enabled**: Per-package toggle
-   **Trial Days**: Custom duration (1-365)

### Key Methods

```php
$subscription->hasTrialEnabled()      // Check if trial enabled
$subscription->getTrialDays()         // Get trial days (0 if disabled)
$subscription->getTrialDurationText() // "30 days" or "No Trial"
```

### Trial Logic

-   **Registration**: Uses subscription trial settings for expiry date
-   **Assignment**: `assignSubscription()` checks trial first, then intervals
-   **Display**: Shows trial badges in pricing tables

## 🌐 Language Support (13 Languages)

**Supported Languages:**

-   English (default), Arabic (RTL), Spanish, French, German, Italian
-   Dutch, Japanese, Polish, Portuguese, Russian, Danish

**Key Features:**

-   **706 translation keys** in English (reference)
-   **705 translation keys** per language (~99.9% coverage)
-   **RTL support** for Arabic with automatic CSS handling
-   **User preference** stored in `users.lang` field
-   **Dynamic switching** via header dropdown

**Implementation:**

```php
\App::setLocale(\Auth::user()->lang);  // Set user language
{{ __('Text to translate') }}          // Translation pattern
Route: /language/{lang}                // Language switching
```

**Files:** `resources/lang/{language}.json` + installer directories

## 💳 SaaS Subscription System

**Core Tables**: `subscriptions`, `package_transactions`, `coupons`

### Subscription Fields

-   `title`, `package_amount`, `interval` (Monthly/Quarterly/Yearly/Unlimited)
-   `user_limit`, `client_limit`, `enabled_logged_history`

### Payment Methods

-   **Stripe**: Credit/debit cards (real-time)
-   **PayPal**: PayPal accounts + cards (OAuth)
-   **Flutterwave**: African payment gateway
-   **Bank Transfer**: Manual with receipt upload (admin approval)
-   **Manual Assignment**: Super admin bypass

### Key Functions

```php
assignSubscription($id)           // Assign subscription + enforce limits
Coupon::couponApply($id, $code)  // Apply discount coupons
PackageTransaction::transactionData($data) // Record payments
```

### Subscription Flow

1. Select plan → 2. Apply coupon → 3. Process payment → 4. Create transaction → 5. Assign subscription → 6. Enforce user limits

### Limit Enforcement

-   `pricing_feature = 'on'`: Enforce limits strictly
-   `pricing_feature = 'off'`: Allow unlimited usage
-   Users beyond limit are deactivated (`is_active = 0`)

### Default Setup

-   Basic plan (ID: 1): Free, Monthly, 10 users, 10 clients
-   New registrations get Basic + 1-month trial

## 🔑 Critical Functions

```php
parentId()                    // ALWAYS use for data filtering
settings()                    // Get system configuration
defaultTemplate($id)          // Create email templates
defaultClientCreate($id)      // Create default client role
assignSubscription($id)       // Handle subscription changes
```

## 🗄️ Key Database Tables

**Core Business**: `work_orders`, `wo_requests`, `estimations`, `invoices`, `assets`, `service_parts`
**Users**: `users`, `roles`, `permissions`, `client_details`
**System**: `settings`, `subscriptions`, `notifications`, `logged_histories`

## 📋 Business Flow

```
Client Request → WO Request → Estimation → Work Order → Service Appointment → Invoice → Payment
```

## 🔐 Security Patterns

```php
// Always check permissions
if (\Auth::user()->can('manage work order')) { /* code */ }

// Always filter by organization
WorkOrder::where('parent_id', parentId())->get();

// Multi-tenant data isolation via parent_id
```

### 🔐 Registration & Authentication

-   **Theme**: "Become a Provider" (professional service provider onboarding)
-   **Flow**: 3-step registration process
    1. **Basic Info**: Name, Email, Password, Phone
    2. **Business Info**: Business Type (dropdown), Service Location, Logo, Bio
    3. **KYC**: Optional identity verification (skippable)
-   **Business Types**: 21 predefined categories (dropdown selection)
-   **Service Location**: Structured fields (Country, Zip, City, Address) - 80+ countries supported
-   **Database**: Separate tables (`business_profiles`, `kyc_documents`) linked to `users`
-   **Assets**: External CSS/JS files (`multi-step-registration.css/js`)
-   **Default Role**: "owner" assigned after successful registration
-   **Email Verification**: Optional (configurable via settings)

## ⚙️ Key Settings

-   `register_page`: Enable/disable registration
-   `google_recaptcha`: reCAPTCHA validation
-   `owner_email_verification`: Email verification
-   `pricing_feature`: Subscription limits

## 🔄 Default Logins

-   Super Admin: `superadmin@gmail.com` / `123456`
-   Owner: `owner@gmail.com` / `123456`
-   Manager: `manager@gmail.com` / `123456`

## 📁 Important Files

-   `app/Helper/helper.php`: Core utility functions
-   `database/seeders/DefaultDataUsersTableSeeder.php`: Default roles/permissions
-   `app/Http/Controllers/Auth/RegisteredUserController.php`: Registration logic
-   Routes: `routes/auth.php`, `routes/web.php`

---

**💡 Remember**: Always use `parent_id` for multi-tenant data isolation and check permissions before operations!

## After Signup, Users Get the **"Owner"** Role

Looking at the registration controller (`RegisteredUserController.php`), here's exactly what happens during signup:

### 📝 **User Data Created During Signup:**

```php
$userData = [
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'type' => 'owner',                    // ← USER TYPE SET TO 'OWNER'
    'lang' => 'english',
    'subscription' => 1,                  // Default Basic subscription
    'subscription_expire_date' => Carbon::now()->addMonths(1), // 1 month free trial
    'parent_id' => 1,                     // Under Super Admin
];
```

### 🎭 **Role Assignment:**

```php
$userRole = Role::findByName('owner');
$owner->assignRole($userRole);           // ← ASSIGNED 'OWNER' ROLE
```

### 🏗️ **What This Means:**

**New signups automatically become:**

-   **User Type**: `owner`
-   **Role**: `owner` (via Spatie Permission system)
-   **Hierarchy**: Organization administrator level
-   **Default Subscription**: Basic plan (ID: 1) with 1-month free trial
-   **Parent ID**: 1 (under Super Admin for multi-tenancy)

**Owner Capabilities:**

-   Full organizational access [[memory:3927125]]
-   Can manage users, clients, work orders, assets
-   Can configure company settings
-   Can manage subscriptions and billing
-   Can create custom roles for their organization

**Additional Setup:**

-   `defaultTemplate($owner->id)` - Creates default email templates
-   `defaultClientCreate($owner->id)` - Creates default client role for the organization

So every new signup becomes an **Organization Owner** with full administrative rights within their own tenant/organization space in the multi-tenant system.
