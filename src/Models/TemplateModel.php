<?php

namespace OptimistDigital\NovaPageManager\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateModel extends Model
{
    protected $table = 'nova_page_manager';

    protected $fillable = ['parent_id', 'locale_parent_id'];

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
            // Is a locale parent template
            if ($template->locale_parent_id === null) {
                // Find child templates
                $childTemplates = TemplateModel::where('locale_parent_id', '=', $template->id)->get();
                if (count($childTemplates) === 0) return;

                // Pick the first template randomly and let it become the parent
                $childTemplates[0]->update(['locale_parent_id' => null]);
                $newLocaleParentId = $childTemplates[0]->id;

                // Update others
                for ($i = 1; $i < count($childTemplates); $i++) {
                    $childTemplates[$i]->update(['locale_parent_id' => $newLocaleParentId]);
                }
            }

            // Is a parent template
            if ($template->parent_id === null) {
                // Find child templates
                $childTemplates = TemplateModel::where('parent_id', '=', $template->id)->get();
                if (count($childTemplates) === 0) return;

                // Set their parent to null
                $childTemplates->each(function ($template) {
                    $template->update(['parent_id' => null]);
                });
            }
        });
    }
}
