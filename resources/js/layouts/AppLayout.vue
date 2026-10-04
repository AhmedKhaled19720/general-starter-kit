<script setup lang="ts">
import StarterLayout from '@/layouts/StarterLayout.vue';
import type { BreadcrumbItem } from '@/types';
defineProps<{ breadcrumbs?: BreadcrumbItem[] }>();
</script>
<template>
    <StarterLayout :breadcrumbs="breadcrumbs"><slot /></StarterLayout>
</template>
