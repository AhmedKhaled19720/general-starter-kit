<script setup lang="ts">
import KbdHint from '@/components/KbdHint.vue';
import { ArrowLeft, ArrowRight, Save, UserCog, X } from '@lucide/vue';
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
    user: {
        id: number;
        name: string;
        username: string | null;
        avatar: string | null;
        email: string;
        role: string | null;
    };
    roles: Array<{ id: number; name: string }>;
}>();

const roleOptions = computed(() =>
    props.roles.map((role) => ({ value: role.name, label: role.name })),
);

const form = useForm({
    name: props.user.name,
    username: props.user.username ?? '',
    avatar: props.user.avatar,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    role: props.user.role ?? '',
    next: '',
});

function submit(next = false): void {
    form.next = next ? 'continue' : '';
    form.put('/users/' + props.user.id);
}
</script>

<template>
    <div class="space-y-5">
        <PageHeader
            :icon="UserCog"
            :title="$t('Edit') + ' ' + props.user.name"
            :description="$t('Update user details and role')"
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
                        <InputLabel value="New password (optional)" />
                        <TextInput
                            v-model="form.password"
                            type="password"
                            class="mt-1 w-full"
                            autocomplete="new-password"
                        />
                        <p class="mt-1 text-xs text-muted-foreground">
                            {{
                                $t('Leave empty to keep the current password.')
                            }}
                        </p>
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
                        <Save class="size-3.5" />
                        {{ $t('Save changes') }}
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
