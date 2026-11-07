<script setup lang="ts">
import { FrameCollection } from '@/components/frames/frame';
import FrameTable from '@/components/frames/Table.vue';
import Header from '@coleus/support/components/card/Header.vue';
import MetaInformation from '@coleus/support/components/table/MetaInformation.vue';
import Pagination from '@coleus/support/components/table/Pagination.vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
    collection: FrameCollection;
}>();
</script>

<template>
    <UiCard>
        <template #header>
            <Header title="Frames">
                <Link :href="route('frame.create')">
                    <UiButton icon="lucide-plus">New</UiButton>
                </Link>
            </Header>
        </template>
        <FrameTable :collection="collection" />
        <template #footer>
            <div class="flex items-center justify-between">
                <MetaInformation :meta="collection.meta" />
                <Pagination :meta="collection.meta" />
            </div>
        </template>
    </UiCard>
</template>
