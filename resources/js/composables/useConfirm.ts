import { reactive } from 'vue';

type ConfirmOptions = {
    title?: string;
    confirmLabel?: string;
};

type ConfirmState = {
    show: boolean;
    title: string;
    message: string;
    confirmLabel: string;
    resolve: ((result: boolean) => void) | null;
};

const state = reactive<ConfirmState>({
    show: false,
    title: 'Confirm',
    message: '',
    confirmLabel: 'Confirm',
    resolve: null,
});

/**
 * Opens the global confirm dialog and resolves with the user's choice.
 */
export function confirm(
    message: string,
    options: ConfirmOptions = {},
): Promise<boolean> {
    state.resolve?.(false);

    state.title = options.title ?? 'Confirm';
    state.message = message;
    state.confirmLabel = options.confirmLabel ?? 'Confirm';
    state.show = true;

    return new Promise<boolean>((resolve) => {
        state.resolve = resolve;
    });
}

/**
 * Shared dialog state - read by the ConfirmModal mounted in the app layout.
 */
export function confirmState(): ConfirmState {
    return state;
}

/**
 * Closes the dialog and resolves the pending promise with the given result.
 */
export function settleConfirm(result: boolean): void {
    state.show = false;
    state.resolve?.(result);
    state.resolve = null;
}
