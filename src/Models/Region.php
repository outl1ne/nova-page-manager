<?php

namespace OptimistDigital\NovaPageManager\Models;

use OptimistDigital\NovaPageManager\NovaPageManager;

class Region extends TemplateModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(NovaPageManager::getRegionsTableName());
    }
}
