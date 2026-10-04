<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

import { computed } from 'vue';
const page = usePage();
const name = computed(() => page.props.name);
</script>

<template>
    <span class="flex items-center gap-2">
        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="h-6 w-6 shrink-0 text-primary"
            aria-hidden="true"
        >
            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
        </svg>
        <span class="text-sm font-bold whitespace-nowrap text-primary">{{
            name
        }}</span>
    </span>
</template>
