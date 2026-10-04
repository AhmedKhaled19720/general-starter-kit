<script setup lang="ts">
import KbdHint from '@/components/KbdHint.vue';
import { ArrowLeft, ArrowRight, Plus, UserPlus, X } from '@lucide/vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AvatarPicker from '@/components/AvatarPicker.vue';
import Card from '@/components/Card.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PageHeader from '@/components/PageHeader.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import Select2 from '@/components/Select2.vue';
import TextInput from '@/components/TextInput.vue';

const props = defineProps<{
    roles: Array<{ id: number; name: string }>;
}>();

const roleOptions = computed(() =>
    props.roles.map((role) => ({ value: role.name, label: role.name })),
);

const form = useForm({
    name: '',
    username: '',
    avatar: null as string | null,
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    next: '',
});

function submit(next = false): void {
    form.next = next ? 'continue' : '';

    if (next) {
        form.post('/users', { onSuccess: () => form.reset() });
    } else {
        form.post('/users');
    }
}
</script>

<template>
    <div class="space-y-5">
        <PageHeader
            :icon="UserPlus"
            :title="$t('Add user')"
            :description="$t('Create a new user account')"
        >
            <Link
                href="/users"
                class="inline-flex items-center gap-2 rounded-md border border-line bg-card px-4 py-2 text-xs font-semibold tracking-widest text-foreground/80 uppercase transition hover:bg-emerald-soft hover:text-primary"
            >
                <ArrowLeft class="size-3.5" />
                {{ $t('Back') }}
            </Link>
        </PageHeader>

        <Card>
            <form class="space-y-4" @submit.prevent="submit()">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel :value="$t('Name')" />
                        <TextInput v-model="form.name" class="mt-1 w-full" />
                        <InputError class="mt-1" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel :value="$t('Username')" />
                        <TextInput
                            v-model="form.username"
                            class="mt-1 w-full"
                        />
                        <InputError
                            class="mt-1"
                            :message="form.errors.username"
                        />
                    </div>
                    <div>
                        <InputLabel :value="$t('Email')" />
                        <TextInput
                            v-model="form.email"
                            type="email"
                            class="mt-1 w-full"
                        />
                        <InputError class="mt-1" :message="form.errors.email" />
                    </div>
                    <div>
                        <InputLabel :value="$t('Password')" />
                        <TextInput
                            v-model="form.password"
                            type="password"
                            class="mt-1 w-full"
                            autocomplete="new-password"
                        />
                        <InputError
                            class="mt-1"
                            :message="form.errors.password"
                        />
                    </div>
                    <div>
                        <InputLabel :value="$t('Confirm password')" />
                        <TextInput
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 w-full"
                            autocomplete="new-password"
                        />
                    </div>
                    <div>
                        <InputLabel :value="$t('Role')" />
                        <Select2
                            v-model="form.role"
                            :options="roleOptions"
                            :placeholder="$t('Select role')"
                            :invalid="Boolean(form.errors.role)"
                            class="mt-1 w-full"
                        />
                        <InputError class="mt-1" :message="form.errors.role" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <InputLabel :value="$t('Avatar')" />
                    <AvatarPicker v-model="form.avatar" />
                    <InputError class="mt-1" :message="form.errors.avatar" />
                </div>

                <div class="flex items-center gap-3 border-t border-line pt-4">
                    <PrimaryButton :disabled="form.processing">
                        <Plus class="size-3.5" />
                        {{ $t('Create user') }}
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
                        @click="$inertia.visit('/users')"
                    >
                        <X class="size-3.5" />
                        {{ $t('Cancel') }}
                    </SecondaryButton>
                </div>
            </form>
        </Card>
    </div>
</template>
