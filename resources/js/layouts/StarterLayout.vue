<script setup lang="ts">
import { computed, shallowRef, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import StarterNavigation from '@/components/StarterNavigation.vue';
import StarterToolbar from '@/components/StarterToolbar.vue';
import ConfirmModal from '@/components/ConfirmModal.vue';
import ShortcutsHelp from '@/components/ShortcutsHelp.vue';
import ToastHost from '@/components/ToastHost.vue';
import { useFlashToast } from '@/composables/useFlashToast';
import { useKeyboardShortcuts } from '@/composables/useKeyboardShortcuts';
import { useProjectTheme } from '@/composables/useProjectTheme';
import type { BreadcrumbItem } from '@/types';
defineProps<{ breadcrumbs?: BreadcrumbItem[] }>();
const page = usePage();
const mobileOpen = shallowRef(false);
const sidebar = computed(() => page.props.preferences.layout === 'sidebar');
watch(
    () => page.url,
    () => {
        mobileOpen.value = false;
    },
);
useFlashToast();
useKeyboardShortcuts();
useProjectTheme();
</script>

<template>
    <div
        class="min-h-screen bg-background text-foreground"
        :class="sidebar ? 'lg:flex' : ''"
    >
        <aside
            v-if="sidebar"
            class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col gap-8 border-e border-line bg-card p-5 lg:flex"
        >
            <Link href="/dashboard"><AppLogo /></Link>
            <StarterNavigation vertical />
            <Link
                href="/settings/appearance"
                class="mt-auto rounded-md p-3 text-sm hover:bg-emerald-soft"
                >{{ $t('Appearance settings') }}</Link
            >
        </aside>
        <div class="min-w-0 flex-1">
            <header class="sticky top-0 z-30 border-b border-line bg-card">
                <div
                    class="mx-auto flex min-h-14 max-w-7xl items-center gap-3 px-4 sm:px-6"
                >
                    <button
                        type="button"
                        class="rounded-md p-2 hover:bg-emerald-soft focus-visible:outline-2 focus-visible:outline-primary lg:hidden"
                        :aria-expanded="mobileOpen"
                        aria-controls="mobile-navigation"
                        :aria-label="$t('Navigation')"
                        @click="mobileOpen = !mobileOpen"
                    >
                        <X v-if="mobileOpen" class="size-5" /><Menu
                            v-else
                            class="size-5"
                        />
                    </button>
                    <Link href="/dashboard" :class="sidebar ? 'lg:hidden' : ''"
                        ><AppLogo
                    /></Link>
                    <StarterNavigation v-if="!sidebar" class="hidden lg:flex" />
                    <div class="ms-auto"><StarterToolbar /></div>
                </div>
                <div
                    v-if="mobileOpen"
                    id="mobile-navigation"
                    class="border-t border-line p-4 lg:hidden"
                >
                    <StarterNavigation vertical />
                </div>
            </header>
            <main id="main-content" class="mx-auto max-w-7xl px-4 py-6 sm:px-6">
                <slot />
            </main>
        </div>
        <ToastHost /><ConfirmModal /><ShortcutsHelp />
    </div>
</template>
