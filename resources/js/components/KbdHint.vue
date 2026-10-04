<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        keys: string[];
        tone?: 'on-dark' | 'on-light';
    }>(),
    { tone: 'on-light' },
);

const isMac =
    typeof navigator !== 'undefined' &&
    /Mac|iPhone|iPad|iPod/.test(navigator.userAgent);

const displayKeys = computed(() =>
    props.keys.map((key) => (key === 'CTRL' && isMac ? '⌘' : key)),
);
</script>

<template>
    <span class="hidden items-center gap-1 md:inline-flex" aria-hidden="true">
        <kbd
            v-for="key in displayKeys"
            :key="key"
            class="rounded border px-1.5 py-0.5 text-[10px] leading-none font-semibold"
            :class="
                tone === 'on-dark'
                    ? 'border-white/40 bg-white/15 text-white/90'
                    : 'border-line bg-muted text-foreground/75'
            "
        >
            {{ key }}
        </kbd>
    </span>
</template>
