<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select/index';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    locations: App.DTOs.TLocation[];
    players: App.DTOs.TPlayer[];
    games: App.DTOs.TGame[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const locationForm = useForm({
    name: '',
});

const gameForm = useForm({
    date: '',
    location_id: 0,
});

function submitLocation() {
    locationForm.post(route('locations.store'), {
        onSuccess: () => {
            locationForm.reset();
        },
    });
}

function submitGame() {
    gameForm.post(route('games.store'), {
        onSuccess: () => {
            gameForm.reset();
        },
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl space-y-6">
            <div class="space-y-2">
                <h2 class="text-xl font-semibold">Add New Location</h2>
                <p class="text-sm text-muted-foreground">Create a new location for your padel matches.</p>
            </div>

            <form @submit.prevent="submitLocation" class="space-y-4">
                <div class="space-y-2">
                    <Label for="name">Location Name</Label>
                    <Input id="name" v-model="locationForm.name" type="text" placeholder="Enter location name" :disabled="locationForm.processing" />
                    <InputError :message="locationForm.errors.name" />
                </div>

                <Button type="submit" :disabled="locationForm.processing">
                    {{ locationForm.processing ? 'Creating...' : 'Create Location' }}
                </Button>
            </form>

            <div class="space-y-4">
                <h2 class="text-xl font-semibold">Create New Game</h2>
                <p class="text-sm text-muted-foreground">Create a new padel game with another player.</p>
            </div>

            <form @submit.prevent="submitGame" class="space-y-4">
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

            <div class="space-y-4">
                <h2 class="text-xl font-semibold">Existing Locations</h2>
                <div v-if="props.locations.length === 0" class="text-sm text-muted-foreground">No locations have been created yet.</div>
                <div v-else class="grid gap-4">
                    <div v-for="location in props.locations" :key="location.id" class="rounded-lg border p-4">
                        <h3 class="font-medium">{{ location.name }}</h3>
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
        </div>
    </AppLayout>
</template>
