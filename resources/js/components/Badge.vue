<script setup lang="ts">
type Variant =
    | 'success'
    | 'neutral'
    | 'outline'
    | 'warning'
    | 'danger'
    | 'primary';

withDefaults(defineProps<{ variant?: Variant }>(), {
    variant: 'neutral',
});
</script>

<template>
    <span
        class="inline-flex items-center rounded-full border px-3 py-1 text-xs whitespace-nowrap"
        :class="{
            'border-emerald/40 bg-emerald-soft font-semibold text-primary dark:text-primary':
                variant === 'success',
            'border-line bg-muted font-medium text-foreground/75':
                variant === 'neutral',
            'border-line bg-background font-semibold text-foreground/75':
                variant === 'outline',
            'border-warning/40 bg-warning-soft font-semibold text-warning dark:text-amber-300':
                variant === 'warning',
            'border-danger/40 bg-danger-soft font-semibold text-danger dark:text-red-400':
                variant === 'danger',
            'border-primary bg-primary font-semibold text-primary-foreground':
                variant === 'primary',
        }"
    >
        <slot />
    </span>
</template>
