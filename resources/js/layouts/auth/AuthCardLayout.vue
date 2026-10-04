<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-background pt-6 sm:justify-center sm:pt-0"
    >
        <Link :href="home()" class="flex items-center" :aria-label="$t('Home')">
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="h-20 w-20 text-muted-foreground"
                aria-hidden="true"
            >
                <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
            </svg>
        </Link>

        <div class="mt-6 w-full sm:max-w-md">
            <Card class="rounded-lg border-0 bg-card shadow-md">
                <CardHeader class="px-6 pt-6 pb-0 text-center">
                    <CardTitle class="text-xl font-semibold text-foreground">{{
                        $t(title ?? '')
                    }}</CardTitle>
                    <CardDescription class="text-sm text-muted-foreground">
                        {{ $t(description ?? '') }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="px-6 py-6">
                    <slot />
                </CardContent>
            </Card>
        </div>
    </div>
</template>
