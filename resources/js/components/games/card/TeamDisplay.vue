<script setup lang="ts">
const props = defineProps<{
    team: App.DTOs.TTeam;
    eloChanges: App.DTOs.TEloChange[];
}>();

function getEloChange(playerId: number) {
    return props.eloChanges.find((change) => change.player_id === playerId);
}
</script>

<template>
    <div class="space-y-1">
        <div v-for="player in props.team.players" :key="player.id" class="flex items-center gap-2">
            <span>{{ player.last_name }}</span>
            <span v-if="player.elo" class="text-sm text-muted-foreground">({{ player.elo }})</span>
            <span
                v-if="getEloChange(player.id)"
                :class="{
                    'text-green-600': (getEloChange(player.id)?.change ?? 0) > 0,
                    'text-red-600': (getEloChange(player.id)?.change ?? 0) < 0,
                }"
                class="text-sm font-medium"
            >
                {{ (getEloChange(player.id)?.change ?? 0) > 0 ? '+' : '' }}
                {{ getEloChange(player.id)?.change }}
            </span>
        </div>
    </div>
</template>
