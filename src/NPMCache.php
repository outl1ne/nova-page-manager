<?php

namespace OptimistDigital\NovaPageManager;

class NPMCache
{
    protected $cache = [];

    public function find($id)
    {
        return $this->get('id', $id, fn () => NovaPageManager::getPageModel()::find($id));
    }

    protected function get($column, $id, $modelQuery)
    {
        if (!$this->isCached($column, $id)) {
            $page = $modelQuery();
            $this->cache($column, $id, $page);
            return $page;
        }
        return $this->retrieveFromCache($column, $id);
    }

    protected function isCached($column, $id)
    {
        return isset($this->cache[$column]) && array_key_exists($id, $this->cache[$column]);
    }

    protected function cache($column, $id, $object)
    {
        if (!isset($this->cache[$column])) {
            $this->cache[$column] = [];
        }
        $this->cache[$column][$id] = $object;
    }

    protected function retrieveFromCache($column, $id)
    {
        return $this->cache[$column][$id];
    }

    public function clear()
    {
        $this->cache = [];
    }
}
