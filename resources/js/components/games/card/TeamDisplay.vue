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
    <div class="w-[200px]">
        <div v-for="player in props.team.players" :key="player.id" class="grid grid-cols-[1fr_auto] items-center gap-2">
            <div class="mb-4 flex items-start gap-1 truncate">
                <div class="flex flex-col">
                    <div class="flex items-center gap-1">
                        <span class="truncate">{{ player.last_name }}</span>
                        <span v-if="player.elo" class="text-muted-foreground">{{ player.elo }}</span>
                    </div>
                    <span class="text-sm">{{ player.first_name }}</span>
                </div>
            </div>
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
