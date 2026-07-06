import { defineConfig } from 'vite';
import statamic from '@statamic/cms/vite-plugin';

// Compile les composants Vue du panneau d'admin en UN seul fichier JS (IIFE),
// avec Vue externalisé sur le Vue global du CP (window.Vue) via le plugin Statamic.
// Sortie : resources/dist/js/cp.js — chargé par le ServiceProvider ($scripts).
export default defineConfig({
    plugins: [statamic()],
    build: {
        outDir: 'resources/dist/js',
        emptyOutDir: true,
        cssCodeSplit: false,
        lib: {
            entry: 'resources/js/cp.js',
            formats: ['iife'],
            name: 'FoorintodevSeoCp',
            fileName: () => 'cp.js',
        },
    },
});
