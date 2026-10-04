<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
const page = usePage();
</script>
<template>
    <Head :title="$t('Dashboard')" />
    <div class="max-w-3xl space-y-8">
        <Heading
            :title="$t('Welcome') + '، ' + page.props.auth.user?.name"
            :description="$t('Your application starts here.')"
        />
        <section class="space-y-4 border-t border-line pt-6">
            <h2 class="text-lg font-semibold">{{ $t('Make it yours') }}</h2>
            <p class="text-sm leading-7 text-muted-foreground">
                {{
                    $t(
                        'Choose your navigation, language and appearance from your account settings.',
                    )
                }}
            </p>
            <Link
                href="/settings/appearance"
                class="inline-flex rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground hover:opacity-90"
                >{{ $t('Appearance settings') }}</Link
            >
        </section>
    </div>
</template>
