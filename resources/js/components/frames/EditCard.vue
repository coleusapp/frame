<script setup lang="ts">
import EditFrame from '@/components/frames/Edit.vue';
import Header from '@coleus/support/components/card/Header.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <UiCard>
        <template #header>
            <Header title="Update Frame">
                <Link :href="route('frame.index')">
                    <UiButton trailing-icon="i-lucide-arrow-right" variant="ghost">All Frames</UiButton>
                </Link>
            </Header>
        </template>
        <EditFrame />
    </UiCard>
</template>
