<script setup lang="ts">
import EditCard from '@/components/frames/EditCard.vue';
import { FrameResource, kioskUrlKey, OptionCollection, resourceKey, sourcesKey } from '@/components/frames/frame';
import KioskUrlAlert from '@/components/frames/KioskUrlAlert.vue';
import FrameLayout from '@/layouts/FrameLayout.vue';
import { provide, toRef } from 'vue';

const props = defineProps<{
    resource: FrameResource;
    sources: OptionCollection;
    kiosk_url: string | null;
}>();

provide(resourceKey, props.resource);
provide(sourcesKey, props.sources);
provide(kioskUrlKey, toRef(props, 'kiosk_url'));
</script>

<template>
    <FrameLayout class="flex flex-col gap-2">
        <KioskUrlAlert />
        <EditCard />
    </FrameLayout>
</template>
