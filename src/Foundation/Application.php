<?php
/**
 * Application.php
 *
 * Creator:    chongyi
 * Created at: 2016/08/06 22:40
 */

namespace Journey\Modules\Swoole\Foundation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Application extends LaravelApplication
{
    private $swoole_path;
    private $swoole_config;

    public function __construct(string $basePath = null)
    {
        include __DIR__ . '/Providers/RouteServiceProvider.php';
        $this->swoole_path = __DIR__ . '/../../';
        $this->swoole_config =  include __DIR__ . '/../../config/app.php';
        parent::__construct($basePath);
        $this->init_singleton();
    }

    public function init_singleton () {
        $this->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \App\Http\Kernel::class
        );

        $this->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            \App\Console\Kernel::class
        );

        $this->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \App\Exceptions\Handler::class
        );
    }

    /**
     * Register all of the configured providers.
     *
     * @return void
     */
    public function registerConfiguredProviders()
    {
        $providers = Collection::make($this->swoole_config['providers'])
            ->partition(function ($provider) {
                return Str::startsWith($provider, 'Illuminate\\');
            });

        $providers->splice(1, 0, [$this->make(PackageManifest::class)->providers()]);

        (new ProviderRepository($this, new Filesystem, $this->getCachedServicesPath()))
            ->load($providers->collapse()->toArray());
    }

    public function bootstrapPath($path = '')
    {
        return $this->swoole_path.'bootstrap'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

}