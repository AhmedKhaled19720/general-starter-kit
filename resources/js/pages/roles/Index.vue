<script setup lang="ts">
import { Pencil, Plus, Shield, Trash2 } from '@lucide/vue';
import { Link } from '@inertiajs/vue3';
import Badge from '@/components/Badge.vue';
import Card from '@/components/Card.vue';
import PageHeader from '@/components/PageHeader.vue';
import { confirmDelete } from '@/composables/useFlashToast';

defineProps<{
    roles: Array<{
        id: number;
        name: string;
        users_count: number;
        permissions: Array<{ id: number; name: string }>;
    }>;
    permissionCount: number;
}>();
</script>

<template>
    <div class="space-y-5">
        <PageHeader
            :icon="Shield"
            :title="$t('Roles')"
            description="Define roles and their permissions"
        >
            <Link
                href="/roles/create"
                class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-xs font-semibold tracking-widest text-primary-foreground uppercase transition hover:bg-primary/90"
            >
                <Plus class="size-3.5" />
                {{ $t('Add role') }}
            </Link>
        </PageHeader>

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr
                            class="border-b border-line text-xs text-muted-foreground uppercase"
                        >
                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('Role') }}
                            </th>
                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('Users') }}
                            </th>
                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('Permissions') }}
                            </th>
                            <th class="py-2 font-semibold">
                                {{ $t('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="role in $props.roles"
                            :key="role.id"
                            class="border-b border-line last:border-0 hover:bg-background"
                        >
                            <td
                                class="py-2.5 pe-4 font-semibold text-foreground"
                            >
                                {{ role.name }}
                            </td>
                            <td
                                class="py-2.5 pe-4 text-foreground/75 tabular-nums"
                            >
                                {{ role.users_count }}
                            </td>
                            <td class="py-2.5 pe-4">
                                <div class="flex flex-wrap gap-1.5">
                                    <Badge
                                        v-for="permission in role.permissions.slice(
                                            0,
                                            4,
                                        )"
                                        :key="permission.id"
                                        variant="outline"
                                    >
                                        {{ permission.name }}
                                    </Badge>
                                    <span
                                        v-if="role.permissions.length > 4"
                                        class="self-center text-xs text-muted-foreground"
                                    >
                                        +{{ role.permissions.length - 4 }}
                                        {{ $t('more') }}
                                    </span>
                                    <span
                                        v-if="role.permissions.length === 0"
                                        class="text-xs text-gray-400"
                                    >
                                        {{ $t('No permissions') }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-2.5">
                                <div
                                    class="flex items-center gap-3 text-sm font-medium"
                                >
                                    <Link
                                        v-if="role.name !== 'super-admin'"
                                        :href="'/roles/' + role.id + '/edit'"
                                        class="flex items-center gap-1 text-primary hover:underline"
                                    >
                                        <Pencil class="size-3.5" />
                                        {{ $t('Edit') }}
                                    </Link>
                                    <button
                                        v-if="role.name !== 'super-admin'"
                                        type="button"
                                        class="flex items-center gap-1 text-danger hover:underline"
                                        @click="
                                            confirmDelete(
                                                '/roles/' + role.id,
                                                'Delete this role? Users will lose its permissions.',
                                            )
                                        "
                                    >
                                        <Trash2 class="size-3.5" />
                                        {{ $t('Delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
    </div>
</template>
