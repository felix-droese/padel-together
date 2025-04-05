<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    players: App.DTOs.TPlayer[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Players',
        href: '/players',
    },
];

function deletePlayer(playerId: number) {
    if (confirm('Are you sure you want to delete this player?')) {
        router.delete(route('players.destroy', playerId));
    }
}
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
                    <div class="flex items-center justify-between">
                        <div class="font-medium">
                            {{ player.first_name }} {{ player.last_name }}
                            <span v-if="player.user?.email" class="ml-2 text-sm text-muted-foreground"> ({{ player.user.email }}) </span>
                        </div>
                        <Button variant="ghost" size="icon" @click="deletePlayer(player.id)">
                            <Trash2 class="h-5 w-5 text-red-500" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
