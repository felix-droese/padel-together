<script setup lang="ts">
import GameCard from '@/components/games/GameCard.vue';
import GameForm from '@/components/games/GameForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { SharedData, User, type BreadcrumbItem } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    games: App.DTOs.TGame[];
    locations: App.DTOs.TLocation[];
    players: App.DTOs.TPlayer[];
}>();

const selectedLocationIds = ref<number[]>([]);
const isProcessingPayment = ref(false);

const filteredGames = computed(() => {
    if (selectedLocationIds.value.length === 0) return props.games;
    return props.games.filter((game) => selectedLocationIds.value.includes(game.location_id));
});

function toggleLocation(locationId: number) {
    const index = selectedLocationIds.value.indexOf(locationId);
    if (index === -1) {
        selectedLocationIds.value.push(locationId);
    } else {
        selectedLocationIds.value.splice(index, 1);
    }
}

async function createPayment() {
    try {
        isProcessingPayment.value = true;
        await router.post(route('payments.create'), { amount: '20.00' });
    } catch (error) {
        console.error('Payment creation failed:', error);
    } finally {
        isProcessingPayment.value = false;
    }
}

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Games',
        href: '/games',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl space-y-16">
            <GameForm class="mb-10" :locations="props.locations" :players="props.players" v-if="user" />

            <div>
                <div class="mb-4">
                    <button
                        @click="createPayment"
                        :disabled="isProcessingPayment"
                        class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground ring-offset-background transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                    >
                        {{ isProcessingPayment ? 'Processing...' : 'Test PayPal Payment (€20)' }}
                    </button>
                </div>

                <h2 class="text-xl font-semibold"></h2>
                <div v-if="props.games.length === 0" class="text-sm text-muted-foreground">No open games available.</div>
                <div v-else class="grid gap-4">
                    <div class="flex gap-4">
                        <h3 class="text-lg font-semibold">Games</h3>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="location in props.locations"
                                :key="location.id"
                                @click="toggleLocation(location.id)"
                                class="inline-flex items-center rounded-full border px-3 py-1 text-sm transition-colors"
                                :class="{
                                    'border-primary bg-primary text-primary-foreground': selectedLocationIds.includes(location.id),
                                    'border-border hover:bg-accent hover:text-accent-foreground': !selectedLocationIds.includes(location.id),
                                }"
                            >
                                {{ location.name }}
                            </button>
                        </div>
                    </div>

                    <GameCard v-for="game in filteredGames" :key="game.id" :game="game" :locations="props.locations" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
