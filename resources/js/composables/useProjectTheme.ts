import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { updateTheme } from '@/composables/useAppearance';
import type { Palette } from '@/types/starter';

export function useProjectTheme() {
    const page = usePage();
    watch(
        () => [page.props.theme, page.props.preferences] as const,
        ([theme, preferences]) => {
            if (typeof document === 'undefined') return;
            document.documentElement.lang = preferences.locale;
            document.documentElement.dir =
                preferences.locale === 'ar' ? 'rtl' : 'ltr';
            document.documentElement.dataset.appearance =
                preferences.appearance;
            const selectors = { light: ':root', dark: ':root.dark' };
            const declarations = (palette: Palette) =>
                Object.entries(palette)
                    .map(
                        ([key, value]) =>
                            `--${key.replaceAll('_', '-')}:${value}`,
                    )
                    .join(';');
            let style = document.getElementById('project-theme');
            if (!style) {
                style = document.createElement('style');
                style.id = 'project-theme';
                document.head.append(style);
            }
            style.textContent = Object.entries(selectors)
                .map(
                    ([mode, selector]) =>
                        `${selector}{${declarations(theme[mode as keyof typeof theme])}}`,
                )
                .join('\n');
            updateTheme(preferences.appearance);
        },
        { immediate: true, deep: true },
    );
}
