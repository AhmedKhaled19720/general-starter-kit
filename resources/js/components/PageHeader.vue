<script setup lang="ts">
import type { Component } from 'vue';

defineProps<{
    title: string;
    description?: string;
    icon?: Component;
}>();
</script>

<template>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1
                class="flex items-center gap-2 text-xl font-bold text-foreground"
            >
                <component
                    :is="icon"
                    v-if="icon"
                    class="size-5 shrink-0 text-primary"
                />
                {{ $t(title ?? '') }}
            </h1>
            <p v-if="description" class="mt-1 text-sm text-muted-foreground">
                {{ $t(description ?? '') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <slot />
        </div>
    </div>
</template>
