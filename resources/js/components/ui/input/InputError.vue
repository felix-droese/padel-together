<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <div v-if="message" class="text-sm text-destructive">{{ message }}</div>
</template>
