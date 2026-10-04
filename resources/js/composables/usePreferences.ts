import { router, usePage } from '@inertiajs/vue3';
import { computed, shallowRef } from 'vue';
import type { Preferences } from '@/types/starter';

export function usePreferences() {
    const page = usePage();
    const saving = shallowRef(false);
    const preferences = computed(() => page.props.preferences);
    function update(values: Partial<Preferences>) {
        if (saving.value) return;
        saving.value = true;
        router.patch(
            page.props.auth.user ? '/settings/preferences' : '/language',
            values,
            {
                preserveScroll: true,
                onFinish: () => {
                    saving.value = false;
                },
            },
        );
    }
    return { preferences, saving, update };
}
