<template>
    <div
        class="pointer-events-none fixed inset-x-0 bottom-6 z-50 flex flex-col items-center gap-2"
        aria-live="polite"
    >
        <TransitionGroup name="toast">
            <div
                v-for="item in toasts"
                :key="item.id"
                class="pointer-events-auto rounded-lg border border-emerald/40 bg-card px-4 py-2.5 text-sm font-semibold text-primary shadow-lg"
            >
                {{ item.message }}
            </div>
        </TransitionGroup>
    </div>
</template>

<script setup lang="ts">
import { toasts } from '@/utils/toast';
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition:
        opacity 300ms ease-out,
        transform 300ms ease-out;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(0.75rem);
}
</style>
