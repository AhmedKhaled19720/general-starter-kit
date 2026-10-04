import { onBeforeUnmount, ref } from 'vue';

/**
 * Shared state for the keyboard shortcuts help overlay.
 * Mounted once by `useKeyboardShortcuts()` in the app layout.
 */
export const showShortcutsHelp = ref(false);

function isEditableTarget(target: EventTarget | null): boolean {
    if (!(target instanceof HTMLElement)) {
        return false;
    }

    return (
        target.isContentEditable ||
        target.tagName === 'INPUT' ||
        target.tagName === 'TEXTAREA' ||
        target.tagName === 'SELECT'
    );
}

function openDialog(): HTMLDialogElement | null {
    return document.querySelector('dialog[open]');
}

function visibleForms(scope: ParentNode): HTMLFormElement[] {
    return Array.from(scope.querySelectorAll('form')).filter(
        (form) => form.offsetParent !== null,
    );
}

/**
 * The form keyboard shortcuts act on: the one containing the focused element,
 * or the first visible form. While a dialog is open only its own forms are
 * considered, so background forms are never submitted behind a modal.
 */
function currentForm(): HTMLFormElement | null {
    const focused = document.activeElement;
    const focusedForm =
        focused instanceof HTMLElement ? focused.closest('form') : null;

    if (focusedForm instanceof HTMLFormElement) {
        return focusedForm;
    }

    const scope = openDialog() ?? document;
    return visibleForms(scope)[0] ?? null;
}

function disabledSubmitButton(form: HTMLFormElement): boolean {
    return Array.from(form.querySelectorAll('button')).some(
        (button) =>
            (button as HTMLButtonElement).type === 'submit' &&
            (button as HTMLButtonElement).disabled,
    );
}

function submitCurrentForm(): void {
    const form = currentForm();

    if (!form || disabledSubmitButton(form)) {
        return;
    }

    form.requestSubmit();
}

function clickSaveAndContinue(): void {
    const form = currentForm();
    const button = form
        ? Array.from(form.querySelectorAll('button')).find((candidate) =>
              /save\s*&\s*continue|حفظ وإضافة آخر/i.test(
                  candidate.textContent ?? '',
              ),
          )
        : undefined;

    if (button instanceof HTMLButtonElement && !button.disabled) {
        button.click();
    }
}

function onKeyDown(event: KeyboardEvent): void {
    if (event.defaultPrevented || event.repeat) {
        return;
    }

    const mod = event.ctrlKey || event.metaKey;

    // `event.code` matches the physical key, so the shortcuts keep working on
    // any keyboard layout (Arabic, etc.) and with Caps Lock on.
    if (mod && (event.code === 'KeyS' || event.key.toLowerCase() === 's')) {
        event.preventDefault();
        submitCurrentForm();
        return;
    }

    if (mod && (event.code === 'KeyD' || event.key.toLowerCase() === 'd')) {
        event.preventDefault();
        clickSaveAndContinue();
        return;
    }

    if (mod && (event.code === 'Slash' || event.key === '/')) {
        event.preventDefault();
        showShortcutsHelp.value = !showShortcutsHelp.value;
        return;
    }

    const helpKey =
        event.key === '?' ||
        event.key === '؟' ||
        (event.shiftKey && event.code === 'Slash');

    if (helpKey && !mod && !isEditableTarget(event.target)) {
        event.preventDefault();
        showShortcutsHelp.value = !showShortcutsHelp.value;
    }
}

/**
 * Global keyboard shortcuts (Ctrl/Cmd+S, Ctrl/Cmd+D, ? / Ctrl+/).
 * Call once from the app layout.
 */
export function useKeyboardShortcuts(): void {
    window.addEventListener('keydown', onKeyDown);

    onBeforeUnmount(() => {
        window.removeEventListener('keydown', onKeyDown);
    });
}
