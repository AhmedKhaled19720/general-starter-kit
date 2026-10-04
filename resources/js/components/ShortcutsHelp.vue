<script setup lang="ts">
import KbdHint from '@/components/KbdHint.vue';
import Modal from '@/components/Modal.vue';
import { showShortcutsHelp } from '@/composables/useKeyboardShortcuts';

const shortcuts: Array<{ label: string; keys: string[] }> = [
    { label: 'Save the open form', keys: ['CTRL', 'S'] },
    { label: 'Save & Continue', keys: ['CTRL', 'D'] },
    { label: 'Close dialogs', keys: ['ESC'] },
    { label: 'Show or hide this help', keys: ['?'] },
    { label: 'Show or hide this help', keys: ['CTRL', '/'] },
];
</script>

<template>
    <Modal
        :show="showShortcutsHelp"
        :title="$t('Keyboard shortcuts')"
        max-width="md"
        @close="showShortcutsHelp = false"
    >
        <ul class="divide-y divide-line">
            <li
                v-for="(shortcut, index) in shortcuts"
                :key="index"
                class="flex items-center justify-between gap-4 py-3"
            >
                <span class="text-sm text-foreground/80">{{
                    shortcut.label
                }}</span>
                <KbdHint :keys="shortcut.keys" tone="on-light" />
            </li>
        </ul>
        <p class="mt-4 text-xs text-muted-foreground">
            {{
                $t(
                    'Shortcuts work while you are typing in a form. On macOS use the ⌘ key instead of CTRL.',
                )
            }}
        </p>
    </Modal>
</template>
