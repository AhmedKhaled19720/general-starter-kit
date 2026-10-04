import { usePage } from '@inertiajs/vue3';
import type { App } from 'vue';
import ar from '@/locales/ar';
export function translate(key: string): string {
    const locale =
        typeof document === 'undefined' ? 'en' : document.documentElement.lang;
    return locale === 'ar' ? (ar[key.replace(/\s+/g, ' ').trim()] ?? key) : key;
}
export const i18nPlugin = {
    install(app: App) {
        app.config.globalProperties.$t = (key: string): string => {
            const locale = usePage().props.preferences?.locale;
            return locale === 'ar' ? (ar[key] ?? key) : key;
        };
    },
};
