<script setup lang="ts">
import GameCard from '@/components/games/GameCard.vue';
import GameForm from '@/components/games/GameForm.vue';

const props = defineProps<{
    games: App.DTOs.TGame[];
    locations: App.DTOs.TLocation[];
    players: App.DTOs.TPlayer[];
}>();
</script>

<template>
    <div>
        <GameForm :locations="props.locations" :players="props.players" />

        <div class="mt-10">
            <h2 class="mt-10 text-xl font-semibold"></h2>
            <div v-if="props.games.length === 0" class="text-sm text-muted-foreground">No open games available.</div>
            <div v-else class="grid gap-4">
                <GameCard v-for="game in props.games" :key="game.id" :game="game" :locations="props.locations" />
            </div>
        </div>
    </div>
</template>
