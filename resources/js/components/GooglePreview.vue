<template>
    <div :style="s.wrap">
        <div :style="s.head">
            <div :style="s.favicon">{{ initial }}</div>
            <div style="line-height:1.3">
                <div :style="s.site">{{ seo.siteName || 'Votre site' }}</div>
                <div :style="s.url">{{ seo.host || 'exemple.com' }}</div>
            </div>
        </div>
        <div :style="s.title">{{ seo.title || 'Titre de la page' }}</div>
        <div :style="s.desc">{{ seo.description || 'La méta description apparaîtra ici. Remplissez le champ ci-dessous pour la personnaliser.' }}</div>
    </div>
</template>

<script>
import { FieldtypeMixin } from '@statamic/cms';
import { resolveSeo } from '../seo.js';

export default {
    mixins: [FieldtypeMixin],

    computed: {
        values() {
            return (this.publishContainer && this.publishContainer.values) || {};
        },
        siteHandle() {
            const s = this.publishContainer && this.publishContainer.site;
            if (!s) return '';
            return typeof s === 'string' ? s : (s.handle || s.value || '');
        },
        seo() {
            return resolveSeo(this.meta || {}, this.values, this.siteHandle);
        },
        initial() {
            return (this.seo.siteName || 'S').charAt(0).toUpperCase();
        },
        s() {
            return {
                wrap: {
                    maxWidth: '600px', padding: '18px 20px', border: '1px solid #dfe1e5',
                    borderRadius: '10px', background: '#fff', fontFamily: 'arial, sans-serif',
                },
                head: { display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '8px' },
                favicon: {
                    width: '26px', height: '26px', borderRadius: '50%', background: '#f1f3f4',
                    color: '#5f6368', display: 'flex', alignItems: 'center', justifyContent: 'center',
                    fontSize: '13px', fontWeight: '700', flex: '0 0 auto',
                },
                site: { fontSize: '14px', color: '#202124' },
                url: { fontSize: '12px', color: '#4d5156' },
                title: { fontSize: '20px', lineHeight: '1.3', color: '#1a0dab', marginBottom: '4px' },
                desc: { fontSize: '14px', lineHeight: '1.58', color: '#4d5156' },
            };
        },
    },
};
</script>
