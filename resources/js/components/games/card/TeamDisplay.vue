<script setup lang="ts">
const props = defineProps<{
    team: App.DTOs.TTeam;
    isWinningTeam: boolean;
}>();
</script>

<template>
    <div class="flex-1">
        <div class="text-xs font-medium text-muted-foreground">Team {{ props.team.id }}</div>
        <template v-if="props.team.players && props.team.players.length > 0">
            <div
                v-for="player in props.team.players"
                :key="player.id"
                class="text-xs font-medium sm:text-base"
                :class="{ 'text-green-600': isWinningTeam }"
            >
                {{ player.first_name }} {{ player.last_name }}
                <span class="text-xs text-muted-foreground">({{ player.elo }})</span>
            </div>
        </template>
        <div v-else class="text-xs text-muted-foreground">No players assigned</div>
    </div>
</template>
