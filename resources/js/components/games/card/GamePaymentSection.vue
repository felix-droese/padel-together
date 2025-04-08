<script setup lang="ts">
import { Button } from '@/components/ui/button';
import type { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    game: App.DTOs.TGame;
}>();

const page = usePage<SharedData>();
const auth = computed(() => page.props.auth);

const isProcessingPayment = ref(false);

function formatPrice(amountInCent: number): string {
    return `€${(amountInCent / 100).toFixed(2)}`;
}

const userPayment = computed(() => {
    return props.game.payments.find((payment) => payment.player.user?.id === auth.value.user?.id);
});

const isPayer = computed(() => {
    return props.game.payments.some((payment) => payment.payer?.id === auth.value.user?.id);
});

const otherPayments = computed(() => {
    return props.game.payments.filter((payment) => payment.player.user?.id !== auth.value.user?.id);
});

const payer = computed(() => {
    return props.game.payments.find((payment) => payment.payer)?.payer;
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

const getStatusClasses = (status: string) => {
    const baseClasses = 'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset';
    const statusClasses = {
        completed: 'bg-green-50 text-green-700 ring-green-600/20',
        pending: 'bg-yellow-50 text-yellow-800 ring-yellow-600/20',
        failed: 'bg-red-50 text-red-700 ring-red-600/20',
    };
    return `${baseClasses} ${statusClasses[status as keyof typeof statusClasses]}`;
};
</script>

<template>
    <!-- Your Payment Section -->
    <div v-if="!isPayer && userPayment" class="mt-4 rounded-lg border p-4">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <h3 class="font-medium">Your Payment</h3>
                    <span class="tabular-nums">{{ formatPrice(userPayment.amount_in_cent) }}</span>
                    <span :class="getStatusClasses(userPayment.status)">{{ userPayment.status }}</span>
                </div>
                <p v-if="payer" class="text-sm text-muted-foreground">Pay to: {{ payer.email }}</p>
            </div>
        </div>
        <Button v-if="userPayment.status === 'pending'" class="mt-4 w-full" :disabled="isProcessingPayment" @click="createPayment">
            {{ isProcessingPayment ? 'Processing...' : 'Pay with PayPal' }}
        </Button>
    </div>

    <!-- Other Players' Payments Section -->
    <div v-if="isPayer && userPayment" class="space-y-3 text-sm">
        <h4 class="mt-8 font-medium">Payments (you paid {{ formatPrice(game.price_in_cent) }})</h4>
        <div class="max-w-[360px] divide-y divide-border rounded-lg border">
            <div v-for="payment in otherPayments" :key="payment.id" class="flex items-center justify-between gap-2 p-3">
                <div>
                    <span class="mr-2 font-medium">{{ payment.player.first_name }} {{ payment.player.last_name }}</span>
                    <span :class="getStatusClasses(payment.status)">{{ payment.status }}</span>
                </div>
                <div>
                    <span class="tabular-nums">{{ formatPrice(payment.amount_in_cent) }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
