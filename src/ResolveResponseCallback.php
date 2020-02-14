<?php

namespace OptimistDigital\NovaPageManager;

trait ResolveResponseCallback
{
    private $resolveResponseCallback = null;

    /**
     * Define the callback that should be used to resolve the field's value in the response.
     *
     * @param  callable  $resolveResponseCallback
     * @return $this
     */
    public function resolveResponseUsing(callable $resolveResponseCallback)
    {
        $this->resolveResponseCallback = $resolveResponseCallback;

        return $this;
    }

    /**
     * Resolve the field's value in the response.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function resolveResponseValue($value)
    {
        if (method_exists(get_parent_class($this), 'resolveResponseValue')) {
            $value = parent::resolveResponseValue($value);
        }

        if ($this->resolveResponseCallback && is_callable($this->resolveResponseCallback)) {
            $value = call_user_func($this->resolveResponseCallback, $value);
        }

        return $value;
    }
}
