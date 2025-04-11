<script setup lang="ts">
import GameMetadata from './card/GameMetadata.vue';
import GamePaymentSection from './card/GamePaymentSection.vue';
import GameResultDisplay from './card/GameResultDisplay.vue';
import GameTeams from './card/GameTeams.vue';

const props = defineProps<{
    game: App.DTOs.TGame;
    locations: App.DTOs.TLocation[];
}>();
</script>

<template>
    <div class="space-y-6 rounded-lg border bg-card p-4 shadow-sm">
        <GameMetadata :game="props.game" :locations="props.locations" />
        <GameTeams :game="props.game" />
        <GameResultDisplay v-if="props.game.result" :game="props.game" />
        <GamePaymentSection :game="props.game" />
    </div>
</template>
