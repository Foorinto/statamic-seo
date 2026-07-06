<?php

namespace Foorintodev\Seo\Tags;

use Illuminate\Support\Str;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Site;
use Statamic\Tags\Tags;

class Seo extends Tags
{
    protected static $handle = 'seo';

    /**
     * {{ seo }} — génère toutes les balises SEO du <head> :
     * titre, description, robots, canonical, Open Graph (Facebook/LinkedIn) et Twitter.
     * Priorité : champ de la page > défaut du site (config) > repli.
     */
    public function index(): string
    {
        $site = Site::current();

        $c = config('foorintodev-seo', []);
        $c = array_merge($c, $c['sites'][$site->handle()] ?? []);

        $val = fn (string $key) => $this->context->value($key);

        $siteName = $c['site_name'] ?: config('app.name');
        $sep = $c['title_separator'] ?? '·';

        // Titre : champ SEO, sinon titre de la page. On ajoute le nom du site,
        // sauf s'il y est déjà (évite « Beim Zeutzius … · Beim Zeutzius »).
        $pageTitle = trim((string) ($val('seo_title') ?: $val('title')));
        $appendName = ($c['append_site_name'] ?? true)
            && $siteName
            && $pageTitle !== ''
            && ! Str::contains($pageTitle, $siteName);
        $title = $pageTitle === ''
            ? $siteName
            : ($appendName ? "{$pageTitle} {$sep} {$siteName}" : $pageTitle);

        $description = trim((string) ($val('seo_description') ?: ($c['default_description'] ?? '')));

        // Les pages d'erreur (404, 500…) ne doivent jamais être indexées.
        $isError = (int) ($val('response_code') ?: 0) >= 400;

        $noindex = $isError || (bool) ($c['noindex_site'] ?? false) || (bool) $val('seo_noindex');
        $nofollow = (bool) ($c['nofollow_site'] ?? false);
        $robots = ($noindex ? 'noindex' : 'index').', '.($nofollow ? 'nofollow' : 'follow');

        $url = (string) ($val('permalink') ?: url()->current());

        // Réseaux sociaux : surcharges optionnelles, repli sur le SEO de base.
        $ogTitle = trim((string) ($val('og_title') ?: $title));
        $ogDescription = trim((string) ($val('og_description') ?: $description));

        // Image de partage : sélecteur d'asset (chemin relatif au container), URL ou
        // chemin absolu ; repli sur l'image par défaut du site.
        $image = $this->resolveImage($val('seo_image'), $c);
        if ($image === '') {
            $default = trim((string) ($c['default_og_image'] ?? ''));
            $image = $default === '' ? ''
                : (Str::startsWith($default, ['http://', 'https://']) ? $default : url($default));
        }

        $twitterCard = (string) ($val('twitter_card') ?: ($image !== '' ? 'summary_large_image' : 'summary'));

        return $this->build([
            'title' => $title,
            'description' => $description,
            'robots' => $robots,
            'url' => $url,
            'image' => $image,
            'site_name' => $siteName,
            'locale' => $site->locale(),
            'og_type' => $c['og_type'] ?? 'website',
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'twitter_card' => $twitterCard,
            'twitter_site' => $c['twitter_handle'] ?? null,
            'twitter_creator' => $c['twitter_creator'] ?? ($c['twitter_handle'] ?? null),
            'fb_app_id' => $c['fb_app_id'] ?? null,
        ]);
    }

    /**
     * Résout une valeur d'image en URL absolue, quelle que soit sa forme :
     * objet Asset, collection d'assets, chemin relatif au container, chemin web ou URL.
     */
    protected function resolveImage($value, array $c): string
    {
        if ($value instanceof \Statamic\Contracts\Assets\Asset) {
            return (string) $value->absoluteUrl();
        }

        if (is_iterable($value)) {
            foreach ($value as $item) {
                if (($resolved = $this->resolveImage($item, $c)) !== '') {
                    return $resolved;
                }
            }

            return '';
        }

        $v = trim((string) $value);
        if ($v === '') {
            return '';
        }
        if (Str::startsWith($v, ['http://', 'https://'])) {
            return $v;
        }
        if (Str::startsWith($v, '/')) {
            return url($v);
        }

        // Chemin relatif au container d'assets (ex. « photos/hero.webp »).
        $container = $c['assets_container'] ?? null;
        if ($container && ($ct = AssetContainer::find($container))) {
            if ($asset = $ct->asset($v)) {
                return (string) $asset->absoluteUrl();
            }
            if ($base = $ct->url()) {
                return url(rtrim($base, '/').'/'.ltrim($v, '/'));
            }
        }

        return url($v);
    }

    protected function build(array $d): string
    {
        $e = fn ($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
        $t = [];

        // --- Base référencement -------------------------------------------------
        $t[] = '<title>'.$e($d['title']).'</title>';
        if ($d['description'] !== '') {
            $t[] = '<meta name="description" content="'.$e($d['description']).'">';
        }
        $t[] = '<meta name="robots" content="'.$e($d['robots']).'">';
        $t[] = '<link rel="canonical" href="'.$e($d['url']).'">';

        // --- Open Graph (Facebook, LinkedIn, WhatsApp…) -------------------------
        $t[] = '<meta property="og:type" content="'.$e($d['og_type']).'">';
        $t[] = '<meta property="og:site_name" content="'.$e($d['site_name']).'">';
        $t[] = '<meta property="og:title" content="'.$e($d['og_title']).'">';
        if ($d['og_description'] !== '') {
            $t[] = '<meta property="og:description" content="'.$e($d['og_description']).'">';
        }
        $t[] = '<meta property="og:url" content="'.$e($d['url']).'">';
        if ($d['locale']) {
            $t[] = '<meta property="og:locale" content="'.$e($d['locale']).'">';
        }
        if ($d['image'] !== '') {
            $t[] = '<meta property="og:image" content="'.$e($d['image']).'">';
            $t[] = '<meta property="og:image:alt" content="'.$e($d['og_title']).'">';
        }
        if ($d['fb_app_id']) {
            $t[] = '<meta property="fb:app_id" content="'.$e($d['fb_app_id']).'">';
        }

        // --- Twitter / X --------------------------------------------------------
        $t[] = '<meta name="twitter:card" content="'.$e($d['twitter_card']).'">';
        if ($d['twitter_site']) {
            $t[] = '<meta name="twitter:site" content="'.$e($d['twitter_site']).'">';
        }
        if ($d['twitter_creator']) {
            $t[] = '<meta name="twitter:creator" content="'.$e($d['twitter_creator']).'">';
        }
        $t[] = '<meta name="twitter:title" content="'.$e($d['og_title']).'">';
        if ($d['og_description'] !== '') {
            $t[] = '<meta name="twitter:description" content="'.$e($d['og_description']).'">';
        }
        if ($d['image'] !== '') {
            $t[] = '<meta name="twitter:image" content="'.$e($d['image']).'">';
            $t[] = '<meta name="twitter:image:alt" content="'.$e($d['og_title']).'">';
        }

        return implode("\n    ", $t);
    }
}
