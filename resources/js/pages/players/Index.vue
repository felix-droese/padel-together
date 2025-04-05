<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    players: App.DTOs.TPlayer[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Players',
        href: '/players',
    },
];
</script>

<template>
    <Head title="Players" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl space-y-8">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Players</h1>
                <Button as-child>
                    <a :href="route('players.create')">Add Player</a>
                </Button>
            </div>

            <div v-if="props.players.length === 0" class="text-sm text-muted-foreground">No players available.</div>
            <div v-else class="grid gap-4">
                <div v-for="player in props.players" :key="player.id" class="rounded-lg border p-4">
                    <div class="font-medium">{{ player.first_name }} {{ player.last_name }}</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
