<script setup lang="ts">
import DeleteAction from '@coleus/support/components/table/DeleteAction.vue';
import EditAction from '@coleus/support/components/table/EditAction.vue';
import ConfirmModal from '@coleus/support/components/ui/modal/ConfirmModal.vue';
import { onErrorToast, onSuccessToast, ToastType } from '@coleus/support/lib/inertia';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    frameId: number | string;
}>();

const confirmingRegenerate = ref(false);

function fetchNow(): void {
    router.post(
        route('frame.fetch', { frame: props.frameId }),
        {},
        {
            preserveScroll: true,
            ...onSuccessToast(ToastType.UPDATE_SUCCESS, 'Fetching new artworks'),
            ...onErrorToast(ToastType.ERROR),
        },
    );
}

function regenerateToken(): void {
    router.post(
        route('frame.token.regenerate', { frame: props.frameId }),
        {},
        {
            ...onSuccessToast(ToastType.UPDATE_SUCCESS, 'Kiosk URL regenerated'),
            ...onErrorToast(ToastType.ERROR),
        },
    );
}
</script>

<template>
    <div class="flex items-center justify-end">
        <UiButton icon="i-lucide-download" label="Fetch now" variant="ghost" color="neutral" @click="fetchNow" />
        <UiButton icon="i-lucide-refresh-cw" label="Regenerate URL" variant="ghost" color="neutral" @click="confirmingRegenerate = true" />
        <EditAction :href="route('frame.edit', { frame: frameId })" />
        <DeleteAction :href="route('frame.destroy', { frame: frameId })" />

        <ConfirmModal
            v-model:open="confirmingRegenerate"
            title="Regenerate the kiosk URL?"
            description="The frame stops working until you open the new URL on it."
            confirm-label="Regenerate"
            @confirm="regenerateToken"
        />
    </div>
</template>
