<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue';
import { useForm } from '@inertiajs/vue3';
import { Clock, MapPin, Trash2 } from 'lucide-vue-next';
import { watch } from 'vue';

const props = defineProps<{
    game: App.DTOs.TGame;
    locations: App.DTOs.TLocation[];
}>();

const resultForm = useForm({
    game_id: 0,
    sets: [
        { first_team: '', second_team: '' },
        { first_team: '', second_team: '' },
        { first_team: '', second_team: '' },
    ],
});

const deleteForm = useForm({});

function submitResult(gameId: number) {
    const routeName = props.game.result ? 'games.result.update' : 'games.result';
    const method = props.game.result ? 'put' : 'post';

    resultForm[method](route(routeName, { game: gameId }), {
        onSuccess: () => {
            resultForm.reset();
        },
    });
}

function deleteGame() {
    if (confirm('Are you sure you want to delete this game?')) {
        deleteForm.delete(route('games.destroy', { game: props.game.id }));
    }
}

// Initialize form with existing result if present
watch(
    () => props.game.result,
    (newResult) => {
        if (newResult) {
            // Reset all sets to empty
            resultForm.sets = [
                { first_team: '', second_team: '' },
                { first_team: '', second_team: '' },
                { first_team: '', second_team: '' },
            ];

            // Fill in only the existing sets
            Object.values(newResult.sets).forEach((set: App.DTOs.TSet, index: number) => {
                if (index < 3) {
                    // Ensure we don't exceed our form's capacity
                    resultForm.sets[index] = {
                        first_team: set.first_team.toString(),
                        second_team: set.second_team.toString(),
                    };
                }
            });
        }
    },
    { immediate: true },
);
</script>

<template>
    <div class="rounded-lg border p-4">
        <div class="flex items-center justify-between">
            <div class="flex w-full items-start justify-between">
                <div class="flex-1">
                    <div class="text-xs font-medium text-muted-foreground">Team 1</div>
                    <div
                        class="text-xs font-medium sm:text-base"
                        :class="{ 'text-green-600': props.game.winning_team?.id === props.game.first_team.id }"
                    >
                        {{ props.game.first_team.players[0].first_name }} {{ props.game.first_team.players[0].last_name }}
                        <span class="text-xs text-muted-foreground">({{ props.game.first_team.players[0].elo }})</span>
                    </div>
                    <div
                        v-if="props.game.first_team.players[1]"
                        class="text-xs font-medium sm:text-base"
                        :class="{ 'text-green-600': props.game.winning_team?.id === props.game.first_team.id }"
                    >
                        {{ props.game.first_team.players[1].first_name }} {{ props.game.first_team.players[1].last_name }}
                        <span class="text-xs text-muted-foreground">({{ props.game.first_team.players[1].elo }})</span>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="text-xs font-medium text-muted-foreground">Team 2</div>
                    <div
                        class="text-xs font-medium sm:text-base"
                        :class="{ 'text-green-600': props.game.winning_team?.id === props.game.second_team?.id }"
                    >
                        {{ props.game.second_team?.players[0]?.first_name }} {{ props.game.second_team?.players[0]?.last_name }}
                        <span v-if="props.game.second_team?.players[0]" class="text-xs text-muted-foreground"
                            >({{ props.game.second_team.players[0].elo }})</span
                        >
                    </div>
                    <div
                        v-if="props.game.second_team?.players[1]"
                        class="text-xs font-medium sm:text-base"
                        :class="{ 'text-green-600': props.game.winning_team?.id === props.game.second_team?.id }"
                    >
                        {{ props.game.second_team.players[1].first_name }} {{ props.game.second_team.players[1].last_name }}
                        <span class="text-xs text-muted-foreground">({{ props.game.second_team.players[1].elo }})</span>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="flex items-center gap-2">
                    <Button variant="ghost" size="icon" @click="deleteGame">
                        <Trash2 class="h-5 w-5 text-red-500" />
                    </Button>
                    <Popover v-slot="{ close }">
                        <PopoverButton as="div">
                            <Button variant="outline">
                                {{ props.game.result ? 'Edit Result' : 'Add Result' }}
                            </Button>
                        </PopoverButton>
                        <PopoverPanel
                            class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        >
                            <div class="space-y-4 p-4">
                                <div class="space-y-2">
                                    <h4 class="font-medium">{{ props.game.result ? 'Edit Result' : 'Enter Result' }}</h4>
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
                                <div class="flex gap-2">
                                    <Button @click="close" variant="outline" class="flex-1">Cancel</Button>
                                    <Button @click="submitResult(props.game.id)" :disabled="resultForm.processing" class="flex-1">
                                        {{ resultForm.processing ? 'Saving...' : 'Save Result' }}
                                    </Button>
                                </div>
                            </div>
                        </PopoverPanel>
                    </Popover>
                </div>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-4 text-[10px] text-muted-foreground sm:text-sm">
            <div class="flex items-center gap-1">
                <Clock class="h-3 w-3 sm:h-4 sm:w-4" />
                <span>
                    {{ new Date(props.game.date).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) }}
                    {{ new Date(props.game.date).toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' }) }}
                </span>
            </div>
            <div class="flex items-center gap-1">
                <MapPin class="h-3 w-3 sm:h-4 sm:w-4" />
                <span>{{ props.locations.find((l) => l.id === props.game.location_id)?.name }}</span>
            </div>
        </div>
        <div v-if="props.game.result" class="mt-6">
            <div class="grid grid-cols-[120px_40px_40px_40px] gap-x-4 gap-y-1">
                <div></div>
                <div class="text-center text-muted-foreground">Set 1</div>
                <div class="text-center text-muted-foreground">Set 2</div>
                <div class="text-center text-muted-foreground">Set 3</div>

                <div :class="{ 'text-green-600': props.game.winning_team?.id === props.game.first_team.id }">First Team</div>
                <div class="text-center" :class="{ 'text-green-600': props.game.result.sets[0].first_team > props.game.result.sets[0].second_team }">
                    {{ props.game.result.sets[0].first_team }}
                </div>
                <div class="text-center" :class="{ 'text-green-600': props.game.result.sets[1].first_team > props.game.result.sets[1].second_team }">
                    {{ props.game.result.sets[1].first_team }}
                </div>
                <div class="text-center" :class="{ 'text-green-600': props.game.result.sets[2].first_team > props.game.result.sets[2].second_team }">
                    {{ props.game.result.sets[2].first_team }}
                </div>

                <div :class="{ 'text-green-600': props.game.winning_team?.id === props.game.second_team?.id }">Second Team</div>
                <div class="text-center" :class="{ 'text-green-600': props.game.result.sets[0].second_team > props.game.result.sets[0].first_team }">
                    {{ props.game.result.sets[0].second_team }}
                </div>
                <div class="text-center" :class="{ 'text-green-600': props.game.result.sets[1].second_team > props.game.result.sets[1].first_team }">
                    {{ props.game.result.sets[1].second_team }}
                </div>
                <div class="text-center" :class="{ 'text-green-600': props.game.result.sets[2].second_team > props.game.result.sets[2].first_team }">
                    {{ props.game.result.sets[2].second_team }}
                </div>
            </div>
        </div>
    </div>
</template>
