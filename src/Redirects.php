<?php

namespace Foorintodev\Seo;

use Statamic\Facades\YAML;

/**
 * Stockage plat des redirections 301 : une simple carte { ancien_chemin: nouveau_chemin }.
 * Les chemins incluent le préfixe de langue éventuel (ex. /en/…), donc uniques.
 */
class Redirects
{
    protected ?array $cache = null;

    public function path(): string
    {
        return config('foorintodev-seo.redirects_path') ?: base_path('content/redirects.yaml');
    }

    public function all(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $path = $this->path();

        return $this->cache = is_file($path)
            ? (array) YAML::parse(file_get_contents($path))
            : [];
    }

    public function lookup(string $from): ?string
    {
        $to = $this->all()[$this->normalize($from)] ?? null;

        return $to ? $this->normalize($to) : null;
    }

    public function add(string $from, string $to): void
    {
        $from = $this->normalize($from);
        $to = $this->normalize($to);

        if ($from === '' || $from === $to) {
            return;
        }

        $map = $this->all();

        // Aplatir les chaînes : toute redirection qui pointait vers l'ancien chemin
        // pointe désormais directement vers le nouveau.
        foreach ($map as $k => $v) {
            if ($this->normalize((string) $v) === $from) {
                $map[$k] = $to;
            }
        }

        // Le nouveau chemin ne doit plus être une source de redirection.
        unset($map[$to]);

        $map[$from] = $to;

        $this->save($map);
    }

    public function forget(string $from): void
    {
        $map = $this->all();
        unset($map[$this->normalize($from)]);
        $this->save($map);
    }

    protected function save(array $map): void
    {
        ksort($map);
        @mkdir(dirname($this->path()), 0755, true);
        file_put_contents($this->path(), YAML::dump($map));
        $this->cache = $map;
    }

    public function normalize(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH) ?: $path;
        $path = '/'.trim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
