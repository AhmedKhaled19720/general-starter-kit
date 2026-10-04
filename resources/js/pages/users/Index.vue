<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { Check, Pencil, Plus, RotateCcw, Trash2, Users } from '@lucide/vue';
import { computed } from 'vue';
import Badge from '@/components/Badge.vue';
import Card from '@/components/Card.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import { confirmDelete } from '@/composables/useFlashToast';
import type { Auth } from '@/types/auth';
import { avatarUrl } from '@/utils/avatars';
import { formatDate } from '@/utils/format';

const props = defineProps<{
    users: Array<{
        id: number;
        name: string;
        username: string | null;
        avatar: string | null;
        email: string;
        role: string | null;
        is_super_admin: boolean;
        created_at: string | null;
        deleted_at: string | null;
    }>;
    trash: boolean;
}>();

const { getInitials } = useInitials();
const page = usePage();
const isSuperAdmin = computed(
    () => (page.props.auth as Auth | undefined)?.isSuperAdmin === true,
);

function setTrash(value: boolean): void {
    if (props.trash === value) {
        return;
    }
    router.get(
        '/users',
        { trash: value ? 1 : undefined },
        { preserveState: true, replace: true },
    );
}

function restoreUser(id: number): void {
    router.post('/users/' + id + '/restore');
}
</script>

<template>
    <div class="space-y-5">
        <PageHeader
            :icon="Users"
            :title="$t('Users')"
            description="Manage system users and their roles"
        >
            <Link
                v-if="!props.trash"
                href="/users/create"
                class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-xs font-semibold tracking-widest text-primary-foreground uppercase transition hover:bg-primary/90"
            >
                <Plus class="size-3.5" />
                {{ $t('Add user') }}
            </Link>
        </PageHeader>

        <Card>
            <div class="mb-4 flex items-center gap-2">
                <button
                    type="button"
                    class="flex items-center gap-1.5 rounded-md border border-line px-3 py-1.5 text-xs font-semibold tracking-widest uppercase transition"
                    :class="
                        !props.trash
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-card text-foreground/75 hover:bg-emerald-soft'
                    "
                    @click="setTrash(false)"
                >
                    <Check class="size-3.5" />
                    {{ $t('Active') }}
                </button>
                <button
                    type="button"
                    class="flex items-center gap-1.5 rounded-md border border-line px-3 py-1.5 text-xs font-semibold tracking-widest uppercase transition"
                    :class="
                        props.trash
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-card text-foreground/75 hover:bg-emerald-soft'
                    "
                    @click="setTrash(true)"
                >
                    <Trash2 class="size-3.5" />
                    {{ $t('Trash') }}
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr
                            class="border-b border-line text-xs text-muted-foreground uppercase"
                        >
                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('Name') }}
                            </th>
                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('Username') }}
                            </th>
                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('Email') }}
                            </th>
                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('Role') }}
                            </th>

                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('Created') }}
                            </th>
                            <th class="py-2 font-semibold">
                                {{ $t('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-if="props.users.length === 0"
                            class="border-b border-line"
                        >
                            <td
                                colspan="6"
                                class="py-6 text-center text-muted-foreground"
                            >
                                {{
                                    props.trash
                                        ? $t('Trash is empty.')
                                        : $t('No users found.')
                                }}
                            </td>
                        </tr>
                        <tr
                            v-for="user in $props.users"
                            :key="user.id"
                            class="border-b border-line last:border-0 hover:bg-background"
                        >
                            <td
                                class="py-2.5 pe-4 font-semibold text-foreground"
                            >
                                <span class="flex items-center gap-2">
                                    <Avatar class="h-7 w-7 rounded-full">
                                        <AvatarImage
                                            v-if="avatarUrl(user.avatar)"
                                            :src="avatarUrl(user.avatar)!"
                                            :alt="user.name"
                                        />
                                        <AvatarFallback
                                            class="rounded-full text-xs"
                                        >
                                            {{ getInitials(user.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    {{ user.name }}
                                    <span
                                        v-if="user.is_super_admin"
                                        class="ms-1 text-xs font-normal text-gray-400"
                                    >
                                        {{ $t('(super admin)') }}
                                    </span>
                                </span>
                            </td>
                            <td class="py-2.5 pe-4 text-foreground/75">
                                {{ user.username ?? '—' }}
                            </td>
                            <td class="py-2.5 pe-4 text-foreground/75">
                                {{ user.email }}
                            </td>
                            <td class="py-2.5 pe-4">
                                <Badge v-if="user.role" variant="primary">
                                    {{ user.role }}
                                </Badge>
                                <span v-else class="text-gray-400">—</span>
                            </td>
                            <td
                                class="py-2.5 pe-4 text-foreground/75 tabular-nums"
                            >
                                {{
                                    user.created_at
                                        ? formatDate(user.created_at)
                                        : '—'
                                }}
                            </td>
                            <td class="py-2.5">
                                <div
                                    v-if="props.trash"
                                    class="flex items-center gap-3 text-sm font-medium"
                                >
                                    <button
                                        type="button"
                                        class="flex items-center gap-1 text-primary hover:underline"
                                        @click="restoreUser(user.id)"
                                    >
                                        <RotateCcw class="size-3.5" />
                                        {{ $t('Restore') }}
                                    </button>
                                    <button
                                        v-if="
                                            isSuperAdmin && !user.is_super_admin
                                        "
                                        type="button"
                                        class="flex items-center gap-1 text-danger hover:underline"
                                        @click="
                                            confirmDelete(
                                                '/users/' + user.id + '/force',
                                                'Permanently delete this user? This cannot be undone.',
                                            )
                                        "
                                    >
                                        {{ $t('Delete forever') }}
                                    </button>
                                </div>
                                <div
                                    v-else
                                    class="flex items-center gap-3 text-sm font-medium"
                                >
                                    <Link
                                        :href="'/users/' + user.id + '/edit'"
                                        class="flex items-center gap-1 text-primary hover:underline"
                                    >
                                        <Pencil class="size-3.5" />
                                        {{ $t('Edit') }}
                                    </Link>
                                    <button
                                        v-if="!user.is_super_admin"
                                        type="button"
                                        class="flex items-center gap-1 text-danger hover:underline"
                                        @click="
                                            confirmDelete(
                                                '/users/' + user.id,
                                                'Delete this user?',
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
