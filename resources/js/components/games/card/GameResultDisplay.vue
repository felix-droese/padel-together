<script setup lang="ts">
const props = defineProps<{
    game: App.DTOs.TGame;
}>();
</script>

<template>
    <div class="w-fit rounded-lg border bg-card p-4 text-xs shadow-sm md:text-sm">
        <div class="grid grid-cols-[120px_20px_20px_20px] gap-x-4 gap-y-1 lg:grid-cols-[240px_40px_40px_40px]">
            <div class="col-span-1 font-medium text-black">Result</div>
            <div v-for="i in 3" :key="i" class="text-center text-xs font-medium uppercase tracking-wider text-muted-foreground">Set {{ i }}</div>

            <div class="mt-4 font-medium" :class="{ 'text-green-600': props.game.winning_team?.id === props.game.first_team.id }">
                {{ props.game.first_team.players[0]?.last_name
                }}{{ props.game.first_team.players[1] ? `/${props.game.first_team.players[1].last_name}` : '' }}
            </div>
            <template v-if="props.game.result?.sets">
                <div
                    v-for="i in 3"
                    :key="i"
                    class="mt-4 text-center font-medium"
                    :class="{ 'text-green-600': props.game.result.sets[i - 1]?.first_team > props.game.result.sets[i - 1]?.second_team }"
                >
                    {{ props.game.result.sets[i - 1]?.first_team || '-' }}
                </div>
            </template>

            <div class="font-medium" :class="{ 'text-green-600': props.game.winning_team?.id === props.game.second_team?.id }">
                {{ props.game.second_team?.players[0]?.last_name
                }}{{ props.game.second_team?.players[1] ? `/${props.game.second_team.players[1].last_name}` : '' }}
            </div>
            <template v-if="props.game.result?.sets">
                <div
                    v-for="i in 3"
                    :key="i"
                    class="text-center font-medium"
                    :class="{ 'text-green-600': props.game.result.sets[i - 1]?.second_team > props.game.result.sets[i - 1]?.first_team }"
                >
                    {{ props.game.result.sets[i - 1]?.second_team || '-' }}
                </div>
            </template>
        </div>
    </div>
</template>
