<script setup lang="ts">
import GameCard from '@/components/games/GameCard.vue';
import GameForm from '@/components/games/GameForm.vue';
import GamesHeader from '@/components/games/GamesHeader.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { SharedData, User, type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    games: App.DTOs.TGame[];
    locations: App.DTOs.TLocation[];
    players: App.DTOs.TPlayer[];
}>();

const page = usePage<SharedData>();
const selectedLocationIds = ref<number[]>([]);

const filteredGames = computed(() => {
    if (selectedLocationIds.value.length === 0) return props.games;
    return props.games.filter((game) => selectedLocationIds.value.includes(game.location_id));
});

const user = page.props.auth.user as User;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Games',
        href: '/games',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl space-y-16">
            <GameForm class="mb-10" :locations="props.locations" :players="props.players" v-if="user" />

            <div>
                <h2 class="text-xl font-semibold"></h2>
                <div v-if="props.games.length === 0" class="text-sm text-muted-foreground">No open games available.</div>
                <div v-else class="grid gap-4">
                    <GamesHeader :locations="props.locations" v-model:selected-location-ids="selectedLocationIds" />

                    <GameCard v-for="game in filteredGames" :key="game.id" :game="game" :locations="props.locations" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
