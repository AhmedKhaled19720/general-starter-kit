<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Bell, Settings, LogOut } from '@lucide/vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { logout } from '@/routes';
const page = usePage();
</script>

<template>
    <div class="flex flex-wrap items-center justify-end gap-1">
        <LanguageSwitcher />
        <Link
            v-if="page.props.notifications.enabled"
            href="/notifications"
            class="rounded-md p-2 hover:bg-emerald-soft"
            :aria-label="$t('Notifications')"
            ><Bell class="size-4"
        /></Link>
        <Link
            href="/settings/appearance"
            class="rounded-md p-2 hover:bg-emerald-soft focus-visible:outline-2 focus-visible:outline-primary"
            :aria-label="$t('Settings')"
            ><Settings class="size-4"
        /></Link>
        <span class="hidden max-w-40 truncate px-2 text-sm sm:block">{{
            page.props.auth.user?.name
        }}</span>
        <Link
            :href="logout()"
            as="button"
            class="rounded-md p-2 hover:bg-emerald-soft focus-visible:outline-2 focus-visible:outline-primary"
            :aria-label="$t('Log out')"
            ><LogOut class="size-4"
        /></Link>
    </div>
</template>
