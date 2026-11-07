<script setup lang="ts">
import { kioskUrlKey } from '@/components/frames/frame';
import { inject, ref, watch } from 'vue';

const kioskUrl = inject(kioskUrlKey)!;

const copied = ref(false);

watch(kioskUrl, () => (copied.value = false));

async function copyKioskUrl(): Promise<void> {
    if (!kioskUrl.value) {
        return;
    }

    await navigator.clipboard.writeText(kioskUrl.value);

    copied.value = true;
}
</script>

<template>
    <UiAlert
        v-if="kioskUrl"
        color="warning"
        variant="subtle"
        icon="i-lucide-key-round"
        title="Kiosk URL — shown only once"
        description="Open this on the frame's browser. It can't be shown again; regenerate it if you lose it."
    >
        <template #actions>
            <div class="flex w-full items-center gap-2">
                <code class="bg-default min-w-0 flex-1 rounded-md px-2 py-1.5 text-sm break-all select-all">{{ kioskUrl }}</code>
                <UiButton :icon="copied ? 'i-lucide-check' : 'i-lucide-copy'" color="neutral" variant="outline" @click="copyKioskUrl">
                    {{ copied ? 'Copied' : 'Copy' }}
                </UiButton>
            </div>
        </template>
    </UiAlert>
</template>
