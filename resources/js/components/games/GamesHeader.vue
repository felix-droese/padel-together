<script setup lang="ts">
const props = defineProps<{
    locations: App.DTOs.TLocation[];
}>();

const selectedLocationIds = defineModel<number[]>('selectedLocationIds', { required: true });

function toggleLocation(locationId: number) {
    if (selectedLocationIds.value.includes(locationId)) {
        selectedLocationIds.value = selectedLocationIds.value.filter((id) => id !== locationId);
    } else {
        selectedLocationIds.value.push(locationId);
    }
}
</script>

<template>
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
</template>
