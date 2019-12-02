<?php

namespace Laracatch\Client\Collectors;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use Laracatch\Client\Contracts\EventCollectorContract;
use Laracatch\Client\Recorders\Event\Event;
use Laracatch\Client\Recorders\Event\EventRecorder;
use Laracatch\Client\Recorders\Log\LogMessage;
use Laracatch\Client\Support\AttributeTypeSerializationTrait;

class EventCollector implements EventCollectorContract
{
    use AttributeTypeSerializationTrait;

    /**
     * @var array
     */
    protected $events = [];

    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return mixed
     */
    public function listen()
    {
        return $this->app['events']->listen('*', function ($name, $data) {
            $this->record($name, $data);
        });
    }

    /**
     * @param null $name
     * @param array $data
     *
     * @throws \ReflectionException
     */
    protected function record($name = null, $data = [])
    {
        $params = $this->prepareParams($data);

        $listeners = [];

        foreach ($this->app['events']->getListeners($name) as $i => $listener) {
            // Check if it's an object + method name
            if (is_array($listener) && count($listener) > 1 && is_object($listener[0])) {
                list($class, $method) = $listener;

                // Skip this class itself
                if ($class instanceof static) {
                    continue;
                }

                // Format the listener to readable format
                $listener = get_class($class) . '@' . $method;
            // Handle closures
            } elseif ($listener instanceof \Closure) {
                $reflector = new \ReflectionFunction($listener);

                // Format the closure to a readable format
                $filename = ltrim(str_replace(base_path(), '', $reflector->getFileName()), '/');
                $listener = $reflector->getName() . ' (' . $filename . ':' . $reflector->getStartLine() . '-' . $reflector->getEndLine() . ')';
            } else {
                // Not sure if this is possible, but to prevent edge cases
                $listener = $this->serializeValue($listener);
            }

            $listeners[] = $listener;
        }

        $this->events[] = [
            'name' => $name,
            'params' => $params,
            'listeners' => $listeners,
            'microtime' => microtime(true),
        ];
    }

    /**
     * @param $params
     *
     * @return array
     */
    protected function prepareParams($params)
    {
        $data = [];

        foreach ($params as $key => $value) {
            if (is_object($value) && Str::is('Illuminate\*\Events\*', get_class($value))) {
                $value = $this->prepareParams(get_object_vars($value));
            }

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->events;
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->events = [];
    }
}
