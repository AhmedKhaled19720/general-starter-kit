<script setup lang="ts">
import KbdHint from '@/components/KbdHint.vue';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import AvatarPicker from '@/components/AvatarPicker.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Profile settings',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const avatar = ref<string | null>(user.value?.avatar ?? null);
</script>

<template>
    <Head :title="$t('Profile settings')" />

    <h1 class="sr-only">
        {{ $t('Profile settings') }}
    </h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            :title="$t('Profile')"
            description="Update your name, avatar, and email address"
        />

        <Form
            v-bind="ProfileController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <input type="hidden" name="avatar" :value="avatar ?? ''" />

            <div class="grid gap-2">
                <Label for="name">
                    {{ $t('Name') }}
                </Label>
                <Input
                    id="name"
                    class="mt-1 block w-full"
                    name="name"
                    :default-value="user?.name"
                    required
                    autocomplete="name"
                    placeholder="Full name"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">
                    {{ $t('Email address') }}
                </Label>
                <Input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    name="email"
                    :default-value="user?.email"
                    required
                    autocomplete="username"
                    :placeholder="$t('Email address')"
                />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div class="mt-2 grid gap-3 border-t border-line pt-6">
                <Label>
                    {{ $t('Avatar') }}
                </Label>
                <AvatarPicker v-model="avatar" />
                <InputError class="mt-3" :message="errors.avatar" />
            </div>

            <div v-if="page.props.mustVerifyEmail && !user?.email_verified_at">
                <p class="-mt-4 text-sm text-muted-foreground">
                    {{ $t('Your email address is unverified.') }}
                    <Link
                        :href="send()"
                        as="button"
                        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                    >
                        {{
                            $t('Click here to re-send the verification email.')
                        }}
                    </Link>
                </p>

                <div
                    v-if="page.props.status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    {{
                        $t(
                            'A new verification link has been sent to your email address.',
                        )
                    }}
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button
                    :disabled="processing"
                    data-test="update-profile-button"
                >
                    {{ $t('Save') }}
                    <KbdHint :keys="['CTRL', 'S']" tone="on-dark"
                /></Button>
            </div>
        </Form>
    </div>
</template>
