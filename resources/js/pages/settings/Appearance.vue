<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { LayoutPanelTop, PanelLeft, Sun, Moon, Monitor } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { usePreferences } from '@/composables/usePreferences';
const { preferences, saving, update } = usePreferences();
const layouts = [
    { value: 'navbar', icon: LayoutPanelTop, label: 'Top navigation' },
    { value: 'sidebar', icon: PanelLeft, label: 'Sidebar' },
] as const;
const modes = [
    { value: 'light', icon: Sun, label: 'Light' },
    { value: 'dark', icon: Moon, label: 'Dark' },
    { value: 'system', icon: Monitor, label: 'System' },
] as const;
</script>
<template>
    <Head :title="$t('Appearance settings')" />
    <div class="space-y-8">
        <Heading
            variant="small"
            :title="$t('Appearance settings')"
            :description="
                $t(
                    'These preferences are saved to your account across devices.',
                )
            "
        />
        <fieldset>
            <legend class="mb-3 text-sm font-semibold">
                {{ $t('Navigation layout') }}
            </legend>
            <div class="flex flex-wrap gap-3">
                <button
                    v-for="item in layouts"
                    :key="item.value"
                    type="button"
                    :disabled="saving"
                    :aria-pressed="preferences.layout === item.value"
                    class="flex min-h-12 items-center gap-3 rounded-md border px-4 py-3 text-sm focus-visible:outline-2 focus-visible:outline-primary disabled:opacity-50"
                    :class="
                        preferences.layout === item.value
                            ? 'border-primary bg-emerald-soft text-primary'
                            : 'border-line'
                    "
                    @click="update({ layout: item.value })"
                >
                    <component :is="item.icon" class="size-5" />{{
                        $t(item.label)
                    }}
                </button>
            </div>
        </fieldset>
        <fieldset>
            <legend class="mb-3 text-sm font-semibold">
                {{ $t('Color mode') }}
            </legend>
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="item in modes"
                    :key="item.value"
                    type="button"
                    :disabled="saving"
                    :aria-pressed="preferences.appearance === item.value"
                    class="flex items-center gap-2 rounded-md border px-4 py-2 text-sm disabled:opacity-50"
                    :class="
                        preferences.appearance === item.value
                            ? 'border-primary bg-emerald-soft text-primary'
                            : 'border-line'
                    "
                    @click="update({ appearance: item.value })"
                >
                    <component :is="item.icon" class="size-4" />{{
                        $t(item.label)
                    }}
                </button>
            </div>
        </fieldset>
        <fieldset>
            <legend class="mb-3 text-sm font-semibold">
                {{ $t('Language') }}
            </legend>
            <div class="flex gap-2">
                <button
                    v-for="locale in ['ar', 'en'] as const"
                    :key="locale"
                    type="button"
                    :disabled="saving"
                    :aria-pressed="preferences.locale === locale"
                    class="rounded-md border px-4 py-2 text-sm disabled:opacity-50"
                    :class="
                        preferences.locale === locale
                            ? 'border-primary bg-emerald-soft text-primary'
                            : 'border-line'
                    "
                    @click="update({ locale })"
                >
                    {{ locale === 'ar' ? 'العربية' : 'English' }}
                </button>
            </div>
        </fieldset>
    </div>
</template>
