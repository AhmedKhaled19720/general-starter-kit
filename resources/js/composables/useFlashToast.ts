import { router, usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { confirm } from '@/composables/useConfirm';
import { translate } from '@/lib/i18n';
import { toast } from '@/utils/toast';

/**
 * Shows a toast whenever the server flashes a success/error message.
 * Mount once per app layout.
 */
export function useFlashToast(): void {
    const page = usePage();

    watch(
        () => [page.props.flash?.success, page.props.flash?.error] as const,
        ([success, error]) => {
            if (success) {
                toast(translate(success));
            }

            if (error) {
                toast(translate(error));
            }
        },
        { immediate: true },
    );
}

/**
 * Run an Inertia delete request after the shared confirm dialog.
 */
export async function confirmDelete(
    url: string,
    message = 'Are you sure?',
): Promise<void> {
    const approved = await confirm(translate(message), {
        title: translate('Delete'),
        confirmLabel: translate('Delete'),
    });

    if (approved) {
        router.delete(url, { preserveScroll: true });
    }
}
