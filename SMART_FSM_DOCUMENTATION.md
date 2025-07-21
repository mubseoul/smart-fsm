# Smart FSM - Field Service Management System Documentation

## Project Overview

**Smart FSM** is a comprehensive Field Service Management (FSM) system built with Laravel 9. It's designed to facilitate better communication between office and field staff, leading to improved productivity, customer satisfaction, and operational efficiency. The system operates as a SaaS platform with multi-tenancy support.

## System Architecture

### Technology Stack

-   **Backend**: Laravel 9 (PHP 8.0+)
-   **Frontend**: Bootstrap 5, jQuery, Alpine.js
-   **Database**: MySQL/PostgreSQL
-   **Authentication**: Laravel Breeze with 2FA support
-   **Permissions**: Spatie Laravel Permission
-   **Payments**: Stripe, PayPal, Flutterwave, Bank Transfer
-   **Notifications**: Email notifications with customizable templates
-   **File Storage**: Laravel Storage with multiple disk support

### Multi-Tenancy Structure

The system uses a hierarchical multi-tenancy model:

-   **Super Admin** (parent_id: 0): Platform administrator
-   **Owner** (parent_id: super_admin_id): Organization owner
-   **Manager/Staff** (parent_id: owner_id): Organization employees
-   **Client** (parent_id: owner_id): Organization customers

## Core Features & Modules

### 1. User Management

-   **User Types**: Super Admin, Owner, Manager, Client
-   **Role-based Permissions**: Granular permission system using Spatie Permission
-   **User Impersonation**: Super admins can impersonate other users
-   **2FA Authentication**: Google 2FA integration
-   **Email Verification**: Configurable email verification

### 2. Business Management

#### Assets Management

-   Asset registration with detailed information
-   Asset hierarchy (parent-child relationships)
-   Asset warranty tracking
-   Asset-part associations

#### Services & Parts Management

-   Service catalog with pricing
-   Parts inventory management
-   Service tasks with duration tracking
-   SKU-based part identification

#### Work Order System

-   **WO Requests**: Initial service requests from clients
-   **Estimations**: Cost estimates with approval workflow
-   **Work Orders**: Actual work assignments
-   **Service Appointments**: Scheduling system
-   **Invoicing**: Billing integration

### 3. Work Order Workflow

```
WO Request → Estimation → Work Order → Service Appointment → Invoice
```

#### WO Request Fields:

-   Request details, client, asset, priority, due date
-   Assignment to staff members
-   Preferred scheduling (date/time/notes)
-   Status tracking (Pending, In Progress, Completed, Cancelled)

#### Work Order Features:

-   Service and part allocation
-   Task management with status tracking
-   Time tracking and duration logging
-   Priority levels (Low, Medium, High, Critical)
-   Status workflow (Pending → Approved → Completed)

### 4. Client Management

-   Client registration and profile management
-   Service history tracking
-   Asset associations
-   Communication logs

### 5. Financial Management

#### Subscription System (SaaS)

-   **Subscription Plans**: Monthly, Quarterly, Yearly, Unlimited
-   **User Limits**: Configurable user and client limits
-   **Payment Methods**: Stripe, PayPal, Flutterwave, Bank Transfer
-   **Coupon System**: Discount codes with usage limits
-   **Transaction History**: Complete payment tracking

#### Invoicing

-   Automatic invoice generation from work orders
-   PDF invoice generation
-   Payment status tracking
-   Due date management

### 6. Communication & Notifications

-   **Email Templates**: Customizable notification templates
-   **Notification System**: Real-time notifications
-   **Contact Diary**: Communication logging
-   **Notice Board**: Internal announcements

### 7. Language & Localization

#### Multi-Language Support (13 Languages)

-   **Supported Languages**: English, Arabic, Spanish, French, German, Italian, Dutch, Japanese, Polish, Portuguese, Russian, Danish
-   **Translation Files**: JSON format with 700+ translation keys
-   **RTL Support**: Full right-to-left layout for Arabic
-   **User Preferences**: Individual language selection per user
-   **Dynamic Switching**: Real-time language changes via UI

#### Localization Features

-   **User Interface**: Complete UI translation
-   **Email Templates**: Localized notification emails
-   **Error Messages**: Translated validation and error messages
-   **Date/Time Formats**: Locale-specific formatting
-   **Currency Display**: Multi-currency support with localization

### 8. Content Management System

-   **Landing Page**: Customizable homepage
-   **Custom Pages**: Dynamic page creation
-   **FAQ System**: Knowledge base
-   **Multi-language Support**: 13+ languages supported

## Database Schema Overview

### Key Tables:

-   `users`: User management with multi-tenancy
-   `work_orders`: Core work order data
-   `w_o_requests`: Service requests
-   `estimations`: Cost estimates
-   `assets`: Equipment/asset registry
-   `service_parts`: Services and parts catalog
-   `invoices`: Billing information
-   `subscriptions`: SaaS subscription plans
-   `package_transactions`: Payment history

### Important Relationships:

-   Work Orders → Clients (Users)
-   Work Orders → Assets
-   Work Orders → Service Parts
-   Work Orders → Service Tasks
-   Work Orders → Service Appointments
-   Users → Subscriptions
-   Organizations (parent_id hierarchy)

## Key Helper Functions

### Core System Functions (app/Helper/helper.php)

-   `parentId()`: Get current user's organization ID
-   `settings()`: Retrieve system settings
-   `assignSubscription()`: Handle subscription assignments
-   `workOrderPrefix()`: Generate work order numbers
-   `dateFormat()`: Consistent date formatting

### Authentication & Permissions

-   Role-based access control throughout the system
-   Permission gates for all major operations
-   Multi-level hierarchy enforcement

## Configuration & Settings

### System Settings Categories:

1. **General**: App name, logos, company details
2. **Email**: SMTP configuration, templates
3. **Payment**: Payment gateway settings
4. **Theme**: UI customization options
5. **Security**: 2FA, captcha settings
6. **Localization**: Language and timezone settings

### Environment Variables:

```env
APP_NAME="Smart FSM"
DB_CONNECTION=mysql
MAIL_MAILER=smtp
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
PAYPAL_CLIENT_ID=your_paypal_client_id
```

## API Structure

### Route Groups:

-   **Authentication**: Login, register, 2FA
-   **Users**: User management (CRUD)
-   **Work Orders**: Complete work order lifecycle
-   **Assets**: Asset management
-   **Clients**: Client relationship management
-   **Subscriptions**: SaaS billing and payments
-   **Settings**: System configuration

## Development Guidelines

### Code Structure:

-   **Controllers**: Handle HTTP requests and business logic
-   **Models**: Eloquent models with relationships
-   **Views**: Blade templates with Bootstrap 5
-   **Helpers**: Utility functions in helper.php
-   **Middleware**: Authentication, XSS protection

### Security Features:

-   XSS protection middleware
-   CSRF protection
-   Role-based authorization
-   Input validation and sanitization
-   Secure file uploads

### Multi-language Support:

-   JSON-based language files
-   RTL/LTR support
-   13+ supported languages including English, Arabic, Spanish, French, German, etc.

## Deployment Considerations

### Requirements:

-   PHP 8.0+
-   MySQL/PostgreSQL
-   Composer
-   Node.js (for asset compilation)
-   Web server (Apache/Nginx)

### Storage:

-   File uploads stored in storage/upload/
-   Logo and branding assets
-   Payment receipts
-   User profiles

## Common Tasks for AI Assistants

### Adding New Features:

1. Create migration for database changes
2. Create/update Eloquent models with relationships
3. Create controller with CRUD operations
4. Add routes with proper middleware
5. Create Blade views with consistent styling
6. Add permissions to seeder
7. Update navigation menu
8. Add language translations

### Modifying Work Order System:

-   Work orders are central to the system
-   Always maintain the workflow: Request → Estimation → Work Order → Appointment → Invoice
-   Ensure proper parent_id filtering for multi-tenancy
-   Update related service parts and tasks

### Payment Integration:

-   All payments go through PackageTransaction model
-   Support multiple payment gateways
-   Handle subscription renewals and user limits
-   Maintain coupon system integration

### User Management:

-   Respect the hierarchical structure (parent_id)
-   Enforce subscription limits
-   Maintain role-based permissions
-   Handle user activation/deactivation based on subscription

This documentation provides a comprehensive overview of the Smart FSM system. When working with this codebase, always consider the multi-tenant nature, maintain the work order workflow, and respect the permission system.
