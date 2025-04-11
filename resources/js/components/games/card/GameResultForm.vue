<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue';
import { useForm } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    game: App.DTOs.TGame;
}>();

const showPlayerForm = ref(false);

const resultForm = useForm({
    game_id: 0,
    sets: [
        { first_team: '', second_team: '' },
        { first_team: '', second_team: '' },
        { first_team: '', second_team: '' },
    ],
});

const playerForm = useForm({
    first_name: '',
    last_name: '',
});

function submitPlayer() {
    playerForm.post(route('players.store'), {
        onSuccess: () => {
            playerForm.reset();
            showPlayerForm.value = false;
        },
    });
}

function submitResult(gameId: number, close: () => void) {
    const routeName = props.game.result ? 'games.result.update' : 'games.result';
    const method = props.game.result ? 'put' : 'post';

    resultForm[method](route(routeName, { game: gameId }), {
        onSuccess: () => {
            resultForm.reset();
            close();
        },
    });
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
    <Popover v-slot="{ close }">
        <PopoverButton as="div">
            <Button variant="outline">
                {{ props.game.result ? 'Edit Result' : 'Add Result' }}
            </Button>
        </PopoverButton>
        <PopoverPanel
            class="absolute z-50 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
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
                    <Button @click="submitResult(props.game.id, close)" :disabled="resultForm.processing" class="flex-1">
                        {{ resultForm.processing ? 'Saving...' : 'Save Result' }}
                    </Button>
                </div>
                <div class="border-t pt-4">
                    <Button variant="ghost" class="w-full justify-start gap-2" @click="showPlayerForm = !showPlayerForm">
                        <Plus class="h-4 w-4" />
                        Add Player
                    </Button>
                    <div v-if="showPlayerForm" class="mt-4 space-y-2">
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
                        <Button @click="submitPlayer" :disabled="playerForm.processing" class="w-full">
                            {{ playerForm.processing ? 'Adding...' : 'Add Player' }}
                        </Button>
                    </div>
                </div>
            </div>
        </PopoverPanel>
    </Popover>
</template>
