import { usePage } from '@inertiajs/vue3';
import type { Auth } from '@/types/auth';

/**
 * Check whether the current user has the given permission.
 * The super admin (isSuperAdmin) always passes.
 */
export function useCan(): (permission: string) => boolean {
    const page = usePage();

    return (permission: string): boolean => {
        const auth = page.props.auth as Auth | undefined;

        if (!auth) {
            return false;
        }

        if (auth.isSuperAdmin) {
            return true;
        }

        return (auth.permissions ?? []).includes(permission);
    };
}
