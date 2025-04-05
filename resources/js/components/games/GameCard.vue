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
    <div class="space-y-4 rounded-lg border bg-card p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <TeamDisplay :team="props.game.first_team" :elo-changes="props.game.elo_changes" />
                <span class="mx-8 text-muted-foreground">vs</span>
                <TeamDisplay v-if="props.game.second_team" :team="props.game.second_team" :elo-changes="props.game.elo_changes" />
            </div>
            <div class="flex items-center gap-2">
                <GameResultForm v-if="canEditResult" :game="props.game" />
                <Button variant="ghost" size="icon" @click="deleteGame">
                    <Trash2 class="h-4 w-4" />
                </Button>
            </div>
        </div>
        <GameMetadata :game="props.game" :locations="props.locations" />
        <GameResultDisplay v-if="props.game.result" :game="props.game" />
    </div>
</template>
