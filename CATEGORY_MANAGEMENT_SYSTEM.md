# Category Management System

## Overview

A hierarchical category management system has been implemented for Super Admin users. The system supports parent-child relationships (main categories and subcategories) with consistent popup-style editing following the Package Management pattern.

## Features

### Core Functionality

-   **Add Category**: Create new categories with optional parent category
-   **Edit Category**: Modify existing categories using popup forms
-   **Delete Category**: Remove categories (with protection for non-deletable categories)
-   **Hierarchical Structure**: Support for main categories and subcategories using `parent_id`
-   **Unique Slug Validation**: Ensures all category slugs are unique across the system

### Database Schema

```sql
CREATE TABLE categories (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    parent_id INT DEFAULT 0,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    active BOOLEAN DEFAULT TRUE,
    is_deletable BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Category Fields

-   **name**: Category display name (required)
-   **parent_id**: ID of parent category (0 for main categories)
-   **slug**: URL-friendly identifier (required, unique)
-   **description**: Optional category description
-   **active**: Enable/disable category
-   **is_deletable**: Protection flag to prevent deletion

## Access Control

### Permissions

-   `manage categories`: View category list
-   `create categories`: Create new categories
-   `edit categories`: Modify existing categories
-   `delete categories`: Remove categories

### User Access

-   **Super Admin Only**: All category management features are restricted to Super Admin users
-   Menu item appears in Super Admin navigation under "Categories"

## User Interface

### Category List View

-   Hierarchical display showing parent-child relationships
-   Visual indicators for subcategories (└─ prefix)
-   Status badges for Active/Inactive categories
-   Slug display with badges
-   Action buttons for Edit/Delete operations

### Popup Forms

-   **Create Category**: Modal popup with all category fields
-   **Edit Category**: Pre-populated modal for modifications
-   **Auto-slug Generation**: Automatic slug creation from category name
-   **Parent Selection**: Dropdown of available parent categories

## Validation & Business Rules

### Validation Rules

-   Name is required (max 255 characters)
-   Slug is required and must be unique
-   Parent category must exist if specified
-   Description is optional

### Business Logic

-   Cannot set category as parent of itself
-   Cannot set category as parent of its own child (prevents circular references)
-   Cannot delete categories that have subcategories
-   Cannot delete categories marked as `is_deletable = false`
-   Auto-generates unique slugs when slug conflicts occur

## Usage Examples

### Creating a Main Category

1. Navigate to Categories page
2. Click "Create Category" button
3. Fill in name, slug, description
4. Leave "Parent Category" as "None (Main Category)"
5. Set Active and Is Deletable flags as needed
6. Click "Create"

### Creating a Subcategory

1. Navigate to Categories page
2. Click "Create Category" button
3. Fill in category details
4. Select parent category from "Parent Category" dropdown
5. Complete form and click "Create"

### Editing Categories

1. Click edit icon next to any category
2. Modify fields in popup form
3. Click "Update" to save changes

## Technical Implementation

### Model Relationships

```php
// Parent relationship
public function parent()
{
    return $this->belongsTo(Category::class, 'parent_id');
}

// Children relationship
public function children()
{
    return $this->hasMany(Category::class, 'parent_id');
}
```

### Routes

-   `GET /categories` - Category list page
-   `GET /categories/create` - Create form (popup)
-   `POST /categories` - Store new category
-   `GET /categories/{id}/edit` - Edit form (popup)
-   `PUT /categories/{id}` - Update category
-   `DELETE /categories/{id}` - Delete category

### Key Methods

-   `Category::getParentCategories()` - Get available parent categories
-   `Category::generateSlug($name, $id)` - Generate unique slug
-   `Category::hasChildren()` - Check if category has subcategories
-   `$category->getFullNameAttribute()` - Get "Parent > Child" display name

## Integration Points

The category system is designed to be reusable across the Smart FSM application. Categories can be linked to:

-   Service Parts
-   Assets
-   Work Order Types
-   Client Classifications
-   Any other entities requiring categorization

## Maintenance

### Adding New Permissions

Add category permissions to `DefaultDataUsersTableSeeder.php` for new roles that need access.

### Extending Functionality

The system can be extended with:

-   Category icons/images
-   Sort ordering
-   Category-specific settings
-   Multi-level nesting (currently supports 2 levels)

## Security Notes

-   All routes protected with authentication and XSS middleware
-   Permission-based access control
-   Validation prevents circular parent-child relationships
-   Protection against deleting categories with dependencies
