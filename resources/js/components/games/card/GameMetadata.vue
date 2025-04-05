<script setup lang="ts">
import { Clock, MapPin } from 'lucide-vue-next';

const props = defineProps<{
    date: string;
    locationName: string;
}>();
</script>

<template>
    <div class="mt-4 flex items-center gap-4 text-[10px] text-muted-foreground sm:text-sm">
        <div class="flex items-center gap-1">
            <Clock class="h-3 w-3 sm:h-4 sm:w-4" />
            <span>
                {{ new Date(props.date).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) }}
                {{ new Date(props.date).toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' }) }}
            </span>
        </div>
        <div class="flex items-center gap-1">
            <MapPin class="h-3 w-3 sm:h-4 sm:w-4" />
            <span>{{ props.locationName }}</span>
        </div>
    </div>
</template>
