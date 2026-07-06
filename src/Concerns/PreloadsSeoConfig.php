<?php

namespace Foorintodev\Seo\Concerns;

/**
 * Champs d'aperçu (Google / réseaux sociaux) : purement décoratifs.
 * Ils ne stockent aucune valeur ; ils exposent la config SEO au composant Vue.
 */
trait PreloadsSeoConfig
{
    public function preProcess($data)
    {
        return null;
    }

    public function process($data)
    {
        return null;
    }

    /** Données transmises au composant Vue via `this.meta`. */
    public function preload()
    {
        $c = config('foorintodev-seo', []);

        $sites = [];
        foreach (($c['sites'] ?? []) as $handle => $over) {
            $sites[$handle] = [
                'siteName' => $over['site_name'] ?? null,
                'defaultDescription' => $over['default_description'] ?? null,
                'defaultImage' => $over['default_og_image'] ?? null,
            ];
        }

        // Base URL du container d'assets, pour afficher l'image choisie (chemin relatif)
        // dans les aperçus. Relative (ex. /assets/zeutzius) : elle se résout contre
        // l'hôte courant du panneau, quel que soit app.url.
        $assetsBaseUrl = null;
        if (($container = $c['assets_container'] ?? null)
            && ($ct = \Statamic\Facades\AssetContainer::find($container))
            && ($u = $ct->url())) {
            $assetsBaseUrl = rtrim($u, '/');
        }

        return [
            'baseUrl' => rtrim((string) config('app.url'), '/'),
            'siteName' => $c['site_name'] ?: config('app.name'),
            'separator' => $c['title_separator'] ?? '·',
            'appendSiteName' => (bool) ($c['append_site_name'] ?? true),
            'defaultImage' => $c['default_og_image'] ?? null,
            'defaultDescription' => $c['default_description'] ?? null,
            'assetsBaseUrl' => $assetsBaseUrl,
            'sites' => $sites,
        ];
    }
}
