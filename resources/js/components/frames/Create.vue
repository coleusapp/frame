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
    is_active: resource.data.is_active,
});

const submit = () =>
    form.post(route('frame.store'), {
        ...onSuccessToast(ToastType.STORE_SUCCESS),
        ...onErrorToast(ToastType.ERROR),
    });
</script>

<template>
    <Form :form="form" :submit="submit">
        <template #default="{ value }">
            <FrameForm :value="value as FrameRequest" />
        </template>
    </Form>
</template>
