<script setup lang="ts">
const props = defineProps<{
    player: App.DTOs.TPlayer | undefined;
    eloChanges: App.DTOs.TEloChange[];
}>();
</script>

<template>
    <div class="flex items-start justify-between gap-6 text-sm md:text-base">
        <div class="mb-4 flex items-start gap-1 truncate">
            <div class="flex flex-col">
                <div class="flex flex-col">
                    <span class="truncate font-medium">
                        {{ props.player ? props.player.last_name : '-' }}
                    </span>
                    <span class="truncate text-sm text-gray-600">
                        {{ props.player ? props.player.first_name : '-' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <span v-if="props.player?.elo" class="text-xs md:text-base">
                {{ props.player.elo }}
            </span>
            <span
                v-if="props.player?.elo"
                :class="{
                    'text-green-600': (props.eloChanges.find((change) => change.player_id === props.player?.id)?.change ?? 0) > 0,
                    'text-red-600': (props.eloChanges.find((change) => change.player_id === props.player?.id)?.change ?? 0) < 0,
                }"
                class="self-end"
            >
                {{ (props.eloChanges.find((change) => change.player_id === props.player?.id)?.change ?? 0) > 0 ? '+' : '' }}
                {{ props.eloChanges.find((change) => change.player_id === props.player?.id)?.change }}
            </span>
        </div>
    </div>
</template>
