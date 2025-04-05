<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import { Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import GameMetadata from './card/GameMetadata.vue';
import GameResultDisplay from './card/GameResultDisplay.vue';
import GameResultForm from './card/GameResultForm.vue';
import TeamDisplay from './card/TeamDisplay.vue';

const props = defineProps<{
    game: App.DTOs.TGame;
    locations: App.DTOs.TLocation[];
}>();

const deleteForm = useForm({});

function deleteGame() {
    if (confirm('Are you sure you want to delete this game?')) {
        deleteForm.delete(route('games.destroy', { game: props.game.id }));
    }
}

const canEditResult = computed(() => {
    const firstTeamHasTwoPlayers = props.game.first_team.players.length === 2;
    const secondTeamHasTwoPlayers = props.game.second_team?.players.length === 2;
    return firstTeamHasTwoPlayers && secondTeamHasTwoPlayers;
});
</script>

<template>
    <div class="rounded-lg border p-4">
        <div class="flex items-center justify-between">
            <div class="flex w-full items-start justify-between">
                <TeamDisplay :team="props.game.first_team" :is-winning-team="props.game.winning_team?.id === props.game.first_team.id" />
                <TeamDisplay
                    v-if="props.game.second_team"
                    :team="props.game.second_team"
                    :is-winning-team="props.game.winning_team?.id === props.game.second_team.id"
                />
            </div>
            <div class="relative">
                <div class="flex items-center gap-2">
                    <Button variant="ghost" size="icon" @click="deleteGame">
                        <Trash2 class="h-5 w-5 text-red-500" />
                    </Button>
                    <GameResultForm v-if="canEditResult" :game="props.game" />
                </div>
            </div>
        </div>
        <GameMetadata :date="props.game.date" :location-name="props.locations.find((l) => l.id === props.game.location_id)?.name || ''" />
        <div v-if="props.game.result" class="mt-6">
            <GameResultDisplay :game="props.game" />
        </div>
    </div>
</template>
