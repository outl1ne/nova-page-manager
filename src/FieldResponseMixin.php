<?php

namespace OptimistDigital\NovaPageManager;

class FieldResponseMixin
{
    /**
     * Define the callback that should be used to resolve the field's value in the response.
     *
     * @param  callable  $resolveResponseCallback
     * @return $this
     */
    public function resolveResponseUsing()
    {
        return function (callable $resolveResponseCallback) {
            $this->{'resolveResponseCallback'} = $resolveResponseCallback;
            return $this;
        };
    }

    /**
     * Resolve the field's value in the response.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function resolveResponseValue()
    {
        return function ($value, $templateModel) {
            if (method_exists($this, 'resolveResponseValue')) {
                $value = $this->resolveResponseValue($value, $templateModel);
            }

            if (isset($this->resolveResponseCallback) && is_callable($this->resolveResponseCallback)) {
                $value = call_user_func($this->resolveResponseCallback, $value);
            }

            return $value;
        };
    }
}
