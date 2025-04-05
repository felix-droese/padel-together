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
    <div class="mt-10 space-y-4">
        <GameForm :locations="props.locations" :players="props.players" />

        <h2 class="text-xl font-semibold">Games</h2>
        <div v-if="props.games.length === 0" class="text-sm text-muted-foreground">No open games available.</div>
        <div v-else class="grid gap-4">
            <GameCard v-for="game in props.games" :key="game.id" :game="game" :locations="props.locations" />
        </div>
    </div>
</template>
