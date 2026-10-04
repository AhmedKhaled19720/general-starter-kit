<script setup lang="ts">
const model = defineModel<string>();
</script>

<template>
    <textarea
        v-model="model"
        class="rounded-md border border-line bg-card px-3 py-2 text-sm text-foreground shadow-sm transition placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/30 disabled:bg-muted disabled:text-muted-foreground"
    ></textarea>
</template>
