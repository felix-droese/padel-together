<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    error: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Payment Error',
        href: '#',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex min-h-[400px] items-center justify-center">
            <div class="text-center">
                <h1 class="text-2xl font-semibold text-destructive">Payment Failed</h1>
                <p class="mt-2 text-muted-foreground">{{ props.error }}</p>
                <button
                    @click="router.visit(route('games.index'))"
                    class="mt-4 inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground ring-offset-background transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                >
                    Return to Games
                </button>
            </div>
        </div>
    </AppLayout>
</template>
