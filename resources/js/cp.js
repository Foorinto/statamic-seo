import GooglePreview from './components/GooglePreview.vue';
import SocialPreview from './components/SocialPreview.vue';

// Le nom suit la convention Statamic : `{handle}-fieldtype`.
Statamic.$components.register('seo_google_preview-fieldtype', GooglePreview);
Statamic.$components.register('seo_social_preview-fieldtype', SocialPreview);
