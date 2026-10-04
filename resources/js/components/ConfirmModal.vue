<script setup lang="ts">
import DangerButton from '@/components/DangerButton.vue';
import Modal from '@/components/Modal.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import { confirmState, settleConfirm } from '@/composables/useConfirm';

const state = confirmState();

function onCancel(): void {
    settleConfirm(false);
}

function onConfirm(): void {
    settleConfirm(true);
}
</script>

<template>
    <Modal
        :show="state.show"
        :title="state.title"
        max-width="sm"
        @close="onCancel"
    >
        <p class="text-sm text-foreground/75">{{ state.message }}</p>

        <template #footer>
            <SecondaryButton type="button" @click="onCancel">
                {{ $t('Cancel') }}
            </SecondaryButton>
            <DangerButton type="button" @click="onConfirm">
                {{ state.confirmLabel }}
            </DangerButton>
        </template>
    </Modal>
</template>
