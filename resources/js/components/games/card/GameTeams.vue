<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { SharedData } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
import GameResultForm from './GameResultForm.vue';
import PlayerDisplay from './PlayerDisplay.vue';

const props = defineProps<{
    game: App.DTOs.TGame;
}>();

const page = usePage<SharedData>();
const auth = computed(() => page.props.auth);

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

const isPlayerInGame = computed(() => {
    if (!auth.value.user) return false;

    const userId = auth.value.user.id;
    const firstTeamPlayers = props.game.first_team.players.map((p) => p.id);
    const secondTeamPlayers = props.game.second_team?.players.map((p) => p.id) || [];

    return firstTeamPlayers.includes(userId) || secondTeamPlayers.includes(userId);
});
</script>

<template>
    <div class="flex flex-col justify-between lg:flex-row">
        <div class="flex items-center gap-2">
            <div class="flex flex-col">
                <PlayerDisplay v-for="i in 2" :key="i" :player="props.game.first_team.players[i - 1]" :elo-changes="props.game.elo_changes" />
            </div>
            <span class="mx-4 text-muted-foreground md:mx-8">vs</span>
            <div v-if="props.game.second_team" class="flex flex-col">
                <PlayerDisplay v-for="i in 2" :key="i" :player="props.game.second_team.players[i - 1]" :elo-changes="props.game.elo_changes" />
            </div>
        </div>
        <div v-if="isPlayerInGame && canEditResult" class="flex gap-2">
            <GameResultForm :game="props.game" />
            <Button variant="ghost" size="icon" @click="deleteGame">
                <Trash2 class="h-4 w-4" />
            </Button>
        </div>
    </div>
</template>
