<?php

namespace OptimistDigital\NovaPageManager\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateModel extends Model
{
    protected $table = 'nova_page_manager';

    protected $fillable = ['parent_id'];

    protected $casts = [
        'data' => 'object'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('nova-page-manager.table', $this->table));
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($template) {
            // Is a parent template
            if ($template->parent_id === null) {
                // Find child templates
                $childTemplates = TemplateModel::where('parent_id', '=', $template->id)->get();
                if (count($childTemplates) === 0) {
                    return;
                }

                // Pick the first template randomly and let it become the parent
                $childTemplates[0]->update(['parent_id' => null]);
                $newParentId = $childTemplates[0]->id;

                // Update others
                for ($i = 1; $i < count($childTemplates); $i++) {
                    $childTemplates[$i]->update(['parent_id' => $newParentId]);
                }
            }
        });
    }
}
