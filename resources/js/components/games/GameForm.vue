<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select/index';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    locations: App.DTOs.TLocation[];
    players: App.DTOs.TPlayer[];
}>();

const isGameFormVisible = ref(false);

const gameForm = useForm({
    date: '',
    location_id: 0,
    first_team_players: [] as number[],
    second_team_players: [] as number[],
});

function submitGame() {
    gameForm.post(route('games.store'), {
        onSuccess: () => {
            gameForm.reset();
            isGameFormVisible.value = false;
        },
    });
}
</script>

<template>
    <div class="mt-10 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Create New Game</h2>
                <p class="text-sm text-muted-foreground">Create a new padel game with other players.</p>
            </div>
            <Button @click="isGameFormVisible = !isGameFormVisible">
                {{ isGameFormVisible ? 'Hide Form' : 'Show Form' }}
            </Button>
        </div>

        <form v-if="isGameFormVisible" @submit.prevent="submitGame" class="space-y-4">
            <div class="space-y-2">
                <Label for="date">Game Date</Label>
                <VueDatePicker v-model="gameForm.date" />
                <InputError :message="gameForm.errors.date" />
            </div>

            <div class="space-y-2">
                <Label for="location">Location</Label>
                <Select v-model="gameForm.location_id" :disabled="gameForm.processing">
                    <SelectTrigger>
                        <SelectValue>
                            {{ props.locations.find((l) => l.id === gameForm.location_id)?.name || 'Select a location' }}
                        </SelectValue>
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="location in props.locations" :key="location.id" :value="location.id">
                            {{ location.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="gameForm.errors.location_id" />
            </div>

            <div class="space-y-2">
                <Label>First Team Players</Label>
                <div class="grid grid-cols-2 gap-4">
                    <Select v-model="gameForm.first_team_players[0]" :disabled="gameForm.processing">
                        <SelectTrigger>
                            <SelectValue>
                                {{
                                    props.players.find((p) => p.id === gameForm.first_team_players[0])?.first_name +
                                        ' ' +
                                        props.players.find((p) => p.id === gameForm.first_team_players[0])?.last_name || 'Select first player'
                                }}
                            </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="player in props.players" :key="player.id" :value="player.id">
                                {{ player.first_name }} {{ player.last_name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="gameForm.first_team_players[1]" :disabled="gameForm.processing">
                        <SelectTrigger>
                            <SelectValue>
                                {{
                                    props.players.find((p) => p.id === gameForm.first_team_players[1])?.first_name +
                                        ' ' +
                                        props.players.find((p) => p.id === gameForm.first_team_players[1])?.last_name || 'Select second player'
                                }}
                            </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="player in props.players" :key="player.id" :value="player.id">
                                {{ player.first_name }} {{ player.last_name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <InputError :message="gameForm.errors.first_team_players" />
            </div>

            <div class="space-y-2">
                <Label>Second Team Players</Label>
                <div class="grid grid-cols-2 gap-4">
                    <Select v-model="gameForm.second_team_players[0]" :disabled="gameForm.processing">
                        <SelectTrigger>
                            <SelectValue>
                                {{
                                    props.players.find((p) => p.id === gameForm.second_team_players[0])?.first_name +
                                        ' ' +
                                        props.players.find((p) => p.id === gameForm.second_team_players[0])?.last_name || 'Select first player'
                                }}
                            </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="player in props.players" :key="player.id" :value="player.id">
                                {{ player.first_name }} {{ player.last_name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="gameForm.second_team_players[1]" :disabled="gameForm.processing">
                        <SelectTrigger>
                            <SelectValue>
                                {{
                                    props.players.find((p) => p.id === gameForm.second_team_players[1])?.first_name +
                                        ' ' +
                                        props.players.find((p) => p.id === gameForm.second_team_players[1])?.last_name || 'Select second player'
                                }}
                            </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="player in props.players" :key="player.id" :value="player.id">
                                {{ player.first_name }} {{ player.last_name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <InputError :message="gameForm.errors.second_team_players" />
            </div>

            <Button type="submit" :disabled="gameForm.processing">
                {{ gameForm.processing ? 'Creating...' : 'Create Game' }}
            </Button>
        </form>
    </div>
</template>
