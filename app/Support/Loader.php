<?php

namespace Capo\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Loader
{
    protected string $url = '';

    public $ttl = 0;


    /**
     * @return Collection
     */
    public function collection()
    {
        return collect($this->fetch());
    }

    public function fetch()
    {
        if (empty($this->url)) {
            throw new \Exception('No URL set');
        }

        return Cache::remember($this->url, $this->ttl, function () {
            return $this->transform(
                Http::get($this->url)->json()
            );
        });
    }

    public function transform($data)
    {
        return $data;
    }
}
