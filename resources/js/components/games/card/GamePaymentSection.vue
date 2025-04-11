<script setup lang="ts">
import { Button } from '@/components/ui/button';
import type { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ChevronDown, ChevronUp } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import PaymentRow from './PaymentRow.vue';
import PaymentStatusBadge from './PaymentStatusBadge.vue';

const props = defineProps<{
    game: App.DTOs.TGame;
}>();

const page = usePage<SharedData>();
const auth = computed(() => page.props.auth);
const isProcessingPayment = ref(false);
const showPayments = ref(false);

const completedPaymentsCount = computed(() => {
    return props.game.payments.filter((payment) => payment.status === 'completed').length;
});

const totalPaymentsCount = computed(() => {
    return props.game.payments.length;
});

const allPaymentsCompleted = computed(() => {
    return completedPaymentsCount.value === totalPaymentsCount.value;
});

function formatPrice(amountInCent: number): string {
    return `€${(amountInCent / 100).toFixed(2)}`;
}

const userPayment = computed(() => {
    return props.game.payments.find((payment) => payment.user.id === auth.value.user?.id);
});

const isPayer = computed(() => {
    return props.game.payer?.id === auth.value.user?.id;
});

const otherPayments = computed(() => {
    return props.game.payments.filter((payment) => payment.user.id !== auth.value.user?.id);
});

const payer = computed(() => {
    return props.game.payer;
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
    <!-- Payment Overview -->
    <div v-if="isPayer && otherPayments.length > 0" class="mt-4 space-y-3 text-sm">
        <div class="flex items-center gap-2">
            <div class="flex cursor-pointer items-center gap-2" @click="showPayments = !showPayments">
                <h4 class="font-medium">Payments Overview</h4>
                <ChevronDown v-if="!showPayments" class="h-4 w-4" />
                <ChevronUp v-else class="h-4 w-4" />
            </div>
            <span
                class="rounded-full px-2 py-0.5 text-sm"
                :class="allPaymentsCompleted ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
            >
                {{ completedPaymentsCount }}/{{ totalPaymentsCount }}
            </span>
        </div>
        <p class="mt-1 text-muted-foreground">You paid {{ formatPrice(props.game.price_in_cent) }}</p>
        <div v-if="showPayments" class="mt-4 max-w-[360px] divide-y divide-border rounded-lg border">
            <PaymentRow v-for="payment in otherPayments" :key="payment.id" :payment="payment" />
        </div>
    </div>

    <!-- Pending Payment -->
    <div v-if="userPayment" class="mt-4 space-y-3 text-sm">
        <div class="flex items-center gap-2">
            <h4 class="font-medium">Your Payment</h4>
            <span class="tabular-nums">{{ formatPrice(userPayment.amount_in_cent) }}</span>
            <PaymentStatusBadge :status="userPayment.status" />
        </div>
        <p v-if="payer" class="mt-1.5 text-sm text-muted-foreground">
            {{ userPayment.status === 'completed' ? 'Paid to:' : 'Pay to:' }} {{ payer.email }}
        </p>
        <Button v-if="userPayment.status === 'pending'" class="mt-4 w-full" :disabled="isProcessingPayment" @click="createPayment">
            {{ isProcessingPayment ? 'Processing...' : 'Pay with PayPal' }}
        </Button>
    </div>
</template>
