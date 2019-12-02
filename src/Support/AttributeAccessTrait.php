<?php

namespace Laracatch\Client\Support;

use Illuminate\Support\Str;

trait AttributeAccessTrait
{
    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        // getter
        $getter = Str::camel('get_' . $name . '_Attribute');

        if (method_exists($this, $getter)) {
            return call_user_func_array([$this, $getter], []);
        }

        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        throw new \InvalidArgumentException('The ' . $name . ' field is not resolvable');
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
}
