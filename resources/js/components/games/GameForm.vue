<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { EnhancedSelect } from '@/components/ui/select';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    locations: App.DTOs.TLocation[];
    players: App.DTOs.TPlayer[];
}>();

const isGameFormVisible = ref(false);

const gameForm = useForm({
    date: '',
    location_id: 0,
    first_team_players: [] as (number | undefined)[],
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
    return props.players.map((player) => ({
        value: player.id,
        label: `${player.first_name} ${player.last_name}`,
    }));
});
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Create New Game</h2>
                <p class="text-sm text-muted-foreground">Create a new padel game with other players.</p>
            </div>
            <Button @click="isGameFormVisible = !isGameFormVisible">
                {{ isGameFormVisible ? 'Cancel' : 'Create Game' }}
            </Button>
        </div>

        <form v-if="isGameFormVisible" @submit.prevent="submitGame" class="space-y-4">
            <div class="space-y-2">
                <Label for="date">Game Date</Label>
                <div class="w-[300px]">
                    <VueDatePicker v-model="gameForm.date" />
                </div>
                <InputError :message="gameForm.errors.date" />
            </div>

            <div class="space-y-2">
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

            <div class="space-y-2">
                <Label>First Team Players</Label>
                <div class="grid grid-cols-2 gap-4">
                    <EnhancedSelect
                        v-model="gameForm.first_team_players[0]"
                        :options="playerOptions"
                        placeholder="Select first player"
                        :disabled="gameForm.processing"
                        clearable
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

            <div class="space-y-2">
                <Label>Second Team Players</Label>
                <div class="grid grid-cols-2 gap-4">
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

            <Button type="submit" :disabled="gameForm.processing">
                {{ gameForm.processing ? 'Creating...' : 'Create Game' }}
            </Button>
        </form>
    </div>
</template>
