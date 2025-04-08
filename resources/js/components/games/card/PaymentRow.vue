<script setup lang="ts">
import PaymentStatusBadge from './PaymentStatusBadge.vue';

const props = defineProps<{
    payment: App.DTOs.TGamePayment;
}>();

function formatPrice(amountInCent: number): string {
    return `€${(amountInCent / 100).toFixed(2)}`;
}
</script>

<template>
    <div class="flex items-center justify-between gap-2 p-3">
        <div>
            <span class="mr-2 font-medium">{{ props.payment.player.first_name }} {{ props.payment.player.last_name }}</span>
            <PaymentStatusBadge :status="props.payment.status" />
        </div>
        <div>
            <span class="tabular-nums">{{ formatPrice(props.payment.amount_in_cent) }}</span>
        </div>
    </div>
</template>
