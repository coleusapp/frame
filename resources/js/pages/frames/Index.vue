<script setup lang="ts">
import { FrameCollection } from '@/components/frames/frame';
import TableCard from '@/components/frames/TableCard.vue';
import FrameLayout from '@/layouts/FrameLayout.vue';

defineProps<{
    collection: FrameCollection;
}>();
</script>

<template>
    <FrameLayout>
        <TableCard :collection="collection" />
    </FrameLayout>
</template>
