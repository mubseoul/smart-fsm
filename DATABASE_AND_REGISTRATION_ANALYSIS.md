# Database Structure & Registration Flow Analysis

## Overview

This is a **Field Service Management (FSM)** Laravel application that provides comprehensive business management capabilities including user management, asset tracking, work order management, invoicing, and subscription-based access control.

## Database Structure

### Core User Management

-   **users**: Central user table with hierarchical structure (`parent_id`)

    -   Supports multiple user types: `super admin`, `owner`, `client`, etc.
    -   Email verification system with tokens
    -   2FA support (`twofa_secret`)
    -   Subscription-based access control
    -   Multi-language support

-   **roles & permissions**: Spatie permission system
    -   Role-based access control (RBAC)
    -   Hierarchical roles with `parent_id`
    -   Custom permissions per parent organization

### Business Core Tables

#### Asset Management

-   **assets**: Equipment/asset tracking
    -   Asset numbers, GIAI codes
    -   Installation/purchase dates
    -   Warranty tracking
    -   Hierarchical asset structure

#### Service Management

-   **service_parts**: Service components and parts catalog
    -   SKU management
    -   Pricing and units
    -   Service vs Parts classification

#### Work Order System

-   **wo_requests**: Work order requests from clients
-   **work_orders**: Actual work orders with assignments
-   **wo_types**: Work order categorization
-   **estimations**: Cost estimates before work
-   **invoices**: Billing after work completion

#### Client Management

-   **client_details**: Extended client information
    -   Service and billing addresses
    -   Company information
    -   Linked to users table

### System Configuration

-   **settings**: Key-value configuration store
    -   Per-organization settings (`parent_id`)
    -   SMTP, payment, reCAPTCHA, etc.
-   **subscriptions**: Subscription plans
    -   User/client limits
    -   Feature toggles
    -   Pricing tiers

### Content Management

-   **pages**: CMS pages (terms, privacy, etc.)
-   **faqs**: FAQ system
-   **home_pages**: Landing page content
-   **auth_pages**: Authentication page customization
-   **notifications**: Email template system

### Audit & Logging

-   **logged_histories**: User activity tracking
    -   IP addresses, browser info
    -   Geographic data
    -   Device information

## Registration Flow (`http://127.0.0.1:8000/register`)

### Route Definition

```php
// routes/auth.php
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);
```

### Registration Process

#### 1. Display Registration Form (`RegisteredUserController::create()`)

-   Checks if registration is enabled via settings (`register_page = 'on'`)
-   If disabled, redirects to login
-   Loads terms & conditions page for display
-   Sets application locale based on admin user language

#### 2. Form Submission (`RegisteredUserController::store()`)

**Validation Steps:**

1. **reCAPTCHA Validation** (if enabled)

    - Checks `google_recaptcha` setting
    - Validates `g-recaptcha-response` field

2. **Form Data Validation:**
    ```php
    'name' => 'required|string|max:255'
    'email' => 'required|string|email|max:255|unique:users'
    'password' => 'required|confirmed|Rules\Password::defaults()'
    ```

**User Creation Process:**

1. **Create User Record:**

    ```php
    $userData = [
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'type' => 'owner',                    // New registrations are 'owner' type
        'lang' => 'english',                  // Default language
        'subscription' => 1,                  // Default subscription plan
        'subscription_expire_date' => Carbon::now()->addMonths(1),
        'parent_id' => 1,                     // Links to super admin
    ];
    ```

2. **Role Assignment:**

    - Assigns `owner` role using Spatie permissions
    - Owners can manage their own organization

3. **Auto-Login:**

    - Automatically logs in the new user
    - Uses Laravel's `Auth::login()`

4. **Default Setup:**
    - **`defaultTemplate($owner->id)`**: Creates email notification templates
        - user_create, client_create, wo_request_create, etc.
    - **`defaultClientCreate($owner->id)`**: Creates default client role with permissions
        - Contact management permissions
        - Note management permissions

#### 3. Email Verification Flow

**If Email Verification Enabled (`owner_email_verification = 'on'`):**

1. Generate SHA1 token from email
2. Save token to `email_verification_token` field
3. Send verification email with link: `route('email-verification', $token)`
4. Logout user immediately
5. Redirect to login with verification message

**If Email Verification Disabled:**

1. Set `email_verified_at = now()`
2. Clear `email_verification_token`
3. Send welcome email
4. Redirect to dashboard

#### 4. Email Verification Process

-   User clicks link in email: `/email-verification/{token}`
-   System finds user by token
-   Sets `email_verified_at = now()`
-   Clears verification token
-   Redirects to login with success message

### Key Features

#### Multi-Tenancy Support

-   `parent_id` field creates organizational hierarchy
-   Settings are per-organization
-   Users belong to organizations
-   Data isolation by `parent_id`

#### Subscription System

-   New users get default subscription (ID: 1)
-   1-month trial period
-   User/client limits enforced
-   Feature toggles based on subscription

#### Security Features

-   Email verification (optional)
-   reCAPTCHA protection (optional)
-   Password confirmation required
-   Unique email validation
-   2FA support (twofa_secret field)

#### Automatic Setup

-   Email templates for all business processes
-   Default client role with appropriate permissions
-   Welcome email with credentials
-   Immediate dashboard access (if no email verification)

### Error Handling

-   **Email sending failure**: Deletes user and returns error
-   **Validation errors**: Returns to form with messages
-   **reCAPTCHA failure**: Blocks registration
-   **Database errors**: Proper Laravel error handling

### Settings Dependencies

The registration flow depends on these settings:

-   `register_page`: Enable/disable registration
-   `google_recaptcha`: Enable reCAPTCHA validation
-   `owner_email_verification`: Require email verification
-   SMTP settings: For sending verification emails

This creates a complete business management platform with proper user onboarding, security, and multi-tenant architecture suitable for field service management operations.
