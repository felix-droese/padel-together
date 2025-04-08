<script setup lang="ts">
const props = defineProps<{
    status: string;
}>();

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
    <span :class="getStatusClasses(props.status)">{{ props.status }}</span>
</template>
