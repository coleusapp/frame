<script setup lang="ts">
import CreateCard from '@/components/frames/CreateCard.vue';
import { FrameResource, OptionCollection, resourceKey, sourcesKey } from '@/components/frames/frame';
import FrameLayout from '@/layouts/FrameLayout.vue';
import { provide } from 'vue';

const props = defineProps<{
    resource: FrameResource;
    sources: OptionCollection;
}>();

provide(resourceKey, props.resource);
provide(sourcesKey, props.sources);
</script>

<template>
    <FrameLayout>
        <CreateCard />
    </FrameLayout>
</template>
