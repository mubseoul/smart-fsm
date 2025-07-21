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

## Language & Localization

### Supported Languages

English, Arabic, Spanish, French, German, Italian, Dutch, Japanese, Polish, Portuguese, Russian, Danish

### Translation Pattern

```php
{{ __('Text to translate') }}
```

### RTL Support

System supports RTL languages with proper CSS handling.

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
