<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import type { Palette, ProjectTheme } from '@/types/starter';
const props = defineProps<{ defaults: ProjectTheme }>();
const page = usePage();
const form = useForm({
    theme: {
        light: { ...page.props.theme.light },
        dark: { ...page.props.theme.dark },
    },
});
const modes = ['light', 'dark'] as const;
const fields: Array<{ key: keyof Palette; label: string }> = [
    { key: 'primary', label: 'Primary color' },
    { key: 'primary_foreground', label: 'Text on primary' },
    { key: 'background', label: 'Page background' },
    { key: 'foreground', label: 'Text color' },
    { key: 'card', label: 'Surface color' },
];
const preview = computed(() => ({
    backgroundColor: form.theme.light.background,
    color: form.theme.light.foreground,
}));
function reset() {
    form.theme = {
        light: { ...props.defaults.light },
        dark: { ...props.defaults.dark },
    };
}
function errorFor(mode: string, key: string): string | undefined {
    return (form.errors as Record<string, string>)[`theme.${mode}.${key}`];
}
</script>

<template>
    <Head :title="$t('Project colors')" />
    <div class="max-w-4xl space-y-8">
        <Heading
            :title="$t('Project colors')"
            :description="
                $t(
                    'Colors apply to everyone. Your layout and language remain personal.',
                )
            "
        />
        <form
            class="space-y-8"
            @submit.prevent="
                form.put('/project/theme', { preserveScroll: true })
            "
        >
            <div class="grid gap-8 md:grid-cols-2">
                <fieldset v-for="mode in modes" :key="mode" class="space-y-4">
                    <legend class="mb-4 text-lg font-semibold">
                        {{ $t(mode === 'light' ? 'Light' : 'Dark') }}
                    </legend>
                    <div v-for="field in fields" :key="field.key">
                        <label
                            :for="mode + '-' + field.key"
                            class="flex items-center justify-between gap-4 text-sm"
                        >
                            {{ $t(field.label) }}
                            <span class="flex items-center gap-2"
                                ><input
                                    :id="mode + '-' + field.key"
                                    type="color"
                                    v-model="form.theme[mode][field.key]"
                                    class="h-9 w-12 cursor-pointer rounded border border-line" /><input
                                    v-model="form.theme[mode][field.key]"
                                    :aria-label="$t(field.label) + ' HEX'"
                                    pattern="#[0-9a-fA-F]{6}"
                                    maxlength="7"
                                    class="h-9 w-24 rounded border border-line bg-card px-2 text-sm"
                                    dir="ltr"
                            /></span>
                        </label>
                        <InputError :message="errorFor(mode, field.key)" />
                    </div>
                </fieldset>
            </div>
            <section
                :style="preview"
                class="rounded-lg border border-line p-6"
                :aria-label="$t('Preview')"
            >
                <h2 class="text-lg font-semibold">{{ $t('Preview') }}</h2>
                <p class="mt-2 text-sm">
                    {{
                        $t('Check text readability before saving your palette.')
                    }}
                </p>
                <button
                    type="button"
                    class="mt-4 rounded-md px-4 py-2 text-sm font-semibold"
                    :style="{
                        backgroundColor: form.theme.light.primary,
                        color: form.theme.light.primary_foreground,
                    }"
                >
                    {{ $t('Primary button') }}
                </button>
            </section>
            <div class="flex flex-wrap items-center gap-3">
                <PrimaryButton :disabled="form.processing">{{
                    $t('Save colors')
                }}</PrimaryButton
                ><SecondaryButton
                    type="button"
                    :disabled="form.processing"
                    @click="reset"
                    >{{ $t('Reset to original colors') }}</SecondaryButton
                >
            </div>
        </form>
    </div>
</template>
