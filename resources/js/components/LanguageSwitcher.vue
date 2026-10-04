<script setup lang="ts">
import { Languages } from '@lucide/vue';
import { usePreferences } from '@/composables/usePreferences';
const { preferences, saving, update } = usePreferences();
</script>

<template>
    <button
        type="button"
        class="inline-flex min-h-9 items-center gap-2 rounded-md px-3 text-sm hover:bg-emerald-soft focus-visible:outline-2 focus-visible:outline-primary disabled:opacity-50"
        :disabled="saving"
        :aria-label="$t('Change language')"
        @click="update({ locale: preferences.locale === 'ar' ? 'en' : 'ar' })"
    >
        <Languages class="size-4" aria-hidden="true" />
        <span lang="en" v-if="preferences.locale === 'ar'">English</span>
        <span lang="ar" v-else>العربية</span>
    </button>
</template>
