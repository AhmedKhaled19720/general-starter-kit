<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

type LinkItem = {
    url: string | null;
    label: string;
    active: boolean;
};

defineProps<{
    links: LinkItem[];
}>();
</script>

<template>
    <nav
        v-if="links.length > 3"
        class="flex flex-wrap items-center justify-end gap-1"
    >
        <template v-for="(link, index) in links" :key="index">
            <Link
                v-if="link.url"
                :href="link.url"
                class="rounded-md border px-3 py-1.5 text-sm font-medium transition"
                :class="
                    link.active
                        ? 'border-primary bg-primary text-primary-foreground'
                        : 'border-line bg-card text-foreground/75 hover:bg-emerald-soft hover:text-primary'
                "
                v-html="link.label"
            />
            <span
                v-else
                class="rounded-md border border-line bg-card px-3 py-1.5 text-sm font-medium text-muted-foreground/60"
                v-html="link.label"
            />
        </template>
    </nav>
</template>
