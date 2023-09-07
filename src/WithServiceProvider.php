<?php

namespace BytePlatform\Laravel;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use BytePlatform\Laravel\Exceptions\InvalidPackage;
use BytePlatform\Laravel\ServicePackage;
use ReflectionClass;

trait WithServiceProvider
{
    public ServicePackage $package;
    abstract public function configurePackage(ServicePackage $package): void;
    private $withHook = true;
    protected function DisableHook($flg = false)
    {
        $this->withHook = $flg;
    }
    private $extendPackage = false;
    protected function ExtendPackage($flg = true)
    {
        $this->extendPackage = $flg;
    }
    public function register()
    {
        $this->registeringPackage();

        $this->package = $this->newPackage();

        $this->package->setBasePath($this->getPackageBaseDir());

        $this->configurePackage($this->package);
        $this->configurePackaged();
        if (empty($this->package->name)) {
            throw InvalidPackage::nameIsRequired();
        }
        if ($this->withHook)
            do_action(PACKAGE_SERVICE_PROVIDER_REGISTER, $this);

        foreach ($this->package->configFileNames as $configFileName) {
            if (File::exists($this->package->basePath("/../config/{$configFileName}.php")))
                $this->mergeConfigFrom($this->package->basePath("/../config/{$configFileName}.php"), $configFileName);
        }
        if (File::exists($this->package->basePath("/../config/{$this->package->shortName()}.php")))
            $this->mergeConfigFrom($this->package->basePath("/../config/{$this->package->shortName()}.php"), $this->package->shortName());

        if ($this->package->hasRouteWeb) {
            Route::middleware('web')
                ->group($this->package->basePath('/../routes/web.php'));
        }
        if (!$this->extendPackage)
            $this->packageRegistered();

        return $this;
    }

    public function newPackage(): ServicePackage
    {
        return new ServicePackage();
    }

    public function boot()
    {
        $this->bootingPackage();

        if ($this->withHook)
            do_action(PACKAGE_SERVICE_PROVIDER_BOOT, $this);

        if ($this->package->hasTranslations) {
            $langPath = 'vendor/' . $this->package->shortName();

            $langPath = (function_exists('lang_path'))
                ? lang_path($langPath)
                : resource_path('lang/' . $langPath);
        }

        if ($this->app->runningInConsole()) {
            foreach ($this->package->configFileNames as $configFileName) {
                if (File::exists($this->package->basePath("/../config/{$configFileName}.php"))) {
                    $this->publishes([
                        $this->package->basePath("/../config/{$configFileName}.php") => config_path("{$configFileName}.php"),
                    ], "{$this->package->shortName()}-config");
                }
            }

            if ($this->package->hasViews && File::exists($this->package->basePath("/../resources/views"))) {
                $this->publishes([
                    $this->package->basePath('/../resources/views') => base_path("resources/views/vendor/{$this->package->shortName()}"),
                ], "{$this->package->shortName()}-views");
            }
            if (File::exists($this->package->basePath("/../database/migrations"))) {
                $now = Carbon::now();
                foreach ($this->package->migrationFileNames as $migrationFileName) {
                    $filePath = $this->package->basePath("/../database/migrations/{$migrationFileName}.php");
                    if (!file_exists($filePath)) {
                        // Support for the .stub file extension
                        $filePath .= '.stub';
                    }

                    $this->publishes([
                        $filePath => $this->generateMigrationName(
                            $migrationFileName,
                            $now->addSecond()
                        ),
                    ], "{$this->package->shortName()}-migrations");

                    if ($this->package->runsMigrations) {
                        $this->loadMigrationsFrom($filePath);
                    }
                }
                if ($this->package->runsMigrations && File::exists($this->package->basePath("/../database/migrations/"))) {
                    $migrationFiles =  File::allFiles($this->package->basePath("/../database/migrations/"));
                    if ($migrationFiles && count($migrationFiles) > 0) {
                        foreach ($migrationFiles  as $file) {
                            if ($file->getExtension() == "php") {
                                $this->loadMigrationsFrom($file->getRealPath());
                            }
                        }
                    }
                }
            }


            if ($this->package->runsSeeds && File::exists($this->package->basePath("/../database/seeders"))) {
                $seedFiles =  File::allFiles($this->package->basePath("/../database/seeders/"));
                if ($seedFiles && count($seedFiles) > 0) {
                    foreach ($seedFiles  as $file) {
                        if ($file->getExtension() == "php") {
                            require_once($file->getRealPath());
                        }
                    }
                }
            }
            if ($this->package->hasTranslations && File::exists($this->package->basePath('/../resources/lang'))) {
                $this->publishes([
                    $this->package->basePath('/../resources/lang') => $langPath,
                ], "{$this->package->shortName()}-translations");
            }

            if ($this->package->hasAssets && File::exists($this->package->basePath('/../resources/dist'))) {
                $this->publishes([
                    $this->package->basePath('/../resources/dist') => public_path("vendor/{$this->package->shortName()}"),
                ], "{$this->package->shortName()}-assets");
            }
        }

        if (!empty($this->package->commands)) {
            $this->commands($this->package->commands);
        }
        if ($commands = config($this->package->shortName() . '.commands')) {
            if (is_array($commands) && count($commands) > 0) {
                $this->commands($commands);
            }
        }
        if ($this->package->hasTranslations && File::exists($this->package->basePath('/../resources/lang'))) {
            $this->loadTranslationsFrom(
                $this->package->basePath('/../resources/lang/'),
                $this->package->shortName()
            );

            $this->loadJsonTranslationsFrom($this->package->basePath('/../resources/lang'));

            $this->loadJsonTranslationsFrom($langPath);
        }

        if ($this->package->hasViews && File::exists($this->package->basePath('/../resources/views'))) {
            $this->loadViewsFrom($this->package->basePath('/../resources/views'), $this->package->viewNamespace());
        }
        if ($this->package->viewComponents && File::exists($this->package->basePath('/../Components'))) {
            foreach ($this->package->viewComponents as $componentClass => $prefix) {
                $this->loadViewComponentsAs($prefix, [$componentClass]);
            }

            if (count($this->package->viewComponents)) {
                $this->publishes([
                    $this->package->basePath('/../Components') => base_path("app/View/Components/vendor/{$this->package->shortName()}"),
                ], "{$this->package->name}-components");
            }
        }


        if ($this->package->publishableProviderName && File::exists($this->package->basePath("/../resources/stubs/{$this->package->publishableProviderName}.php.stub"))) {
            $this->publishes([
                $this->package->basePath("/../resources/stubs/{$this->package->publishableProviderName}.php.stub") => base_path("app/Providers/{$this->package->publishableProviderName}.php"),
            ], "{$this->package->shortName()}-provider");
        }

        foreach ($this->package->sharedViewData as $name => $value) {
            View::share($name, $value);
        }

        foreach ($this->package->viewComposers as $viewName => $viewComposer) {
            View::composer($viewName, $viewComposer);
        }

        if (!$this->extendPackage)
            $this->packageBooted();

        return $this;
    }

    public static function generateMigrationName(string $migrationFileName, Carbon $now): string
    {
        $migrationsPath = 'migrations/';

        $len = strlen($migrationFileName) + 4;

        if (Str::contains($migrationFileName, '/')) {
            $migrationsPath .= Str::of($migrationFileName)->beforeLast('/')->finish('/');
            $migrationFileName = Str::of($migrationFileName)->afterLast('/');
        }

        foreach (glob(database_path("{$migrationsPath}*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName . '.php')) {
                return $filename;
            }
        }

        return database_path($migrationsPath . $now->format('Y_m_d_His') . '_' . Str::of($migrationFileName)->snake()->finish('.php'));
    }

    public function registeringPackage()
    {
    }
    public function configurePackaged()
    {
    }
    public function packageRegistered()
    {
    }

    public function bootingPackage()
    {
    }

    public function packageBooted()
    {
    }

    protected function getPackageBaseDir(): string
    {
        $reflector = new ReflectionClass(get_class($this));
        return dirname($reflector->getFileName());
    }

    public function getNamespaceName(): string
    {
        $reflector = new ReflectionClass(get_class($this));

        return $reflector->getNamespaceName();
    }
}
