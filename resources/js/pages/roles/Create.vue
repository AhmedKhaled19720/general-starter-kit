<script setup lang="ts">
import KbdHint from '@/components/KbdHint.vue';
import { ArrowLeft, ArrowRight, Plus, X } from '@lucide/vue';
import { Link, useForm } from '@inertiajs/vue3';
import Card from '@/components/Card.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PageHeader from '@/components/PageHeader.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';

defineProps<{
    permissions: Record<string, string[]>;
}>();

const form = useForm({
    name: '',
    permissions: [] as string[],
    next: '',
});

function toggle(permission: string): void {
    const index = form.permissions.indexOf(permission);

    if (index === -1) {
        form.permissions.push(permission);
    } else {
        form.permissions.splice(index, 1);
    }
}

function submit(next = false): void {
    form.next = next ? 'continue' : '';

    if (next) {
        form.post('/roles', { onSuccess: () => form.reset() });
    } else {
        form.post('/roles');
    }
}
</script>

<template>
    <div class="space-y-5">
        <PageHeader
            :icon="Plus"
            :title="$t('Add role')"
            :description="$t('Create a role and pick its permissions')"
        >
            <Link
                href="/roles"
                class="inline-flex items-center gap-2 rounded-md border border-line bg-card px-4 py-2 text-xs font-semibold tracking-widest text-foreground/80 uppercase transition hover:bg-emerald-soft hover:text-primary"
            >
                <ArrowLeft class="size-3.5" />
                {{ $t('Back') }}
            </Link>
        </PageHeader>

        <Card>
            <form class="space-y-4" @submit.prevent="submit()">
                <div class="max-w-sm">
                    <InputLabel :value="$t('Role name')" />
                    <TextInput
                        v-model="form.name"
                        class="mt-1 w-full"
                        placeholder="e.g. supervisor"
                    />
                    <InputError class="mt-1" :message="form.errors.name" />
                </div>

                <div class="border-t border-line pt-4">
                    <InputLabel :value="$t('Permissions')" />
                    <div class="mt-3 grid gap-5 sm:grid-cols-2">
                        <div
                            v-for="(
                                groupPermissions, group
                            ) in $props.permissions"
                            :key="group"
                            class="rounded-lg border border-line bg-background p-3"
                        >
                            <p
                                class="mb-2 text-xs font-semibold text-muted-foreground uppercase"
                            >
                                {{ group }}
                            </p>
                            <div class="space-y-1.5">
                                <label
                                    v-for="permission in groupPermissions"
                                    :key="permission"
                                    class="flex cursor-pointer items-center gap-2 text-sm text-foreground/80"
                                >
                                    <input
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-line text-primary focus:ring-primary"
                                        :checked="
                                            form.permissions.includes(
                                                permission,
                                            )
                                        "
                                        @change="toggle(permission)"
                                    />
                                    {{ permission }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <InputError
                        class="mt-1"
                        :message="form.errors.permissions"
                    />
                </div>

                <div class="flex items-center gap-3 border-t border-line pt-4">
                    <PrimaryButton :disabled="form.processing">
                        <Plus class="size-3.5" />
                        {{ $t('Create role') }}
                        <KbdHint :keys="['CTRL', 'S']" tone="on-dark" />
                    </PrimaryButton>
                    <SecondaryButton
                        type="button"
                        :disabled="form.processing"
                        @click="submit(true)"
                    >
                        <ArrowRight class="size-3.5" />
                        {{ $t('Save & Continue') }}
                    </SecondaryButton>
                    <SecondaryButton
                        type="button"
                        @click="$inertia.visit('/roles')"
                    >
                        <X class="size-3.5" />
                        {{ $t('Cancel') }}
                    </SecondaryButton>
                </div>
            </form>
        </Card>
    </div>
</template>
