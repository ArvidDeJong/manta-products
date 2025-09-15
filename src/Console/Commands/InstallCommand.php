<?php

namespace Darvis\MantaProduct\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manta-product:install
                            {--force : Overwrite existing files}
                            {--migrate : Run migrations after installation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Manta Product package';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Installing Manta Product Package...');
        $this->newLine();

        // Step 1: Publish configuration
        $this->publishConfiguration();

        // Step 2: Publish migrations
        $this->publishMigrations();

        // Step 3: Run migrations if requested
        $this->runMigrations();

        // Step 4: Publish settings files
        $this->publishSettings();

        // Step 5: Import module settings
        $this->importModuleSettings();

        // Step 6: Create default configuration
        $this->createDefaultConfiguration();

        // Step 7: Show completion message
        $this->showCompletionMessage();

        return self::SUCCESS;
    }

    /**
     * Publish configuration files
     */
    protected function publishConfiguration(): void
    {
        $this->info('📝 Publishing configuration files...');

        $params = [
            '--provider' => 'Darvis\MantaProduct\ProductServiceProvider',
            '--tag' => 'manta-product-config'
        ];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        Artisan::call('vendor:publish', $params);

        $this->line('   ✅ Configuration published to config/manta-product.php');
    }

    /**
     * Publish migration files
     */
    protected function publishMigrations(): void
    {
        $this->info('📦 Publishing migration files...');

        $params = [
            '--provider' => 'Darvis\MantaProduct\ProductServiceProvider',
            '--tag' => 'manta-product-migrations'
        ];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        Artisan::call('vendor:publish', $params);

        $this->line('   ✅ Migrations published to database/migrations/');
    }

    /**
     * Run database migrations
     */
    protected function runMigrations(): void
    {
        $this->info('🗄️  Running database migrations...');

        if ($this->confirm('This will run the database migrations. Continue?', true)) {
            Artisan::call('migrate');
            $this->line('   ✅ Migrations completed successfully');
        } else {
            $this->warn('   ⚠️  Migrations skipped. Run "php artisan migrate" manually later.');
        }
    }

    /**
     * Publish settings files
     */
    protected function publishSettings(): void
    {
        $this->info('📄 Publishing settings files...');

        $params = [
            '--provider' => 'Darvis\MantaProduct\ServiceProvider',
            '--tag' => 'manta-product-settings'
        ];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        Artisan::call('vendor:publish', $params);

        $this->line('   ✅ Settings published to export/settings-product.php');
    }

    /**
     * Import module settings
     */
    protected function importModuleSettings(): void
    {
        $this->info('📋 Importing module settings...');

        try {
            Artisan::call('manta:import-module-settings', [
                'package' => 'darvis/manta-product',
                '--all' => true
            ]);

            $this->line('   ✅ Module settings imported successfully');
        } catch (\Exception $e) {
            $this->warn('   ⚠️  Module settings import failed: ' . $e->getMessage());
            $this->warn('   ⚠️  You can run this manually: php artisan manta:import-module-settings darvis/manta-product --all');
        }
    }

    /**
     * Create default configuration if it doesn't exist
     */
    protected function createDefaultConfiguration(): void
    {
        $this->info('⚙️  Setting up default configuration...');

        $configPath = config_path('manta-product.php');

        // Give the filesystem a moment to catch up
        usleep(100000); // 0.1 second

        if (File::exists($configPath)) {
            try {
                $config = include $configPath;

                // Check if configuration needs updating
                if (!isset($config['route_prefix'])) {
                    $this->warn('   ⚠️  Configuration file exists but may need manual updates');
                } else {
                    $this->line('   ✅ Configuration file is ready');
                    $this->line('   📍 Route prefix: ' . $config['route_prefix']);
                }
            } catch (\Exception $e) {
                $this->warn('   ⚠️  Could not read configuration file: ' . $e->getMessage());
            }
        } else {
            // Check if the config was published but not found
            $this->warn('   ⚠️  Configuration file not found at: ' . $configPath);
            $this->line('   💡 The config should have been published. You can manually copy it if needed.');
        }
    }

    /**
     * Show completion message with next steps
     */
    protected function showCompletionMessage(): void
    {
        $this->newLine();
        $this->info('🎉 Manta Product Package installed successfully!');
        $this->newLine();

        $this->comment('Next steps:');
        $this->line('1. Configure your settings in config/manta-product.php');

        if (!$this->option('migrate')) {
            $this->line('2. Run migrations: php artisan migrate');
        }

        $this->line('3. Access the product management at: /product (or your configured route)');
        $this->newLine();

        $this->comment('Available routes:');
        $this->line('• GET /product - Product list');
        $this->line('• GET /product/toevoegen - Create new product');
        $this->line('• GET /product/aanpassen/{id} - Edit product');
        $this->line('• GET /product/lezen/{id} - View product');
        $this->line('• GET /product/bestanden/{id} - Manage product files');
        $this->line('• GET /product/instellingen - Product settings');
        $this->newLine();

        $this->info('📚 For more information, check the README.md file.');
    }
}
