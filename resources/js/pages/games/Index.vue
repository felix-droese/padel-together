<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select/index';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    games: App.DTOs.TGame[];
    openGames: App.DTOs.TGame[];
    locations: App.DTOs.TLocation[];
}>();
const isGameFormVisible = ref(false);

const gameForm = useForm({
    date: '',
    location_id: 0,
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
        <div class="mt-10 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold">Create New Game</h2>
                    <p class="text-sm text-muted-foreground">Create a new padel game with another player.</p>
                </div>
                <Button @click="isGameFormVisible = !isGameFormVisible">
                    {{ isGameFormVisible ? 'Hide Form' : 'Show Form' }}
                </Button>
            </div>

            <form v-if="isGameFormVisible" @submit.prevent="submitGame" class="space-y-4">
                <div class="space-y-2">
                    <Label for="date">Game Date</Label>
                    <Input id="date" v-model="gameForm.date" type="date" :disabled="gameForm.processing" />
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

                <Button type="submit" :disabled="gameForm.processing">
                    {{ gameForm.processing ? 'Creating...' : 'Create Game' }}
                </Button>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        <h2 class="text-xl font-semibold">Open Games</h2>
        <div v-if="props.openGames.length === 0" class="text-sm text-muted-foreground">No open games available.</div>
        <div v-else class="grid gap-4">
            <div v-for="game in props.openGames" :key="game.id" class="rounded-lg border p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-medium">
                            {{ game.first_team.players[0].first_name }} {{ game.first_team.players[0].last_name }} vs
                            {{ game.second_team?.players[0].first_name }} {{ game.second_team?.players[0].last_name }}
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            {{ new Date(game.date).toLocaleDateString() }} at {{ locations.find((l) => l.id === game.location_id)?.name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <h2 class="text-xl font-semibold">Recent Games</h2>
        <div v-if="props.games.length === 0" class="text-sm text-muted-foreground">No games have been played yet.</div>
        <div v-else class="grid gap-4">
            <div v-for="game in props.games" :key="game.id" class="rounded-lg border p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-medium">
                            {{ game.first_team.players[0].first_name }} {{ game.first_team.players[0].last_name }} vs
                            {{ game.second_team?.players[0].first_name }} {{ game.second_team?.players[0].last_name }}
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            {{ new Date(game.date).toLocaleDateString() }} at {{ locations.find((l) => l.id === game.location_id)?.name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
