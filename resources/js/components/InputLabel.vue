<script setup lang="ts">
defineProps<{
    value?: string;
}>();
</script>

<template>
    <label class="block text-sm font-medium text-foreground/80">
        <slot>{{ value }}</slot>
    </label>
</template>
