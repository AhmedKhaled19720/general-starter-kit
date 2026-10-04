<script setup lang="ts">
import KbdHint from '@/components/KbdHint.vue';
import { KeyRound, Plus, X } from '@lucide/vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Badge from '@/components/Badge.vue';
import Card from '@/components/Card.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import Modal from '@/components/Modal.vue';
import PageHeader from '@/components/PageHeader.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import TextInput from '@/components/TextInput.vue';

defineProps<{
    permissions: Array<{
        id: number;
        name: string;
        roles_count: number;
        created_at: string | null;
    }>;
}>();

const showModal = ref(false);

const form = useForm({
    name: '',
});

function openCreate(): void {
    form.reset();
    form.clearErrors();
    showModal.value = true;
}

function submit(): void {
    form.post('/permissions', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
}
</script>

<template>
    <div class="space-y-5">
        <PageHeader
            :icon="KeyRound"
            :title="$t('Permissions')"
            description="System permissions used by roles and routes"
        >
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-xs font-semibold tracking-widest text-primary-foreground uppercase transition hover:bg-primary/90"
                @click="openCreate"
            >
                <Plus class="size-3.5" />
                {{ $t('Add permission') }}
            </button>
        </PageHeader>

        <Card>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr
                            class="border-b border-line text-xs text-muted-foreground uppercase"
                        >
                            <th class="py-2 pe-4 font-semibold">
                                {{ $t('Permission') }}
                            </th>
                            <th class="py-2 font-semibold">
                                {{ $t('Roles') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="permission in $props.permissions"
                            :key="permission.id"
                            class="border-b border-line last:border-0 hover:bg-background"
                        >
                            <td class="py-2.5 pe-4 font-medium text-foreground">
                                {{ permission.name }}
                            </td>
                            <td class="py-2.5">
                                <Badge variant="outline">
                                    {{ permission.roles_count }}
                                </Badge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <Modal
            :show="showModal"
            :title="$t('Add permission')"
            @close="showModal = false"
        >
            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <InputLabel value="Permission name" />
                    <TextInput
                        v-model="form.name"
                        class="mt-1 w-full"
                        placeholder="e.g. reports.export"
                    />
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{
                            $t(
                                'Format: group.action — lowercase letters, numbers, dots and dashes.',
                            )
                        }}
                    </p>
                    <InputError class="mt-1" :message="form.errors.name" />
                </div>

                <div class="flex justify-end gap-3 border-t border-line pt-4">
                    <SecondaryButton type="button" @click="showModal = false">
                        <X class="size-3.5" />
                        {{ $t('Cancel') }}
                    </SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        <Plus class="size-3.5" />
                        {{ $t('Create') }}
                        <KbdHint :keys="['CTRL', 'S']" tone="on-dark" />
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </div>
</template>
