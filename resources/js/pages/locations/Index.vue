<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';

const props = defineProps<{
    locations: App.DTOs.TLocation[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Locations',
        href: '/locations',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl">
            <div class="mb-10 space-y-2">
                <h2 class="text-xl font-semibold">Locations</h2>
                <p class="text-sm text-muted-foreground">Manage your padel match locations.</p>
            </div>
            <div class="flex items-center justify-between">
                <div v-if="props.locations.length === 0" class="text-sm text-muted-foreground">No locations have been created yet.</div>
                <div v-else class="grid w-full gap-4">
                    <div v-for="location in props.locations" :key="location.id" class="rounded-lg border p-4">
                        <h3 class="font-medium">{{ location.name }}</h3>
                    </div>
                </div>
            </div>
            <Button as-child class="mt-6">
                <a :href="route('locations.create')">Create New Location</a>
            </Button>
        </div>
    </AppLayout>
</template>
