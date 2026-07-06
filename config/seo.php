<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nom du site
    |--------------------------------------------------------------------------
    | Ajouté après le titre de chaque page. Vide = utilise le nom de l'app.
    | En multisite, tu peux le surcharger par site via la clé `sites` ci-dessous.
    */
    'site_name' => env('SEO_SITE_NAME', null),
    'title_separator' => '·',
    'append_site_name' => true,

    /*
    |--------------------------------------------------------------------------
    | Valeurs par défaut
    |--------------------------------------------------------------------------
    */
    'default_description' => env('SEO_DEFAULT_DESCRIPTION', null),

    // Image Open Graph par défaut : chemin absolu depuis la racine web
    // (ex. /assets/zeutzius/photos/hero-restaurant.webp) ou URL complète.
    'default_og_image' => env('SEO_DEFAULT_OG_IMAGE', null),

    // Container d'assets utilisé par le sélecteur d'image de partage (champ seo_image).
    // Sert à résoudre un chemin relatif au container en URL absolue. Null = champ texte.
    'assets_container' => env('SEO_ASSETS_CONTAINER', null),

    'og_type' => 'website',

    // Comptes réseaux sociaux (balises Twitter/X + Facebook).
    'twitter_handle' => env('SEO_TWITTER_HANDLE', null),   // twitter:site  (ex. @clausel_lu)
    'twitter_creator' => env('SEO_TWITTER_CREATOR', null), // twitter:creator (défaut = twitter_handle)
    'fb_app_id' => env('SEO_FB_APP_ID', null),             // fb:app_id (optionnel)

    /*
    |--------------------------------------------------------------------------
    | Indexation
    |--------------------------------------------------------------------------
    | noindex_site = true → tout le site en noindex (phase de préparation).
    | À passer à false (ou retirer SEO_NOINDEX du .env) le jour du lancement.
    */
    'noindex_site' => env('SEO_NOINDEX', false),
    'nofollow_site' => env('SEO_NOFOLLOW', false),

    /*
    |--------------------------------------------------------------------------
    | Surcharges par site (multisite)
    |--------------------------------------------------------------------------
    | Optionnel. Clé = handle du site Statamic. Chaque entrée peut redéfinir
    | n'importe quelle clé ci-dessus (site_name, default_description, etc.).
    |
    | 'sites' => [
    |     'zeutzius_fr' => ['default_description' => '…'],
    |     'zeutzius_en' => ['default_description' => '…'],
    | ],
    */
    'sites' => [],

    /*
    |--------------------------------------------------------------------------
    | Redirections
    |--------------------------------------------------------------------------
    | Redirections 301 automatiques quand le slug d'une page change (si la case
    | « Créer une redirection » est cochée). Fichier de stockage : null = content/redirects.yaml.
    */
    'redirects_path' => env('SEO_REDIRECTS_PATH', null),

];
