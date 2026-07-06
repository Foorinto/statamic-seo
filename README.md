# Foorintodev SEO (addon Statamic)

Addon SEO **maison, gratuit, sans licence**, pour Statamic 6. Génère les balises SEO
essentielles, en **multisite** :

- `<title>` (avec ajout intelligent du nom du site, sans doublon)
- `<meta name="description">`
- `<meta name="robots">` (noindex / nofollow, **par page** ou pour tout le site)
- `<link rel="canonical">`
- **Open Graph** (og:title, description, url, site_name, locale, image, type)
- **Twitter Cards** (summary_large_image)

Volontairement simple (pas de sitemap/redirects/reporting comme SEO Pro), mais couvre
les règles SEO de base et fonctionne nativement en multisite/multilingue.

Deux onglets côté éditeur, avec **aperçus en direct** :
- **SEO** : titre, meta description, noindex + **aperçu du rendu Google**.
- **Réseaux sociaux** : image de partage, titre/description OG, format de carte Twitter
  + **aperçu Facebook / X / LinkedIn** (bascule entre les réseaux).

## Installer sur une installation Statamic

L'addon est un **package Composer local** (dossier `addons/statamic-seo/`). Il ne
s'installe donc pas depuis le panneau « Addons » du CP — le CP ne fait que *lister* les
addons ; l'ajout/retrait passe **toujours par Composer**.

1. Copier le dossier `addons/statamic-seo/` dans le projet (ou le cloner depuis un repo Git privé).
2. Déclarer le dépôt local dans le `composer.json` du projet :
   ```json
   "repositories": [
     { "type": "path", "url": "addons/statamic-seo", "options": { "symlink": true } }
   ]
   ```
   *(Depuis un repo Git privé plutôt qu'un dossier copié : `{ "type": "vcs", "url": "git@github.com:foorintodev/statamic-seo.git" }`.)*
3. Installer :
   ```bash
   composer require foorintodev/statamic-seo:^1.0
   ```
4. Publier la config + le fieldset :
   ```bash
   php artisan vendor:publish --tag=foorintodev-seo
   ```
5. Brancher dans les templates (voir « Utilisation » ci-dessous).

## Désinstaller

1. Retirer le package :
   ```bash
   composer remove foorintodev/statamic-seo
   ```
2. Retirer l'entrée `repositories` correspondante du `composer.json`.
3. Supprimer les fichiers publiés si tu ne t'en sers plus :
   `config/foorintodev-seo.php` et `resources/fieldsets/seo.yaml`.
4. Retirer `{{ seo }}` du layout et `- import: seo` des blueprints.
5. (Si copié en local) supprimer le dossier `addons/statamic-seo/`.

Le CP « Addons » n'a pas de bouton désinstaller pour ce type d'addon : c'est normal,
c'est Composer qui gère le cycle de vie.

## Utilisation

**1. Dans le `<head>` du layout** :
```antlers
{{ seo }}
```

**2. Ajouter les champs SEO à un blueprint** (deux onglets) :
```yaml
tabs:
  seo:
    display: SEO
    sections:
      - fields:
          - import: seo          # titre, description, noindex + aperçu Google
  seo_social:
    display: 'Réseaux sociaux'
    sections:
      - fields:
          - import: seo_social   # image, og_title, og_description, twitter_card + aperçu social
```
Fournit, **par page** : `seo_title`, `seo_description`, `seo_noindex`, `seo_image`,
`og_title`, `og_description`, `twitter_card`.

## Assets du panneau d'admin (aperçus Vue)

Les deux aperçus sont des **fieldtypes Vue** (`seo_google_preview`, `seo_social_preview`)
compilés dans `resources/dist/js/cp.js` (livré dans le dépôt). Après un `composer require`,
publier le bundle dans `public/vendor` :
```bash
php artisan vendor:publish --tag=statamic-seo --force
```
Pour **modifier** les composants (`resources/js/`), rebuild :
```bash
cd addons/statamic-seo
npm install --install-links     # installe @statamic/cms (Vue externalisé) + @vitejs/plugin-vue
npm run build                   # -> resources/dist/js/cp.js
```
Le serveur de prod n'a pas besoin de Node : `cp.js` est versionné et republié via
`vendor:publish`.

**3. Défauts du site** : `config/foorintodev-seo.php`
- `site_name`, `title_separator`, `append_site_name`
- `default_description`, `default_og_image`, `og_type`, `twitter_handle`
- `noindex_site` / `nofollow_site` (interrupteur de secours tout le site)
- `sites` : surcharges par site multisite (ex. description par langue)

## Redirections automatiques (301)

Quand le **slug d'une page change**, l'ancienne URL peut être redirigée automatiquement
vers la nouvelle (301) — fini les 404 après un renommage.

- Ajouter le fieldset `redirect` (case **« Créer une redirection si le slug change »**,
  activée par défaut) à un blueprint, typiquement dans l'onglet Réglages :
  ```yaml
  - import: redirect
  ```
- À l'enregistrement, si la case est cochée et que le slug a changé, une règle
  `ancien_chemin → nouveau_chemin` est écrite dans `content/redirects.yaml`.
- Un middleware sert la redirection 301. Les chaînes sont aplaties automatiquement
  (A→B puis B→C donne A→C).
- **Collections structurées** : renommer une page parente redirige aussi **tous ses
  descendants** (leur URL change car le segment parent change), à tous les niveaux.

Fichier de stockage configurable via `redirects_path` (défaut `content/redirects.yaml`).
Les chemins incluent le préfixe de langue (`/en/…`) : compatible multisite.

**Gestion dans le CP** : une page **Outils → Redirections** liste toutes les
redirections, permet d'en **ajouter** à la main et d'en **supprimer**.

## Page 404 gérable dans le CMS

L'addon fournit une page 404 dont le **texte se modifie dans le CP** (global
**« Pages d'erreur »**), rendue automatiquement par Statamic via la vue `errors.404`.

- `php artisan vendor:publish --tag=foorintodev-seo` publie un **modèle 404 autonome**
  (`resources/views/errors/404.antlers.html`) et le **blueprint du global**
  (`resources/blueprints/globals/errors.yaml` : surtitre, titre, message, libellé du bouton).
- Créez le global `errors` (localisable par site) et éditez son contenu dans le CP.
- La 404 hérite automatiquement de votre `layout` (nav + pied de page) et passe en
  **noindex** (le tag `{{ seo }}` détecte `response_code >= 400`).

Restylez librement le modèle publié pour coller à votre charte.

## Indexation (robots)

Le pilotage se fait **page par page** dans l'onglet SEO : le toggle **« Masquer aux
moteurs (noindex) »**. Coché = la page est exclue de Google ; décoché = indexable.

`noindex_site` (config) reste dispo comme **interrupteur de secours** pour forcer tout
le site en noindex d'un coup — à laisser `false` en usage normal.
