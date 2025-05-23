<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { EnhancedSelect } from '@/components/ui/select';
import type { SharedData } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { computed, defineEmits, ref } from 'vue';

const props = defineProps<{
    locations: App.DTOs.TLocation[];
    players: App.DTOs.TPlayer[];
}>();

const page = usePage<SharedData>();
const authenticatedUser = page.props.auth.user;

const authenticatedPlayer = props.players.find((player) => player.user?.id === authenticatedUser?.id);

const isGameFormVisible = ref(false);
const showPlayerForm = ref(false);

const playerForm = useForm({
    first_name: '',
    last_name: '',
});

const emit = defineEmits<{
    (e: 'playerAdded', player: App.DTOs.TPlayer): void;
}>();

function submitPlayer() {
    playerForm.post(route('players.store'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (response) => {
            playerForm.reset();
            showPlayerForm.value = false;

            // Emit the new player to the parent component
            const newPlayer = response.props.player as App.DTOs.TPlayer;
            if (newPlayer) {
                emit('playerAdded', newPlayer);
            }
        },
    });
}

const gameForm = useForm({
    date: '',
    location_id: 0,
    first_team_players: [authenticatedPlayer?.id, undefined] as (number | undefined)[],
    second_team_players: [undefined, undefined] as (number | undefined)[],
    price_in_cent: 0,
    payer_id: authenticatedUser?.id,
});

function submitGame() {
    gameForm.post(route('games.store'), {
        onSuccess: () => {
            gameForm.reset();
            isGameFormVisible.value = false;
        },
    });
}

const locationOptions = computed(() => {
    return props.locations.map((location) => ({
        value: location.id,
        label: location.name,
    }));
});

const playerOptions = computed(() => {
    // Filter out the authenticated user's player from the list
    const filteredPlayers = props.players.filter((player) => player.user?.id !== authenticatedUser?.id);

    return filteredPlayers.map((player) => ({
        value: player.id,
        label: `${player.first_name} ${player.last_name}`,
    }));
});

const payerOptions = computed(() => {
    // Get all users from the players list
    const users = props.players
        .filter((player) => player.user)
        .map((player) => ({
            value: player.user!.id,
            label: `${player.first_name} ${player.last_name} (${player.user!.email})`,
        }));

    return users;
});

const availableSecondTeamFirstPlayer = computed(() => {
    const selectedPlayers = [gameForm.first_team_players[1], gameForm.second_team_players[1]].filter(Boolean);
    return playerOptions.value.filter((option) => !selectedPlayers.includes(option.value));
});

const availableSecondTeamSecondPlayer = computed(() => {
    const selectedPlayers = [gameForm.first_team_players[1], gameForm.second_team_players[0]].filter(Boolean);
    return playerOptions.value.filter((option) => !selectedPlayers.includes(option.value));
});

const availableFirstTeamSecondPlayer = computed(() => {
    const selectedPlayers = [gameForm.second_team_players[0], gameForm.second_team_players[1]].filter(Boolean);
    return playerOptions.value.filter((option) => !selectedPlayers.includes(option.value));
});

const authenticatedPlayerOption = computed(() => {
    if (!authenticatedPlayer) {
        return {
            value: 0,
            label: 'No authenticated player found',
        };
    }
    return {
        value: authenticatedPlayer.id,
        label: `${authenticatedPlayer.first_name} ${authenticatedPlayer.last_name}`,
    };
});
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold">Create New Game</h2>
                <p class="text-sm text-muted-foreground">Create a new padel game with other players.</p>
            </div>
            <Button @click="isGameFormVisible = !isGameFormVisible">
                {{ isGameFormVisible ? 'Cancel' : 'Create Game' }}
            </Button>
        </div>

        <form v-if="isGameFormVisible" @submit.prevent="submitGame" class="space-y-4">
            <div class="space-y-4">
                <div>
                    <Label for="date">Game Date</Label>
                    <div class="w-[300px]">
                        <VueDatePicker v-model="gameForm.date" />
                    </div>
                    <InputError :message="gameForm.errors.date" />
                </div>
                <div>
                    <Label for="location">Location</Label>
                    <EnhancedSelect
                        v-model="gameForm.location_id"
                        :options="locationOptions"
                        placeholder="Select a location"
                        :disabled="gameForm.processing"
                        clearable
                        class="w-[300px]"
                    />
                    <InputError :message="gameForm.errors.location_id" />
                </div>
                <div>
                    <Label for="price_in_cent">Price (in cents)</Label>
                    <input
                        id="price_in_cent"
                        v-model="gameForm.price_in_cent"
                        type="number"
                        min="0"
                        step="1"
                        class="w-[300px] rounded-md border border-input bg-background px-3 py-2 text-sm"
                    />
                    <InputError :message="gameForm.errors.price_in_cent" />
                </div>
                <div>
                    <Label for="payer">Payer</Label>
                    <EnhancedSelect
                        v-model="gameForm.payer_id"
                        :options="payerOptions"
                        placeholder="Select who will pay for the game"
                        :disabled="gameForm.processing"
                        class="w-[300px]"
                    />
                    <p class="mt-1 text-sm text-muted-foreground">
                        The selected player will pay the full amount. Other players will need to pay their share (1/4) to this player.
                    </p>
                    <InputError :message="gameForm.errors.payer_id" />
                </div>
            </div>

            <div class="space-y-2">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <Label>First Team Players</Label>
                        <div class="space-y-2">
                            <div class="flex h-10 w-[300px] items-center rounded-md border border-input bg-muted px-3 py-2 text-sm">
                                {{ authenticatedPlayerOption.label }}
                            </div>
                            <EnhancedSelect
                                v-model="gameForm.first_team_players[1]"
                                :options="availableFirstTeamSecondPlayer"
                                placeholder="Select second player"
                                :disabled="gameForm.processing"
                                clearable
                                class="w-[300px]"
                                :value-type="'number'"
                            >
                                <template #after-search>
                                    <div class="border-b border-t p-2">
                                        <div v-if="!showPlayerForm">
                                            <Button variant="ghost" class="w-full justify-start gap-2" @click="showPlayerForm = true">
                                                <Plus class="h-4 w-4" />
                                                Add Player
                                            </Button>
                                        </div>
                                        <div v-else class="space-y-2">
                                            <input
                                                v-model="playerForm.first_name"
                                                type="text"
                                                placeholder="First Name"
                                                class="w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                                            />
                                            <input
                                                v-model="playerForm.last_name"
                                                type="text"
                                                placeholder="Last Name"
                                                class="w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                                            />
                                            <div class="flex gap-2">
                                                <Button @click="showPlayerForm = false" variant="ghost" class="flex-1">Cancel</Button>
                                                <Button @click="submitPlayer" :disabled="playerForm.processing" class="flex-1">
                                                    {{ playerForm.processing ? 'Adding...' : 'Add' }}
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </EnhancedSelect>
                        </div>
                        <InputError :message="gameForm.errors.first_team_players" />
                    </div>
                    <div>
                        <Label>Second Team Players</Label>
                        <div class="space-y-2">
                            <EnhancedSelect
                                v-model="gameForm.second_team_players[0]"
                                :options="availableSecondTeamFirstPlayer"
                                placeholder="Select first player"
                                :disabled="gameForm.processing"
                                clearable
                                class="w-[300px]"
                                :value-type="'number'"
                            >
                                <template #after-search>
                                    <div class="border-b border-t p-2">
                                        <div v-if="!showPlayerForm">
                                            <Button variant="ghost" class="w-full justify-start gap-2" @click="showPlayerForm = true">
                                                <Plus class="h-4 w-4" />
                                                Add Player
                                            </Button>
                                        </div>
                                        <div v-else class="space-y-2">
                                            <input
                                                v-model="playerForm.first_name"
                                                type="text"
                                                placeholder="First Name"
                                                class="w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                                            />
                                            <input
                                                v-model="playerForm.last_name"
                                                type="text"
                                                placeholder="Last Name"
                                                class="w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                                            />
                                            <div class="flex gap-2">
                                                <Button @click="showPlayerForm = false" variant="ghost" class="flex-1">Cancel</Button>
                                                <Button @click="submitPlayer" :disabled="playerForm.processing" class="flex-1">
                                                    {{ playerForm.processing ? 'Adding...' : 'Add' }}
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </EnhancedSelect>
                            <EnhancedSelect
                                v-model="gameForm.second_team_players[1]"
                                :options="availableSecondTeamSecondPlayer"
                                placeholder="Select second player"
                                :disabled="gameForm.processing"
                                clearable
                                class="w-[300px]"
                                :value-type="'number'"
                            >
                                <template #after-search>
                                    <div class="border-b border-t p-2">
                                        <div v-if="!showPlayerForm">
                                            <Button variant="ghost" class="w-full justify-start gap-2" @click="showPlayerForm = true">
                                                <Plus class="h-4 w-4" />
                                                Add Player
                                            </Button>
                                        </div>
                                        <div v-else class="space-y-2">
                                            <input
                                                v-model="playerForm.first_name"
                                                type="text"
                                                placeholder="First Name"
                                                class="w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                                            />
                                            <input
                                                v-model="playerForm.last_name"
                                                type="text"
                                                placeholder="Last Name"
                                                class="w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                                            />
                                            <div class="flex gap-2">
                                                <Button @click="showPlayerForm = false" variant="ghost" class="flex-1">Cancel</Button>
                                                <Button @click="submitPlayer" :disabled="playerForm.processing" class="flex-1">
                                                    {{ playerForm.processing ? 'Adding...' : 'Add' }}
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </EnhancedSelect>
                        </div>
                        <InputError :message="gameForm.errors.second_team_players" />
                    </div>
                </div>
            </div>

            <Button type="submit" :disabled="gameForm.processing">
                {{ gameForm.processing ? 'Creating...' : 'Create Game' }}
            </Button>
        </form>
    </div>
</template>
