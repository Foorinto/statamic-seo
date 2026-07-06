// Logique de repli partagée par les deux aperçus.
// Reproduit fidèlement le tag {{ seo }} (src/Tags/Seo.php) côté navigateur.

export function siteConfig(meta, siteHandle) {
    const over = (meta.sites && meta.sites[siteHandle]) || {};
    return {
        siteName: over.siteName || meta.siteName || '',
        separator: meta.separator || '·',
        appendSiteName: meta.appendSiteName !== false,
        defaultImage: over.defaultImage || meta.defaultImage || '',
        defaultDescription: over.defaultDescription || meta.defaultDescription || '',
        baseUrl: (meta.baseUrl || '').replace(/\/$/, ''),
    };
}

// Résout l'image de partage vers une URL affichable. Le sélecteur d'asset peut
// renvoyer : un chemin (string), un tableau de chemins, un objet asset {url,path},
// ou un identifiant « container::chemin ». Repli sur l'image par défaut du site.
// On garde des URLs RELATIVES (résolues contre l'hôte du panneau).
function resolveImage(value, c, meta) {
    let v = Array.isArray(value) ? value[0] : value;
    if (v && typeof v === 'object') {
        v = v.url || v.permalink || v.path || v.id || '';
    }
    v = (v || '').toString().trim();
    if (v.includes('::')) v = v.split('::').pop(); // "container::chemin" -> "chemin"
    if (!v) v = (c.defaultImage || '').toString().trim();
    if (!v) return '';
    if (/^https?:\/\//i.test(v)) return v;
    if (v.charAt(0) === '/') return v;
    const base = (meta.assetsBaseUrl || '').replace(/\/$/, '');
    return base ? base + '/' + v : '/' + v;
}

export function resolveSeo(meta, values, siteHandle) {
    const c = siteConfig(meta, siteHandle);
    values = values || {};

    const pageTitle = (values.seo_title || values.title || '').trim();
    let title;
    if (pageTitle === '') {
        title = c.siteName;
    } else if (c.appendSiteName && c.siteName && !pageTitle.includes(c.siteName)) {
        title = `${pageTitle} ${c.separator} ${c.siteName}`;
    } else {
        title = pageTitle;
    }

    const description = (values.seo_description || c.defaultDescription || '').trim();
    const ogTitle = (values.og_title || title || '').trim();
    const ogDescription = (values.og_description || description || '').trim();
    const image = resolveImage(values.seo_image, c, meta);

    const host = c.baseUrl.replace(/^https?:\/\//i, '');

    return {
        siteName: c.siteName,
        title,
        description,
        ogTitle,
        ogDescription,
        image,
        host,
        baseUrl: c.baseUrl,
        twitterCard: values.twitter_card || (image ? 'summary_large_image' : 'summary'),
    };
}
