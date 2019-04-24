<?php

namespace OptimistDigital\NovaPageManager\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateModel extends Model
{
    protected $fillable = ['parent_id', 'locale_parent_id'];

    protected $casts = [
        'data' => 'object'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($template) {
            // Is a locale parent template
            if ($template->locale_parent_id === null) {
                // Find child templates
                $childTemplates = $template::where('locale_parent_id', '=', $template->id)->get();
                if (count($childTemplates) === 0) return;

                // Pick the first template randomly and let it become the parent
                $childTemplates[0]->update(['locale_parent_id' => null]);
                $newLocaleParentId = $childTemplates[0]->id;

                // Update others
                for ($i = 1; $i < count($childTemplates); $i++) {
                    $childTemplates[$i]->update(['locale_parent_id' => $newLocaleParentId]);
                }
            }
        });
    }
}
