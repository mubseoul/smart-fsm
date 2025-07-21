# Smart FSM - Field Service Management System

<div align="center">

![Smart FSM Logo](https://img.shields.io/badge/Smart%20FSM-Field%20Service%20Management-blue?style=for-the-badge)

[![Laravel](https://img.shields.io/badge/Laravel-9.x-red?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.0+-blue?style=flat-square&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)
[![GitHub Stars](https://img.shields.io/github/stars/mubseoul/smart-fsm?style=flat-square)](https://github.com/mubseoul/smart-fsm/stargazers)

**A comprehensive SaaS Field Service Management platform built with Laravel 9**

[🌟 Features](#-features) • [🚀 Installation](#-installation) • [📖 Documentation](#-documentation) • [🔧 Usage](#-usage) • [🤝 Contributing](#-contributing)

</div>

---

## 📋 Overview

Smart FSM is a powerful, multi-tenant Field Service Management system designed to streamline operations between office and field staff. Built with Laravel 9, it offers a complete solution for managing work orders, assets, clients, and billing in a single platform.

### 🎯 Key Benefits

-   **Improved Productivity**: Streamlined workflows from request to completion
-   **Better Communication**: Real-time updates between office and field teams
-   **Enhanced Customer Satisfaction**: Transparent service delivery and tracking
-   **Operational Efficiency**: Automated processes and comprehensive reporting

## ✨ Features

### 🏢 **Multi-Tenant Architecture**

-   **Super Admin**: Platform-wide management
-   **Organization Owners**: Complete business control
-   **Managers & Staff**: Role-based access control
-   **Clients**: Self-service portal access

### 📋 **Work Order Management**

```
Client Request → WO Request → Estimation → Work Order → Service Appointment → Invoice → Payment
```

-   **Work Order Lifecycle**: Complete workflow management
-   **Priority Levels**: Low, Medium, High, Critical
-   **Status Tracking**: Real-time progress updates
-   **Service Scheduling**: Appointment management system
-   **Task Management**: Detailed task tracking with duration

### 🏭 **Asset & Service Management**

-   **Asset Registry**: Comprehensive equipment tracking
-   **Service Catalog**: Parts and services with pricing
-   **Warranty Management**: Expiration tracking and notes
-   **Inventory Control**: SKU-based part identification

### 💰 **SaaS Billing System**

-   **Subscription Plans**: Monthly, Quarterly, Yearly, Unlimited
-   **Multiple Payment Gateways**: Stripe, PayPal, Flutterwave, Bank Transfer
-   **Coupon System**: Discount codes with usage limits
-   **User Limits**: Configurable per subscription tier

### 🌍 **Multi-Language Support**

-   **13+ Languages**: English, Arabic, Spanish, French, German, Italian, Dutch, Japanese, Polish, Portuguese, Russian, Danish, and more
-   **RTL Support**: Right-to-left language compatibility
-   **Localization**: Date/time formats and currency support

### 🔐 **Security Features**

-   **2FA Authentication**: Google 2FA integration
-   **Role-based Permissions**: Granular access control
-   **XSS Protection**: Built-in security middleware
-   **Email Verification**: Configurable verification system

### 📊 **Reporting & Analytics**

-   **Dashboard Analytics**: Role-specific insights
-   **Revenue Tracking**: Comprehensive financial reporting
-   **Work Order Statistics**: Performance metrics
-   **User Activity Monitoring**: System usage tracking

## 🚀 Installation

### Prerequisites

-   PHP 8.0 or higher
-   Composer
-   Node.js & NPM
-   MySQL/PostgreSQL database
-   Web server (Apache/Nginx)

### Quick Setup

1. **Clone the repository**

    ```bash
    git clone https://github.com/mubseoul/smart-fsm.git
    cd smart-fsm
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database configuration**

    ```bash
    # Update .env with your database credentials
    php artisan migrate --seed
    ```

5. **Build assets**

    ```bash
    npm run build
    ```

6. **Start development server**
    ```bash
    php artisan serve
    ```

### 🔧 Configuration

#### Payment Gateways

```env
# Stripe
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key

# PayPal
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_SECRET=your_paypal_secret

# Flutterwave
FLUTTERWAVE_PUBLIC_KEY=your_flutterwave_public_key
FLUTTERWAVE_SECRET_KEY=your_flutterwave_secret_key
```

#### Email Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
```

## 📖 Documentation

### For Developers

-   **[SMART_FSM_DOCUMENTATION.md](SMART_FSM_DOCUMENTATION.md)**: Complete technical documentation
-   **[SMART_FSM_CLAUDE_CONTEXT.md](SMART_FSM_CLAUDE_CONTEXT.md)**: AI assistant context file
-   **[DATABASE_AND_REGISTRATION_ANALYSIS.md](DATABASE_AND_REGISTRATION_ANALYSIS.md)**: Database structure and registration flow analysis

### AI Context Files

This project includes comprehensive context files to help AI assistants (like Claude) understand the system:

#### 🤖 For AI Assistants (Claude, ChatGPT, etc.)

**Primary Context File**: `SMART_FSM_CLAUDE_CONTEXT.md`

-   Complete user roles and hierarchy
-   Database structure overview
-   Registration flow details
-   Essential functions and patterns
-   Security considerations
-   Development guidelines

**Additional Context Files**:

-   `SMART_FSM_DOCUMENTATION.md`: Technical documentation
-   `DATABASE_AND_REGISTRATION_ANALYSIS.md`: Detailed database analysis

#### 📋 How to Preserve Information for AI

1. **Update Context Files**: Add new information to `SMART_FSM_CLAUDE_CONTEXT.md`
2. **Use Descriptive Commits**: Include context in git commit messages
3. **Document Changes**: Update relevant .md files when making system changes
4. **Code Comments**: Add detailed comments for complex business logic

#### 🔄 Keeping AI Context Updated

When making significant changes:

```bash
# 1. Update the context file
vim SMART_FSM_CLAUDE_CONTEXT.md

# 2. Commit with descriptive message
git add .
git commit -m "feat: Add new payment gateway - update AI context"

# 3. Document in relevant files
# Update SMART_FSM_DOCUMENTATION.md if needed
```

### Default Login Credentials

```
Super Admin: superadmin@gmail.com / 123456
Owner: owner@gmail.com / 123456
Manager: manager@gmail.com / 123456
```

## 🔧 Usage

### Creating Work Orders

1. **WO Request**: Client submits service request
2. **Estimation**: Generate cost estimate for approval
3. **Work Order**: Create detailed work assignment
4. **Service Appointment**: Schedule field service
5. **Invoice**: Generate billing after completion

### Managing Subscriptions

-   Configure subscription plans with user/client limits
-   Process payments through multiple gateways
-   Handle subscription renewals and upgrades
-   Apply coupon codes for discounts

### Multi-Tenant Operations

-   All data is isolated by organization (`parent_id`)
-   Role-based permissions control access
-   Subscription limits enforce user quotas
-   Each organization has independent settings

## 🏗️ Architecture

### Technology Stack

-   **Backend**: Laravel 9, PHP 8.0+
-   **Frontend**: Bootstrap 5, jQuery, Alpine.js
-   **Database**: MySQL/PostgreSQL
-   **Authentication**: Laravel Breeze + 2FA
-   **Permissions**: Spatie Laravel Permission
-   **Payments**: Multi-gateway integration

### Key Components

-   **Multi-tenancy**: Hierarchical organization structure
-   **Work Order Engine**: Complete lifecycle management
-   **Billing System**: SaaS subscription handling
-   **Asset Management**: Equipment and service tracking
-   **Communication**: Email notifications and templates

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines

-   Follow PSR-12 coding standards
-   Maintain multi-tenant data isolation
-   Respect the work order workflow
-   Add proper permissions for new features
-   Include tests for new functionality

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

-   Laravel Framework and its amazing ecosystem
-   Bootstrap for the responsive UI components
-   All contributors and users of Smart FSM

## 📞 Support

-   **Issues**: [GitHub Issues](https://github.com/mubseoul/smart-fsm/issues)
-   **Discussions**: [GitHub Discussions](https://github.com/mubseoul/smart-fsm/discussions)
-   **Documentation**: [Project Wiki](https://github.com/mubseoul/smart-fsm/wiki)

---

<div align="center">

**⭐ Star this repository if it helped you!**

Made with ❤️ for the field service community

</div>
