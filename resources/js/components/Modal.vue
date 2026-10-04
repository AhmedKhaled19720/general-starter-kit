<script setup lang="ts">
import { X } from '@lucide/vue';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const maxWidths: Record<string, string> = {
    sm: '24rem',
    md: '28rem',
    lg: '32rem',
    xl: '36rem',
    '2xl': '42rem',
    '3xl': '48rem',
    '4xl': '56rem',
    '5xl': '64rem',
    '6xl': '72rem',
    '7xl': '80rem',
};

const props = withDefaults(
    defineProps<{
        show: boolean;
        title?: string;
        maxWidth?: string;
    }>(),
    {
        title: '',
        maxWidth: '2xl',
    },
);

const emit = defineEmits<{
    close: [];
}>();

const contentMaxWidth = computed(
    () => maxWidths[props.maxWidth] ?? props.maxWidth,
);

const dialog = ref<HTMLDialogElement | null>(null);

watch(
    () => props.show,
    (show) => {
        const element = dialog.value;

        if (!element) {
            return;
        }

        if (show && !element.open) {
            element.showModal();
        }

        if (!show && element.open) {
            element.close();
        }
    },
);

onBeforeUnmount(() => {
    const element = dialog.value;

    if (element?.open) {
        element.close();
    }
});

function onDialogClick(event: MouseEvent) {
    if (event.target === dialog.value) {
        emit('close');
    }
}
</script>

<template>
    <dialog
        ref="dialog"
        class="backdrop:bg-muted0/75 fixed inset-0 m-auto h-fit max-h-[calc(100dvh-2rem)] w-fit max-w-[calc(100%-2rem)] overflow-y-auto border-0 bg-transparent p-0"
        @cancel.prevent="emit('close')"
        @click="onDialogClick"
    >
        <div
            class="rounded-lg bg-card shadow-xl"
            :style="{ maxWidth: contentMaxWidth }"
        >
            <div
                class="flex items-start justify-between gap-4 border-b border-line px-6 py-4"
            >
                <h2 class="text-lg font-semibold text-foreground">
                    <slot name="title">{{ $t(title ?? '') }}</slot>
                </h2>
                <button
                    type="button"
                    class="rounded-md p-1 text-gray-400 transition hover:bg-muted hover:text-foreground/75"
                    @click="emit('close')"
                >
                    <X class="h-5 w-5" />
                    <span class="sr-only">
                        {{ $t('Close') }}
                    </span>
                </button>
            </div>

            <div class="max-h-[85vh] overflow-y-auto p-6">
                <slot />
            </div>

            <div
                v-if="$slots.footer"
                class="flex items-center justify-end gap-3 border-t border-line px-6 py-4"
            >
                <slot name="footer" />
            </div>
        </div>
    </dialog>
</template>

<style scoped>
dialog[open] > div {
    animation: modal-in 300ms ease-out;
}

@keyframes modal-in {
    from {
        opacity: 0;
        transform: translateY(1rem) scale(0.98);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
</style>
