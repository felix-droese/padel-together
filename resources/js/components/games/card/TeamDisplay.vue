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
    <div>
        <div v-if="props.team.players.length === 0" class="text-sm md:text-base">-</div>
        <div v-else v-for="player in props.team.players" :key="player.id" class="flex items-start justify-between gap-6 text-sm md:text-base">
            <div class="mb-4 flex items-start gap-1 truncate">
                <div class="flex flex-col">
                    <div class="flex items-center gap-1">
                        <span class="truncate font-medium">{{ player.last_name }}</span>
                    </div>
                    <span class="text-xs md:text-sm">{{ player.first_name }}</span>
                </div>
            </div>
            <div class="flex flex-col">
                <span v-if="player.elo" class="text-xs md:text-base">{{ player.elo }}</span>
                <span
                    v-if="player.elo"
                    :class="{
                        'text-green-600': (getEloChange(player.id)?.change ?? 0) > 0,
                        'text-red-600': (getEloChange(player.id)?.change ?? 0) < 0,
                    }"
                    class="self-end"
                >
                    {{ (getEloChange(player.id)?.change ?? 0) > 0 ? '+' : '' }} {{ getEloChange(player.id)?.change }}</span
                >
            </div>
        </div>
    </div>
</template>
