<script setup lang="ts">
import { Button } from '@/components/ui/button';
import type { SharedData } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import GameMetadata from './card/GameMetadata.vue';
import GameResultDisplay from './card/GameResultDisplay.vue';
import GameResultForm from './card/GameResultForm.vue';
import TeamDisplay from './card/TeamDisplay.vue';

const props = defineProps<{
    game: App.DTOs.TGame;
    locations: App.DTOs.TLocation[];
}>();

const page = usePage<SharedData>();
const auth = computed(() => page.props.auth);

const deleteForm = useForm({});
const isProcessingPayment = ref(false);

function deleteGame() {
    if (confirm('Are you sure you want to delete this game?')) {
        deleteForm.delete(route('games.destroy', { game: props.game.id }));
    }
}

const canEditResult = computed(() => {
    const firstTeamHasTwoPlayers = props.game.first_team.players.length === 2;
    const secondTeamHasTwoPlayers = props.game.second_team?.players.length === 2;
    return firstTeamHasTwoPlayers && secondTeamHasTwoPlayers;
});

const isPlayerInGame = computed(() => {
    if (!auth.value.user) return false;

    const userId = auth.value.user.id;
    const firstTeamPlayers = props.game.first_team.players.map((p) => p.id);
    const secondTeamPlayers = props.game.second_team?.players.map((p) => p.id) || [];

    return firstTeamPlayers.includes(userId) || secondTeamPlayers.includes(userId);
});

const userPayment = computed(() => {
    if (!auth.value.user) return null;
    return props.game.payments.find((payment) => payment.player.user?.id === auth.value.user.id);
});

async function createPayment() {
    try {
        isProcessingPayment.value = true;
        const response = await fetch(route('game-payments.create', { game: props.game.id }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': document.cookie.match(/XSRF-TOKEN=([\w-]+)/)?.[1] || '',
            },
        });

        const data = await response.json();

        if (data.paypalUrl) {
            window.location.href = data.paypalUrl;
        }
    } catch (error) {
        console.error('Payment creation failed:', error);
    } finally {
        isProcessingPayment.value = false;
    }
}
</script>

<template>
    <div class="space-y-4 rounded-lg border bg-card p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <TeamDisplay :team="props.game.first_team" :elo-changes="props.game.elo_changes" />
                <span class="mx-8 text-muted-foreground">vs</span>
                <TeamDisplay v-if="props.game.second_team" :team="props.game.second_team" :elo-changes="props.game.elo_changes" />
            </div>
            <div v-if="isPlayerInGame" class="flex items-center gap-2">
                <GameResultForm v-if="canEditResult" :game="props.game" />
                <Button variant="ghost" size="icon" @click="deleteGame">
                    <Trash2 class="h-4 w-4" />
                </Button>
            </div>
        </div>
        <GameMetadata :game="props.game" :locations="props.locations" />
        <GameResultDisplay v-if="props.game.result" :game="props.game" />

        <!-- Payment Section -->
        <div v-if="userPayment" class="mt-4 rounded-lg border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium">Your Payment</h3>
                    <p class="text-sm text-muted-foreground">Amount: €{{ (userPayment.amount_in_cent / 100).toFixed(2) }}</p>
                </div>
                <div>
                    <span
                        :class="{
                            'rounded-full px-2 py-1 text-xs font-medium': true,
                            'bg-green-100 text-green-800': userPayment.status === 'completed',
                            'bg-yellow-100 text-yellow-800': userPayment.status === 'pending',
                            'bg-red-100 text-red-800': userPayment.status === 'failed',
                        }"
                    >
                        {{ userPayment.status }}
                    </span>
                </div>
            </div>
            <Button v-if="userPayment.status === 'pending'" class="mt-4 w-full" :disabled="isProcessingPayment" @click="createPayment">
                {{ isProcessingPayment ? 'Processing...' : 'Pay with PayPal' }}
            </Button>
        </div>
    </div>
</template>
