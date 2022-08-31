<?php

namespace Outl1ne\PageManager\Traits;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

trait DataReplaceHelpers
{
    protected function collectAndReplaceUsing(array $data, array $keys, string|Builder $model, $modelMapFn = null)
    {
        $ids = $this->collectValues($data, $keys);
        $replacementModels = is_string($model) ? $model::findMany($ids) : $model->findMany($ids);
        $replacementModels = $replacementModels->keyBy('id');
        if (is_callable($modelMapFn)) $replacementModels = $replacementModels->map($modelMapFn);
        return $this->replaceValues($data, $replacementModels, $keys);
    }

    protected function collectValues($data, array $keys)
    {
        $values = [];
        foreach ($keys as $key) {
            $values[] = Arr::pluck($data, $key);
        }
        $values = Arr::flatten($values);
        $values = array_filter($values);
        $values = array_unique($values);
        return array_values($values);
    }

    protected function replaceValues(array &$data, $replacementValueMap, array $replacementKeys)
    {
        foreach ($data as &$localeData) {
            if (empty($localeData)) continue;
            $this->walkAndReplace($localeData, $replacementValueMap, $replacementKeys);
        }
        return $data;
    }

    private function walkAndReplace(&$data, $replacementValueMap, $replacementKeys, $currentKey = null, $currentFullKey = null)
    {
        foreach ($data as $key => &$value) {
            $newFullKey = $currentFullKey ? "$currentFullKey.$key" : $key;

            if (is_iterable($value)) {
                $this->walkAndReplace($value, $replacementValueMap, $replacementKeys, $key, $newFullKey);
            } else {
                foreach ($replacementKeys as $rplKey) {
                    $rgxKey = str_replace('.', '\.', $rplKey);
                    $rgxKey = str_replace('*', '\d', $rgxKey);
                    $rgxKey = "($rgxKey)";

                    if (preg_match($rgxKey, $newFullKey)) {
                        $lastKey = Arr::last(explode('.', $key));
                        $data[$lastKey] = $replacementValueMap[$data[$lastKey]] ?? null;
                    }
                }
            }
        }
    }
}
