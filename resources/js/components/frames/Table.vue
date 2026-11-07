<script setup lang="ts">
import { type FrameCollection, type FrameData, updateRequest } from '@/components/frames/frame';
import TableActions from '@/components/frames/TableActions.vue';
import SwitchColumn from '@coleus/support/components/ui/table/columns/SwitchColumn.vue';
import type { TableColumn } from '@nuxt/ui';
import { h } from 'vue';

defineProps<{
    collection: FrameCollection;
}>();

const columns: TableColumn<FrameData>[] = [
    {
        accessorKey: 'name',
        header: 'Name',
    },
    {
        accessorKey: 'artworks_count',
        header: 'Artworks',
    },
    {
        accessorKey: 'interval_seconds',
        header: 'Interval',
        cell: ({ row }) => `${row.original.interval_seconds} s`,
    },
    {
        accessorKey: 'is_active',
        header: 'Active',
        cell: ({ row }) =>
            h(SwitchColumn, {
                active: row.original.is_active,
                href: route('frame.update', { frame: row.original.id }),
                payload: (is_active: boolean) => ({ ...updateRequest(row.original), is_active }),
            }),
    },
    {
        id: 'actions',
        cell: ({ row }) => h(TableActions, { frameId: row.original.id }),
    },
];
</script>

<template>
    <UiTable :data="collection?.data" :columns="columns" />
</template>
