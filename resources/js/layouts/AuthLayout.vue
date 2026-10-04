<script setup lang="ts">
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { useProjectTheme } from '@/composables/useProjectTheme';
useProjectTheme();
import AuthLayout from '@/layouts/auth/AuthCardLayout.vue';

const { title = '', description = '' } = defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <AuthLayout :title="title" :description="description">
        <div class="mb-4 flex justify-end"><LanguageSwitcher /></div>
        <slot />
    </AuthLayout>
</template>
