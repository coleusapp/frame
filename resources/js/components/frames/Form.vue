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
    <div class="grid grid-cols-2 gap-4">
        <FormKit type="number" name="display_width" label="Image Width" number="integer" min="320" max="7680" validation="number|between:320,7680" help="In pixels. Newly fetched artworks are stored at most this wide." />
        <FormKit type="number" name="display_height" label="Image Height" number="integer" min="320" max="7680" validation="number|between:320,7680" help="In pixels. Newly fetched artworks are stored at most this tall." />
    </div>
    <FormKit
        type="select"
        name="rotation"
        label="Rotation"
        :options="[
            { label: 'None', value: 0 },
            { label: '90° clockwise', value: 90 },
            { label: '180°', value: 180 },
            { label: '270° clockwise (90° counter-clockwise)', value: 270 },
        ]"
        help="Turns the page for a screen hung on its side. Width, height and insets are as the art hangs."
    />
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <FormKit type="number" name="inset_top" label="Top Inset" number="integer" min="0" max="1000" validation="number|between:0,1000" help="Pixels the frame hides." />
        <FormKit type="number" name="inset_right" label="Right Inset" number="integer" min="0" max="1000" validation="number|between:0,1000" help="Pixels the frame hides." />
        <FormKit type="number" name="inset_bottom" label="Bottom Inset" number="integer" min="0" max="1000" validation="number|between:0,1000" help="Pixels the frame hides." />
        <FormKit type="number" name="inset_left" label="Left Inset" number="integer" min="0" max="1000" validation="number|between:0,1000" help="Pixels the frame hides." />
    </div>
    <FormKit type="color" name="background_color" label="Background" help="Shown around the art, e.g. to match the mat." />
    <FormKit type="switch" name="show_caption" label="Show Caption" help="The title, artist and date over the art." />
    <FormKit
        v-if="value?.show_caption"
        type="select"
        name="caption_position"
        label="Caption Position"
        :options="[
            { label: 'Top left', value: 'top-left' },
            { label: 'Top center', value: 'top-center' },
            { label: 'Top right', value: 'top-right' },
            { label: 'Middle left', value: 'middle-left' },
            { label: 'Middle right', value: 'middle-right' },
            { label: 'Bottom left', value: 'bottom-left' },
            { label: 'Bottom center', value: 'bottom-center' },
            { label: 'Bottom right', value: 'bottom-right' },
        ]"
    />
    <FormKit type="switch" name="is_active" label="Active" help="An inactive frame's kiosk URL stops working and it isn't refreshed." />
</template>
