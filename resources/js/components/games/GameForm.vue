<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { EnhancedSelect } from '@/components/ui/select';
import type { SharedData } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    locations: App.DTOs.TLocation[];
    players: App.DTOs.TPlayer[];
}>();

const page = usePage<SharedData>();
const authenticatedUser = page.props.auth.user;

const authenticatedPlayer = props.players.find((player) => player.user?.id === authenticatedUser?.id);

const isGameFormVisible = ref(false);

const gameForm = useForm({
    date: '',
    location_id: 0,
    first_team_players: [authenticatedPlayer?.id, undefined] as (number | undefined)[],
    second_team_players: [] as (number | undefined)[],
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
    const filteredPlayers = props.players.filter((player) => player.user?.email !== authenticatedUser?.email);

    return filteredPlayers.map((player) => ({
        value: player.id,
        label: `${player.first_name} ${player.last_name}`,
    }));
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
            </div>

            <div class="space-y-2">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <Label>First Team Players</Label>
                        <div class="space-y-2">
                            <EnhancedSelect
                                v-model="gameForm.first_team_players[0]"
                                :options="[authenticatedPlayerOption]"
                                placeholder="Select first player"
                                :disabled="true"
                                class="w-[300px]"
                            />
                            <EnhancedSelect
                                v-model="gameForm.first_team_players[1]"
                                :options="playerOptions"
                                placeholder="Select second player"
                                :disabled="gameForm.processing"
                                clearable
                                class="w-[300px]"
                            />
                        </div>
                        <InputError :message="gameForm.errors.first_team_players" />
                    </div>
                    <div>
                        <Label>Second Team Players</Label>
                        <div class="space-y-2">
                            <EnhancedSelect
                                v-model="gameForm.second_team_players[0]"
                                :options="playerOptions"
                                placeholder="Select first player"
                                :disabled="gameForm.processing"
                                clearable
                                class="w-[300px]"
                            />
                            <EnhancedSelect
                                v-model="gameForm.second_team_players[1]"
                                :options="playerOptions"
                                placeholder="Select second player"
                                :disabled="gameForm.processing"
                                clearable
                                class="w-[300px]"
                            />
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
