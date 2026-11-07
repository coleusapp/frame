<script setup lang="ts">
import { FrameRequest, OptionCollection, sourcesKey } from '@/components/frames/frame';
import { FormKit } from '@formkit/vue';
import { inject } from 'vue';

defineProps<{
    value: FrameRequest;
}>();

const sources = inject(sourcesKey) as OptionCollection;
</script>

<template>
    <FormKit type="text" name="name" label="Name" placeholder="Living room" validation="required|length:0,255" />
    <FormKit
        type="text"
        name="search_query"
        label="Search"
        validation="length:0,255"
        help="Optional words to narrow the paintings, e.g. “impressionism” or “Monet”. Leave empty for any painting."
    />
    <FormKit type="checkbox" name="sources" label="Sources" :options="sources?.data" validation="required" help="Museums new artworks are fetched from." />
    <FormKit
        type="number"
        name="interval_seconds"
        label="Show Each Artwork For"
        number="integer"
        min="10"
        max="86400"
        validation="required|number|between:10,86400"
        help="In seconds."
    />
    <FormKit type="switch" name="is_active" label="Active" help="An inactive frame's kiosk URL stops working and it isn't refreshed." />
</template>
