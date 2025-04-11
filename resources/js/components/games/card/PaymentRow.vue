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
            <span class="mr-2 font-medium">{{ props.payment.user.email }}</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="tabular-nums">{{ formatPrice(props.payment.amount_in_cent) }}</span>
            <PaymentStatusBadge :status="props.payment.status" />
        </div>
    </div>
</template>
