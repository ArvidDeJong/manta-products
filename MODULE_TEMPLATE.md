# Manta CMS Module Template

This document describes how to build a new module for the **darvis/manta-laravel-flux-cms** system, based on the structure of the gallery module.

## Preparation

### 1. Module Planning

Before you start, determine:

- **Module name** (e.g. `manta-events`, `manta-newsletter`)
- **Main entities** (e.g. Event, Registration)
- **Functionality** (CRUD, uploads, submissions, etc.)
- **Database fields** you need

### 2. Naming Conventions

- **Package name**: `darvis/manta-{modulename}`
- **Namespace**: `Darvis\Manta{ModuleName}`
- **Models**: `{EntityName}` and optionally `{EntityName}Submission`
- **Tables**: `{modulename}s` and `{modulename}submissions`

## Step 1: Set Up Project Structure

### Directory Structure

```
manta-{modulename}/
├── config/
│   └── {modulename}.php
├── database/
│   └── migrations/
│       ├── create_{modulename}s_table.php
├── resources/
│   └── views/
├── routes/
│   └── web.php
├── src/
│   ├── Models/
│   │   ├── {EntityName}.php
│   └── {ModuleName}ServiceProvider.php
├── composer.json
├── README.md
└── project.md
```

## Step 2: Configure Composer.json

```json
{
  "name": "darvis/manta-{modulename}",
  "description": "{Module description} package for Laravel",
  "type": "library",
  "license": "MIT",
  "authors": [
    {
      "name": "Darvis",
      "email": "info@arvid.nl"
    }
  ],
  "require": {
    "php": "^8.2",
    "illuminate/support": "^12.0"
  },
  "require-dev": {
    "orchestra/testbench": "^9.0",
    "phpunit/phpunit": "^10.0"
  },
  "autoload": {
    "psr-4": {
      "Darvis\\Manta{ModuleName}\\": "src/"
    }
  },
  "autoload-dev": {
    "psr-4": {
      "Darvis\\Manta{ModuleName}\\Tests\\": "tests/"
    }
  },
  "extra": {
    "laravel": {
      "providers": ["Darvis\\Manta{ModuleName}\\{ModuleName}ServiceProvider"]
    }
  },
  "minimum-stability": "dev",
  "prefer-stable": true,
  "config": {
    "sort-packages": true
  },
  "version": "dev-master"
}
```

## Step 3: Create Service Provider

```php
<?php

namespace Darvis\Manta{ModuleName};

use Illuminate\Support\ServiceProvider;

class {ModuleName}ServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register package services
        $this->mergeConfigFrom(
            __DIR__.'/../config/{modulename}.php', '{modulename}'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/{modulename}.php' => config_path('{modulename}.php'),
        ], '{modulename}-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], '{modulename}-migrations');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load routes (activate later if needed)
        // $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views (activate later if needed)
        // $this->loadViewsFrom(__DIR__.'/../resources/views', '{modulename}');
    }
}
```

## Step 4: Configuration File

```php
<?php
// config/{modulename}.php

return [
    /*
    |--------------------------------------------------------------------------
    | {ModuleName} Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can adjust the configuration for the {ModuleName} package.
    |
    */

    // General settings
    'enabled' => true,

    // Route prefix
    'route_prefix' => '{modulename}',

    // Database configuration
    'database' => [
        'table_prefix' => '',
    ],
];
```

## Step 5: Create Models

### Main Entity Model

```php
<?php

namespace Darvis\Manta{ModuleName}\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Manta\Traits\HasUploadsTrait;
use Manta\Traits\HasTranslations;

class {EntityName} extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUploadsTrait;
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Standard CMS fields
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id',
        'host',
        'pid',
        'locale',

        // Content fields
        'title',
        'subtitle',
        'content',
        'excerpt',

        // SEO fields
        'seo_title',
        'seo_description',
        'slug',

        // Specific fields
        'year',
        'data',

        // System fields
        'active',
        'sort',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'data' => 'array',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'manta_{modulename}s';
}
```

### Submission Model (Optional)

```php
<?php

namespace Darvis\Manta{ModuleName}\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Manta\Traits\HasUploadsTrait;

class {EntityName}Submission extends Model
{
    use HasFactory, SoftDeletes;
    use HasUploadsTrait;

    protected $fillable = [
        // Standard CMS fields
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id',
        'host',
        'locale',

        // Gallery information
        'firstname',
        'lastname',
        'email',
        'phone',
        'company',

        // Address information
        'address',
        'zipcode',
        'city',
        'country',

        // Communication
        'subject',
        'comment',
        'internal_gallery',
        'ip',

        // Extra data
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $table = 'manta_{modulename}submissions';
}
```

## Step 6: Important Notes for Model Names

### Manta CMS Naming Convention

For proper integration with Manta CMS, use the following naming:

```php
<?php

namespace Darvis\Manta{ModuleName}\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Manta\FluxCMS\Traits\HasUploadsTrait;

class {EntityName} extends Model
{
    use SoftDeletes;
    use HasUploadsTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'manta_{modulename}s';

    // Rest of the model...
}
```

### Important Points

- Always use `manta_` prefix for table names
- Follow Laravel naming conventions for models and relationships

## Step 7: Database Migrations

### Main Entity Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manta_{modulename}s', function (Blueprint $table) {
            $table->id();

            // Standard CMS fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('host')->nullable();
            $table->string('pid')->nullable();
            $table->string('locale', 5)->default('nl');

            // Content fields
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();

            // SEO fields
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('slug')->nullable();

            // Specific fields
            $table->integer('year')->nullable();
            $table->json('data')->nullable();

            // System fields
            $table->boolean('active')->default(true);
            $table->integer('sort')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'host']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manta_{modulename}s');
    }
};
```

### Submission Migration (Optional)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manta_{modulename}submissions', function (Blueprint $table) {
            $table->id();

            // Standard CMS fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('host')->nullable();
            $table->string('locale', 5)->default('nl');

            // Gallery information
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();

            // Address information
            $table->string('address')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();

            // Communication
            $table->string('subject');
            $table->longText('comment')->nullable();
            $table->string('internal_gallery')->nullable();
            $table->string('ip')->nullable();

            $table->json('data')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'host']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manta_{modulename}submissions');
    }
};
```

## Step 8: Routes Configuration

```php
<?php
// routes/web.php

use Illuminate\Support\Facades\Route;

// Main entity routes
Route::middleware(['web', 'auth:staff'])->group(function () {
    $prefix = config('{modulename}.route_prefix', '{modulename}');

    Route::prefix($prefix)->group(function () {
        // List
        Route::get('/', {EntityName}List::class)->name('{modulename}.index');

        // Create
        Route::get('/create', {EntityName}Create::class)->name('{modulename}.create');

        // Read
        Route::get('/{id}', {EntityName}Read::class)->name('{modulename}.show');

        // Update
        Route::get('/{id}/edit', {EntityName}Update::class)->name('{modulename}.edit');

        // Upload
        Route::get('/{id}/files', {EntityName}Upload::class)->name('{modulename}.upload');

        // Settings
        Route::get('/settings', {EntityName}Settings::class)->name('{modulename}.settings');
    });
});

// Submission routes (if applicable)
Route::middleware(['web', 'auth:staff'])->group(function () {
    $prefix = config('{modulename}.route_prefix', '{modulename}') . 'submission';

    Route::prefix($prefix)->group(function () {
        // Similar routes for submissions...
    });
});
```

## Step 9: Livewire Components

### Example List Component

```php
<?php

namespace Darvis\Manta{ModuleName}\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Darvis\Manta{ModuleName}\Models\{EntityName};

#[Layout('manta::layouts.app')]
class {EntityName}List extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function mount()
    {
        $this->getBreadcrumb();
    }

    public function getBreadcrumb()
    {
        // Implement breadcrumb logic
    }

    public function render()
    {
        $items = {EntityName}::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(config('{modulename}.ui.items_per_page', 25));

        return view('{modulename}::list', compact('items'));
    }
}
```

### Important Points for Livewire Components

- **Layout**: Always use `#[Layout('manta::layouts.app')]`
- **Traits**: Use the Manta traits for consistent functionality
- **Breadcrumbs**: Always call `getBreadcrumb()` in the mount method

## Step 10: README Template

```markdown
# Manta {ModuleName} Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/darvis/manta-{modulename}.svg?style=flat-square)](https://packagist.org/packages/darvis/manta-{modulename})
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

A Laravel package for {module description}. This module integrates seamlessly with the **darvis/manta-laravel-flux-cms** system.

## Features

- 🎯 **{Main feature}**: Description of main functionality
- 📝 **{Second feature}**: Description of second functionality
- 🌍 **Multilingual**: Support for multiple languages via Manta CMS
- 📁 **File Management**: Integrated upload functionality
- 🔒 **Security**: Staff middleware for access control
- ⚡ **Livewire v3**: Modern, reactive user interface
- 🎨 **FluxUI**: Beautiful, consistent UI components
- 🗄️ **Database**: Soft deletes and audit trails

## Requirements

- PHP ^8.2
- Laravel ^12.0
- darvis/manta-laravel-flux-cms

## Installation

### 1. Install Package

\`\`\`bash
composer require darvis/manta-{modulename}
\`\`\`

### 2. Publish Configuration

\`\`\`bash
php artisan vendor:publish --tag={modulename}-config
php artisan vendor:publish --tag={modulename}-migrations
\`\`\`

### 3. Run Database Migrations

\`\`\`bash
php artisan migrate
\`\`\`

## Usage

[Add specific usage instructions here]

## License

The MIT License (MIT). See [License File](LICENSE.md) for more information.
```

## Step 10: Checklist for New Module

### Pre-Development

- [ ] Module name and functionality determined
- [ ] Database schema designed
- [ ] Required fields identified
- [ ] Naming conventions applied

### Development

- [ ] Directory structure set up
- [ ] composer.json configured
- [ ] Service Provider created
- [ ] Configuration file created
- [ ] Model(s) implemented
- [ ] Database migrations written
- [ ] Routes configured
- [ ] README documentation written

### Testing & Deployment

- [ ] Package tested locally
- [ ] Migrations tested
- [ ] Configuration validated
- [ ] Documentation checked
- [ ] Package published

## Tips & Best Practices

### 1. Naming

- Use consistent naming throughout the project
- Follow Laravel conventions for models and tables
- Use clear, descriptive names

### 2. Database Design

- Always add the standard CMS fields
- Use JSON fields for flexible configuration
- Implement soft deletes for data integrity
- Add relevant indexes for performance

### 3. Manta CMS Integration

- Always use the Manta traits where possible
- Implement staff middleware for security
- Follow the CMS naming conventions for routes
- Ensure consistent UI with FluxUI components

### 4. Code Organization

- Keep models simple and focused
- Use service providers for configuration
- Document all public methods
- Follow PSR-4 autoloading standards

### 5. Testing

- Test all migrations thoroughly
- Validate configuration options
- Test integration with Manta CMS
- Check route registration

## Troubleshooting

### Common Issues

1. **Service Provider not loaded**: Check composer.json extra section
2. **Migrations not found**: Check path in Service Provider
3. **Routes not working**: Check middleware and prefix configuration
4. **Models not found**: Check namespace and autoloading

### Debug Tips

- Use `php artisan route:list` to check routes
- Check `php artisan config:cache` after configuration changes
- Use `composer dump-autoload` after namespace changes

This template provides a solid foundation for developing new Manta CMS modules. Adapt the placeholders to your specific needs and add module-specific functionality where required.
