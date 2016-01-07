<?php namespace C4tech\Test\RayEmitter;

use C4tech\RayEmitter\Contracts\Event\Store;
use C4tech\Foundation\Contracts\RoleInterface;
use C4tech\Foundation\Contracts\UserInterface;
use C4tech\Support\Test\Base as TestCase;
use Illuminate\Support\Facades\App;
use Mockery;

class ServiceProviderTest extends TestCase
{
    public function setUp()
    {
        $this->provider = Mockery::mock('C4tech\RayEmitter\ServiceProvider', [null])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    public function tearDown()
    {
        App::clearResolvedInstances();
        parent::tearDown();
    }

    public function testConstructor()
    {
        $property = $this->getProperty($this->provider, 'configPath');
        expect($property->getValue($this->provider))
            ->contains('/resources/config.php');
    }

    public function testBoot()
    {
        include_once('helpers.php');
        $this->provider->shouldReceive('publishes')
            ->with(
                Mockery::on(function ($configMapping) {
                    $keys = array_keys($configMapping);
                    $key = array_pop($keys);

                    if (substr($key, -10) !== 'config.php') {
                        return false;
                    }

                    expect($key)->contains('/resources/config.php');

                    $value = array_pop($configMapping);
                    expect($value)->equals('test/ray_emitter.php');

                    return true;
                }),
                'config'
            )->once();

        $this->provider->shouldReceive('publishes')
            ->with(
                Mockery::on(function ($migrationMapping) {
                    $keys = array_keys($migrationMapping);
                    $key = array_pop($keys);

                    if (substr($key, -10) !== 'migrations') {
                        return false;
                    }

                    expect($key)->contains('/resources/migrations');

                    $value = array_pop($migrationMapping);
                    expect($value)->equals('testdb/migrations');

                    return true;
                }),
                'migrations'
            )->once();

        expect_not($this->provider->boot());
    }

    public function testRegister()
    {
        $this->provider->shouldReceive('mergeConfigFrom')
            ->with(Mockery::type('string'), 'ray_emitter')
            ->once();

        App::shouldReceive('singleton')
            ->with(
                'rayemitter.store',
                Mockery::on(function ($closure) {
                    $result = $closure();
                    expect_that($result);
                    expect($result instanceof Store)->true();
                    return true;
                })
            )->once();

        expect_not($this->provider->register());
    }

    public function testProvides()
    {
        expect($this->provider->provides())
            ->equals(['rayemitter.store']);
    }
}
