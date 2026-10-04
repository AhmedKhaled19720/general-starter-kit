<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import PaginationLinks from '@/components/PaginationLinks.vue';
type Item = {
    id: string;
    data: { title: string; body: string; url?: string };
    read_at: string | null;
};
const props = defineProps<{
    items: {
        data: Item[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    unread: number;
}>();
const empty = computed(() => props.items.data.length === 0);
function safeUrl(url?: string) {
    return url?.startsWith('/') && !url.startsWith('//') ? url : null;
}
</script>

<template>
    <Head :title="$t('Notifications')" />
    <div class="max-w-3xl space-y-6">
        <Heading :title="$t('Notifications')" />
        <button
            v-if="unread"
            type="button"
            class="text-sm font-semibold text-primary hover:underline"
            @click="router.patch('/notifications/read-all')"
        >
            {{ $t('Mark all as read') }}
        </button>
        <p
            v-if="empty"
            class="rounded-lg border border-line p-8 text-muted-foreground"
        >
            {{ $t('No notifications yet.') }}
        </p>
        <ul v-else class="divide-y divide-line">
            <li
                v-for="item in items.data"
                :key="item.id"
                class="space-y-2 py-5"
            >
                <h2
                    class="font-semibold"
                    :class="item.read_at ? 'text-muted-foreground' : ''"
                >
                    {{ item.data.title }}
                </h2>
                <p class="text-sm text-muted-foreground">
                    {{ item.data.body }}
                </p>
                <div class="flex gap-4 text-sm">
                    <Link
                        v-if="safeUrl(item.data.url)"
                        :href="safeUrl(item.data.url)!"
                        class="text-primary hover:underline"
                        >{{ $t('View') }}</Link
                    ><button
                        v-if="!item.read_at"
                        type="button"
                        class="text-primary hover:underline"
                        @click="
                            router.patch('/notifications/' + item.id + '/read')
                        "
                    >
                        {{ $t('Mark as read') }}
                    </button>
                </div>
            </li>
        </ul>
        <PaginationLinks :links="items.links" />
    </div>
</template>
