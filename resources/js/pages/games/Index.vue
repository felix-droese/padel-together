<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select/index';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    games: App.DTOs.TGame[];
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

const openResultPopover = ref<number | null>(null);

function submitGame() {
    gameForm.post(route('games.store'), {
        onSuccess: () => {
            gameForm.reset();
            isGameFormVisible.value = false;
        },
    });
}

function toggleResultPopover(gameId: number) {
    openResultPopover.value = openResultPopover.value === gameId ? null : gameId;
}

const resultForm = useForm({
    game_id: 0,
    sets: [
        { first_team: '', second_team: '' },
        { first_team: '', second_team: '' },
        { first_team: '', second_team: '' },
    ],
});

function submitResult(gameId: number) {
    resultForm.post(route('games.result', { game: gameId }), {
        onSuccess: () => {
            resultForm.reset();
            openResultPopover.value = null;
        },
    });
}
</script>

<template>
    <div class="mt-10 space-y-4">
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
                                {{ locations.find((l) => l.id === gameForm.location_id)?.name || 'Select a location' }}
                            </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="location in locations" :key="location.id" :value="location.id">
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
                                        players.find((p) => p.id === gameForm.first_team_players[0])?.first_name +
                                            ' ' +
                                            players.find((p) => p.id === gameForm.first_team_players[0])?.last_name || 'Select first player'
                                    }}
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="player in players" :key="player.id" :value="player.id">
                                    {{ player.first_name }} {{ player.last_name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <Select v-model="gameForm.first_team_players[1]" :disabled="gameForm.processing">
                            <SelectTrigger>
                                <SelectValue>
                                    {{
                                        players.find((p) => p.id === gameForm.first_team_players[1])?.first_name +
                                            ' ' +
                                            players.find((p) => p.id === gameForm.first_team_players[1])?.last_name || 'Select second player'
                                    }}
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="player in players" :key="player.id" :value="player.id">
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
                                        players.find((p) => p.id === gameForm.second_team_players[0])?.first_name +
                                            ' ' +
                                            players.find((p) => p.id === gameForm.second_team_players[0])?.last_name || 'Select first player'
                                    }}
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="player in players" :key="player.id" :value="player.id">
                                    {{ player.first_name }} {{ player.last_name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <Select v-model="gameForm.second_team_players[1]" :disabled="gameForm.processing">
                            <SelectTrigger>
                                <SelectValue>
                                    {{
                                        players.find((p) => p.id === gameForm.second_team_players[1])?.first_name +
                                            ' ' +
                                            players.find((p) => p.id === gameForm.second_team_players[1])?.last_name || 'Select second player'
                                    }}
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="player in players" :key="player.id" :value="player.id">
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
    </div>

    <h2 class="text-xl font-semibold">Games</h2>
    <div v-if="props.games.length === 0" class="text-sm text-muted-foreground">No open games available.</div>
    <div v-else class="grid gap-4">
        <div v-for="game in props.games" :key="game.id" class="rounded-lg border p-4">
            <div class="flex items-center justify-between">
                <h3 class="font-medium">
                    {{ game.first_team.players[0].first_name }} {{ game.first_team.players[0].last_name }}
                    {{ game.first_team.players[1] ? `/ ${game.first_team.players[1].first_name} ${game.first_team.players[1].last_name}` : '' }}
                    vs {{ game.second_team?.players[0]?.first_name }} {{ game.second_team?.players[0]?.last_name }}
                    {{ game.second_team?.players[1] ? `/ ${game.second_team.players[1].first_name} ${game.second_team.players[1].last_name}` : '' }}
                </h3>
                <div class="relative">
                    <Popover>
                        <PopoverButton as="div">
                            <Button variant="outline" @click="toggleResultPopover(game.id)"> Add Result </Button>
                        </PopoverButton>
                        <PopoverPanel
                            v-if="openResultPopover === game.id"
                            class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        >
                            <div class="space-y-4 p-4">
                                <div class="space-y-2">
                                    <h4 class="font-medium">Enter Result</h4>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="text-sm text-muted-foreground">Set</div>
                                        <div class="text-sm text-muted-foreground">First Team</div>
                                        <div class="text-sm text-muted-foreground">Second Team</div>
                                    </div>
                                    <div v-for="(set, index) in resultForm.sets" :key="index" class="grid grid-cols-3 gap-2">
                                        <div class="text-sm">Set {{ index + 1 }}</div>
                                        <input
                                            v-model="set.first_team"
                                            type="number"
                                            min="0"
                                            max="7"
                                            class="w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                                            placeholder="0"
                                        />
                                        <input
                                            v-model="set.second_team"
                                            type="number"
                                            min="0"
                                            max="7"
                                            class="w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                                            placeholder="0"
                                        />
                                    </div>
                                </div>
                                <Button @click="submitResult(game.id)" :disabled="resultForm.processing" class="w-full">
                                    {{ resultForm.processing ? 'Saving...' : 'Save Result' }}
                                </Button>
                            </div>
                        </PopoverPanel>
                    </Popover>
                </div>
            </div>
            <p class="mr-4 text-sm text-muted-foreground">
                {{ new Date(game.date).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) }}
                {{ new Date(game.date).toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' }) }} at
                {{ locations.find((l) => l.id === game.location_id)?.name }}
            </p>
            <div v-if="game.result" class="mt-6">
                <div class="text-sm font-medium">Result:</div>
                <div class="grid grid-cols-3 gap-2 text-sm">
                    <div class="text-muted-foreground">Set</div>
                    <div class="text-muted-foreground">First Team</div>
                    <div class="text-muted-foreground">Second Team</div>
                    <template v-for="(set, index) in game.result.sets" :key="index">
                        <div>Set {{ index + 1 }}</div>
                        <div>{{ set.first_team }}</div>
                        <div>{{ set.second_team }}</div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
