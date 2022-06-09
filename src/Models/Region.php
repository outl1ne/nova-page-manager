<?php

namespace Outl1ne\PageManager\Models;

use Outl1ne\PageManager\NPM;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Region extends Model
{
    use HasTranslations;

    protected $translatable = ['name'];
    protected $casts = ['data' => 'array'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(NPM::getRegionsTableName());
    }
}
