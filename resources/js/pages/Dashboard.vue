<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import Index from './games/Index.vue';

const props = defineProps<{
    locations: App.DTOs.TLocation[];
    players: App.DTOs.TPlayer[];
    games: App.DTOs.TGame[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl space-y-16">
            <Button as-child>
                <a :href="route('locations.index')">Locations</a>
            </Button>

            <Index :games="props.games" :open-games="props.openGames" :locations="props.locations" :players="props.players" />
        </div>
    </AppLayout>
</template>
