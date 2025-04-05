<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Players',
        href: '/players',
    },
    {
        title: 'Create',
        href: '/',
    },
];
</script>

<template>
    <Head title="Create Player" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl">
            <h1 class="mb-8 text-2xl font-semibold">Create Player</h1>
            <!-- TODO: Add player creation form -->
        </div>
    </AppLayout>
</template>
