<?php

namespace Darvis\MantaProduct\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Manta\FluxCMS\Traits\HasUploadsTrait;

class Category extends Model
{
    use HasFactory, SoftDeletes, HasUploadsTrait;

    protected $table = 'manta_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'sort',
        'active',
        'image',
        'deleted_by',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort' => 'integer',
    ];

    /**
     * Get the parent category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort');
    }

    /**
     * Get all descendants recursively
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors
     */
    public function ancestors()
    {
        $ancestors = collect([]);
        $parent = $this->parent;

        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get the breadcrumb path
     */
    public function getBreadcrumbPath()
    {
        return $this->ancestors()->pluck('name')->push($this->name)->implode(' > ');
    }

    /**
     * Get products in this category
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'manta_category_product', 'category_id', 'product_id');
    }

    /**
     * Scope to get only root categories
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get active categories
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get the depth level of this category
     */
    public function getDepthAttribute()
    {
        return $this->ancestors()->count();
    }

    /**
     * Check if this category has children
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get all category IDs including descendants
     */
    public function getAllCategoryIds()
    {
        $ids = collect([$this->id]);
        
        foreach ($this->children as $child) {
            $ids = $ids->merge($child->getAllCategoryIds());
        }
        
        return $ids;
    }
}
