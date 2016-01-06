<?php namespace C4tech\RayEmitter;

use C4tech\RayEmitter\Event\Store;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    protected $configPath = '';

    /**
     * @inheritDoc
     */
    public function __construct($app)
    {
        $this->configPath = __DIR__ . '/../resources/config.php';
        parent::__construct($app);
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {
        $configs = [];
        $configs[$this->configPath] = config_path('ray_emitter.php');
        $this->publishes($configs);
    }

    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath, 'ray_emitter');

        App::singleton(
            'rayemitter.store',
            function () {
                return new Store;
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function provides()
    {
        return ['rayemitter.store'];
    }
}
