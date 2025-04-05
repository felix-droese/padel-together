<script setup lang="ts">
const props = defineProps<{
    team: App.DTOs.TTeam;
    isWinningTeam: boolean;
}>();
</script>

<template>
    <div class="flex-1">
        <div class="text-xs font-medium text-muted-foreground">Team {{ props.team.id }}</div>
        <div v-if="props.team.players?.[0]" class="text-xs font-medium sm:text-base" :class="{ 'text-green-600': isWinningTeam }">
            {{ props.team.players[0].first_name }} {{ props.team.players[0].last_name }}
            <span class="text-xs text-muted-foreground">({{ props.team.players[0].elo }})</span>
        </div>
        <div v-if="props.team.players?.[1]" class="text-xs font-medium sm:text-base" :class="{ 'text-green-600': isWinningTeam }">
            {{ props.team.players[1].first_name }} {{ props.team.players[1].last_name }}
            <span class="text-xs text-muted-foreground">({{ props.team.players[1].elo }})</span>
        </div>
    </div>
</template>
