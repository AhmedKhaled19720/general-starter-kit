<script setup lang="ts">
import type { Component } from 'vue';

defineProps<{
    title?: string;
    description?: string;
    icon?: Component;
}>();
</script>

<template>
    <section class="rounded-xl border border-line bg-card p-5">
        <div
            v-if="title || description || $slots.badge"
            class="flex flex-wrap items-start justify-between gap-3"
        >
            <div v-if="title || description">
                <h3
                    class="flex items-center gap-2 text-base font-semibold text-foreground"
                >
                    <component
                        :is="icon"
                        v-if="icon"
                        class="size-4 shrink-0 text-primary"
                    />
                    <slot name="title">{{ $t(title ?? '') }}</slot>
                </h3>
                <p
                    v-if="description || $slots.description"
                    class="mt-1 text-sm text-muted-foreground"
                >
                    <slot name="description">{{ $t(description ?? '') }}</slot>
                </p>
            </div>
            <slot name="badge" />
        </div>

        <div
            :class="{
                'mt-4':
                    title || description || $slots.badge || $slots.description,
            }"
        >
            <slot />
        </div>
    </section>
</template>
