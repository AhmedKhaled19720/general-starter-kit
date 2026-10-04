<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    History,
    LayoutDashboard,
    Palette,
    ShieldCheck,
    Users,
    KeyRound,
} from '@lucide/vue';
import type { Component } from 'vue';
defineProps<{ vertical?: boolean }>();
const page = usePage();
const icons: Record<string, Component> = {
    dashboard: LayoutDashboard,
    users: Users,
    roles: ShieldCheck,
    permissions: KeyRound,
    activity: History,
    theme: Palette,
};
function active(href: string) {
    return page.url.split('?')[0] === href || page.url.startsWith(href + '/');
}
</script>

<template>
    <nav
        :aria-label="$t('Navigation')"
        class="flex gap-1"
        :class="vertical ? 'flex-col' : 'flex-wrap'"
    >
        <Link
            v-for="item in page.props.navigation"
            :key="item.href"
            :href="item.href"
            class="flex min-h-10 items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold transition-colors hover:bg-emerald-soft hover:text-primary focus-visible:outline-2 focus-visible:outline-primary"
            :class="
                active(item.href)
                    ? 'bg-emerald-soft text-primary'
                    : 'text-foreground/80'
            "
            :aria-current="active(item.href) ? 'page' : undefined"
        >
            <component
                :is="icons[item.icon] || LayoutDashboard"
                class="size-4 shrink-0"
                aria-hidden="true"
            />
            {{ $t(item.label) }}
        </Link>
    </nav>
</template>
