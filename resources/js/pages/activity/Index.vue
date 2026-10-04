<script setup lang="ts">
import { History, SlidersHorizontal } from '@lucide/vue';
import { router } from '@inertiajs/vue3';
import { computed, reactive, watch } from 'vue';
import Badge from '@/components/Badge.vue';
import Card from '@/components/Card.vue';
import InputLabel from '@/components/InputLabel.vue';
import PageHeader from '@/components/PageHeader.vue';
import PaginationLinks from '@/components/PaginationLinks.vue';
import Select2 from '@/components/Select2.vue';
import TextInput from '@/components/TextInput.vue';
import { formatDateTime } from '@/utils/format';

type ActivityRow = {
    id: number;
    event: string;
    description: string | null;
    log_name: string | null;
    subject_type: string | null;
    subject_id: number | null;
    created_at: string;
    causer: { id: number; name: string } | null;
};

const props = defineProps<{
    activities: {
        data: ActivityRow[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: {
        causer_id: string | number | null;
        log_name: string | null;
        from: string | null;
        to: string | null;
    };
    users: Array<{ id: number; name: string }>;
    logNames: string[];
}>();

const filters = reactive({
    causer_id: props.filters.causer_id ?? '',
    log_name: props.filters.log_name ?? '',
    from: props.filters.from ?? '',
    to: props.filters.to ?? '',
});

const causerOptions = computed(() => [
    { value: '' as string | number, label: 'Anyone' },
    ...props.users.map((user) => ({ value: user.id, label: user.name })),
]);

const logNameOptions = computed(() => [
    { value: '', label: 'All types' },
    ...props.logNames.map((name) => ({ value: name, label: name })),
]);

let timer: ReturnType<typeof setTimeout> | null = null;

watch(
    () => [filters.causer_id, filters.log_name, filters.from, filters.to],
    () => {
        if (timer) {
            clearTimeout(timer);
        }
        timer = setTimeout(apply, 350);
    },
);

function apply(): void {
    router.get(
        '/activity-log',
        { ...filters },
        { preserveState: true, replace: true },
    );
}

function subjectLabel(activity: ActivityRow): string {
    if (!activity.subject_type) {
        return '—';
    }

    const type =
        activity.subject_type.split('\\').pop() ?? activity.subject_type;

    return `${type} #${activity.subject_id}`;
}
</script>

<template>
    <div class="space-y-5">
        <PageHeader
            :icon="History"
            :title="$t('Activity log')"
            description="Audit trail of all recorded changes"
        />

        <Card :icon="SlidersHorizontal" title="Filters">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <InputLabel :value="$t('User')" />
                    <Select2
                        v-model="filters.causer_id"
                        :options="causerOptions"
                        :placeholder="$t('Anyone')"
                        class="mt-1 w-full"
                    />
                </div>
                <div>
                    <InputLabel value="Type" />
                    <Select2
                        v-model="filters.log_name"
                        :options="logNameOptions"
                        :placeholder="$t('All types')"
                        class="mt-1 w-full"
                    />
                </div>
                <div>
                    <InputLabel :value="$t('From')" />
                    <TextInput
                        v-model="filters.from"
                        type="date"
                        class="mt-1 w-full"
                    />
                </div>
                <div>
                    <InputLabel :value="$t('To')" />
                    <TextInput
                        v-model="filters.to"
                        type="date"
                        class="mt-1 w-full"
                    />
                </div>
            </div>
        </Card>

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr
                            class="border-b border-line text-xs text-muted-foreground uppercase"
                        >
                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('Time') }}
                            </th>
                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('User') }}
                            </th>
                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('Type') }}
                            </th>
                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('Action') }}
                            </th>
                            <th class="py-2 font-semibold">
                                {{ $t('Subject') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-if="props.activities.data.length === 0"
                            class="border-b border-line"
                        >
                            <td
                                colspan="5"
                                class="py-6 text-center text-muted-foreground"
                            >
                                {{ $t('No activity recorded.') }}
                            </td>
                        </tr>
                        <tr
                            v-for="activity in props.activities.data"
                            :key="activity.id"
                            class="border-b border-line last:border-0 hover:bg-background"
                        >
                            <td
                                class="py-2.5 pe-4 whitespace-nowrap text-foreground/75 tabular-nums"
                            >
                                {{ formatDateTime(activity.created_at) }}
                            </td>
                            <td
                                class="py-2.5 pe-4 font-medium text-foreground/90"
                            >
                                {{ activity.causer?.name ?? 'System' }}
                            </td>
                            <td class="py-2.5 pe-4">
                                <Badge variant="outline">
                                    {{ activity.log_name ?? 'default' }}
                                </Badge>
                            </td>
                            <td class="py-2.5 pe-4 text-foreground/75">
                                {{ activity.description ?? activity.event }}
                            </td>
                            <td class="py-2.5 text-foreground/75">
                                {{ subjectLabel(activity) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <PaginationLinks :links="props.activities.links" />
            </div>
        </Card>
    </div>
</template>
