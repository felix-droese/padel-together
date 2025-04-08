<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { onMounted } from 'vue';

const props = defineProps<{
    paypalUrl: string;
}>();

onMounted(() => {
    window.location.href = props.paypalUrl;
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Payment',
        href: '#',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex min-h-[400px] items-center justify-center">
            <div class="text-center">
                <h1 class="text-2xl font-semibold">Redirecting to PayPal...</h1>
                <p class="mt-2 text-muted-foreground">Please wait while we redirect you to PayPal.</p>
            </div>
        </div>
    </AppLayout>
</template>
