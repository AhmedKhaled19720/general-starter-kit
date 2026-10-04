<script setup lang="ts">
import { ref } from 'vue';
import { Check } from '@lucide/vue';
import Modal from '@/components/Modal.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { avatarCatalog, avatarUrl } from '@/utils/avatars';

const model = defineModel<string | null>({ default: null });
const props = defineProps<{ label?: string }>();

const groups = avatarCatalog();
const open = ref(false);

function pick(value: string | null): void {
    model.value = value;
    open.value = false;
}
</script>

<template>
    <div class="flex items-center gap-4">
        <Avatar class="h-16 w-16 rounded-full">
            <AvatarImage
                v-if="avatarUrl(model)"
                :src="avatarUrl(model)!"
                alt="Selected avatar"
            />
            <AvatarFallback class="text-lg">?</AvatarFallback>
        </Avatar>

        <div class="flex flex-col gap-2">
            <button
                type="button"
                class="rounded-md border border-line px-3 py-1.5 text-xs font-semibold tracking-widest uppercase transition hover:bg-emerald-soft"
                @click="open = true"
            >
                {{ $t('Choose avatar') }}
            </button>
            <button
                v-if="model"
                type="button"
                class="self-start text-xs font-medium text-muted-foreground transition hover:text-foreground/80"
                @click="model = null"
            >
                {{ $t('Remove') }}
            </button>
        </div>
    </div>

    <Modal
        :show="open"
        title="Choose an avatar"
        max-width="4xl"
        @close="open = false"
    >
        <div class="space-y-5">
            <button
                type="button"
                class="flex w-full items-center justify-center rounded-md border border-line px-3 py-2 text-sm font-medium text-foreground/75 transition hover:bg-emerald-soft"
                @click="pick(null)"
            >
                {{ $t('No avatar (use initials)') }}
            </button>

            <div v-for="group in groups" :key="group.id">
                <p
                    class="mb-2 text-xs font-semibold tracking-widest text-muted-foreground uppercase"
                >
                    {{ group.label }}
                </p>
                <div class="grid grid-cols-8 gap-2 sm:grid-cols-12">
                    <button
                        v-for="item in group.items"
                        :key="item.id"
                        type="button"
                        class="relative rounded-full p-0.5 transition hover:bg-emerald-soft"
                        :class="
                            model === item.id
                                ? 'ring-2 ring-primary ring-offset-1'
                                : ''
                        "
                        :title="`${group.label} — ${item.seed}`"
                        @click="pick(item.id)"
                    >
                        <img
                            :src="item.url"
                            :alt="`${group.label} ${item.seed}`"
                            loading="lazy"
                            class="h-10 w-10 rounded-full"
                        />
                        <Check
                            v-if="model === item.id"
                            class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-primary text-primary-foreground"
                        />
                    </button>
                </div>
            </div>
        </div>
    </Modal>
</template>
