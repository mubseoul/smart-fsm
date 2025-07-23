<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'description',
        'active',
        'is_deletable',
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_deletable' => 'boolean',
    ];

    /**
     * Get the parent category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories (subcategories)
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Check if category has children
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get all parent categories (for dropdown)
     */
    public static function getParentCategories()
    {
        return self::where('parent_id', 0)->where('active', true)->get();
    }

    /**
     * Generate slug from name
     */
    public static function generateSlug($name, $id = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (self::where('slug', $slug)->when($id, function($query, $id) {
            return $query->where('id', '!=', $id);
        })->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get full category name with parent
     */
    public function getFullNameAttribute()
    {
        if ($this->parent_id > 0 && $this->parent) {
            return $this->parent->name . ' > ' . $this->name;
        }
        return $this->name;
    }
} 