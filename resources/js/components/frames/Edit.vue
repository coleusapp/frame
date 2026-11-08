<script setup lang="ts">
import { FrameRequest, FrameResource, resourceKey } from '@/components/frames/frame';
import FrameForm from '@/components/frames/Form.vue';
import Form from '@coleus/support/components/form/Form.vue';
import { onErrorToast, onSuccessToast, ToastType } from '@coleus/support/lib/inertia';
import { useForm } from '@formkit/inertia';
import { inject } from 'vue';

const resource = inject(resourceKey) as FrameResource;

const form = useForm<FrameRequest>({
    name: resource.data.name,
    search_query: resource.data.search_query,
    sources: resource.data.sources,
    interval_seconds: resource.data.interval_seconds,
    display_width: resource.data.display_width,
    display_height: resource.data.display_height,
    rotation: resource.data.rotation,
    inset_top: resource.data.inset_top,
    inset_right: resource.data.inset_right,
    inset_bottom: resource.data.inset_bottom,
    inset_left: resource.data.inset_left,
    background_color: resource.data.background_color,
    show_caption: resource.data.show_caption,
    caption_position: resource.data.caption_position,
    is_active: resource.data.is_active,
});

const submit = () =>
    form.put(route('frame.update', { frame: resource.data.id }), {
        preserveScroll: true,
        ...onSuccessToast(ToastType.UPDATE_SUCCESS),
        ...onErrorToast(ToastType.ERROR),
    });
</script>

<template>
    <Form :form="form" :submit="submit">
        <template #default="{ value }">
            <FrameForm :value="value as FrameRequest" />
        </template>
        <template #actions>
            <div class="flex gap-2">
                <UiButton type="submit" :disabled="form.processing.value">Save</UiButton>
                <UiButton :href="route('frame.preview', { frame: resource.data.id })" target="_blank" variant="outline" icon="i-lucide-eye">Preview</UiButton>
            </div>
        </template>
    </Form>
</template>
