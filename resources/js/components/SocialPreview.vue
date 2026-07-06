<template>
    <div :style="{ maxWidth: '560px' }">
        <div :style="s.tabs">
            <button
                v-for="n in networks"
                :key="n.key"
                type="button"
                @click="network = n.key"
                :style="network === n.key ? s.tabOn : s.tab"
            >{{ n.label }}</button>
        </div>

        <!-- Twitter vignette : image à gauche -->
        <div v-if="isSummary" :style="s.summaryCard">
            <div :style="summaryImg"></div>
            <div :style="s.summaryBody">
                <div :style="s.domainMuted">{{ seo.host }}</div>
                <div :style="s.title">{{ title }}</div>
                <div :style="s.desc">{{ description }}</div>
            </div>
        </div>

        <!-- Grande carte : image en haut -->
        <div v-else :style="cardStyle">
            <div v-if="seo.image" :style="bigImg"></div>
            <div v-else :style="s.placeholder">Ajoutez une image de partage (1200×630)</div>
            <div :style="bodyStyle">
                <div v-if="network === 'facebook'" :style="s.domainCaps">{{ seo.host }}</div>
                <div :style="s.title">{{ title }}</div>
                <div :style="s.desc">{{ description }}</div>
                <div v-if="network !== 'facebook'" :style="s.domainMuted">{{ seo.host }}</div>
            </div>
        </div>
    </div>
</template>

<script>
import { FieldtypeMixin } from '@statamic/cms';
import { resolveSeo } from '../seo.js';

export default {
    mixins: [FieldtypeMixin],

    data() {
        return {
            network: 'facebook',
            networks: [
                { key: 'facebook', label: 'Facebook' },
                { key: 'twitter', label: 'X / Twitter' },
                { key: 'linkedin', label: 'LinkedIn' },
            ],
        };
    },

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
        title() {
            return this.seo.ogTitle || 'Titre du partage';
        },
        description() {
            return this.seo.ogDescription || 'La description du partage apparaîtra ici.';
        },
        isSummary() {
            return this.network === 'twitter' && this.seo.twitterCard === 'summary';
        },
        cardStyle() {
            const base = { overflow: 'hidden', background: '#fff', border: '1px solid #dfe1e5' };
            if (this.network === 'twitter') return { ...base, borderRadius: '16px' };
            if (this.network === 'linkedin') return { ...base, borderRadius: '4px', boxShadow: '0 0 0 1px rgba(0,0,0,.08)' };
            return { ...base, borderRadius: '8px' }; // facebook
        },
        bodyStyle() {
            if (this.network === 'facebook') return { ...this.s.body, background: '#f2f3f5' };
            return this.s.body;
        },
        bigImg() {
            return {
                paddingTop: '52.35%',
                backgroundImage: `url('${this.seo.image}')`,
                backgroundSize: 'cover',
                backgroundPosition: 'center',
            };
        },
        summaryImg() {
            return {
                width: '128px', flex: '0 0 128px', alignSelf: 'stretch',
                backgroundImage: this.seo.image ? `url('${this.seo.image}')` : 'none',
                backgroundColor: '#e1e8ed',
                backgroundSize: 'cover', backgroundPosition: 'center',
            };
        },
        s() {
            return {
                tabs: { display: 'flex', gap: '6px', marginBottom: '12px' },
                tab: {
                    padding: '5px 12px', fontSize: '13px', borderRadius: '999px', cursor: 'pointer',
                    border: '1px solid #d5d9e0', background: '#fff', color: '#4d5156',
                },
                tabOn: {
                    padding: '5px 12px', fontSize: '13px', borderRadius: '999px', cursor: 'pointer',
                    border: '1px solid #1a0dab', background: '#1a0dab', color: '#fff',
                },
                body: { padding: '12px 14px', fontFamily: 'Helvetica, Arial, sans-serif' },
                placeholder: {
                    paddingTop: '52.35%', position: 'relative', background: '#e9ebee', color: '#90949c',
                    fontSize: '13px', textAlign: 'center',
                },
                title: { fontSize: '16px', fontWeight: '700', color: '#1d2129', lineHeight: '1.3', margin: '2px 0' },
                desc: { fontSize: '14px', color: '#606770', lineHeight: '1.4', marginTop: '3px' },
                domainCaps: { fontSize: '12px', textTransform: 'uppercase', color: '#606770', letterSpacing: '.3px' },
                domainMuted: { fontSize: '13px', color: '#8899a6', marginTop: '4px' },
                summaryCard: {
                    display: 'flex', border: '1px solid #dfe1e5', borderRadius: '16px', overflow: 'hidden', background: '#fff',
                },
                summaryBody: { padding: '10px 14px', fontFamily: 'Helvetica, Arial, sans-serif' },
            };
        },
    },
};
</script>
