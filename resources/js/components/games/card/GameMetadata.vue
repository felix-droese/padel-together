<script setup lang="ts">
import { Clock, MapPin } from 'lucide-vue-next';

const props = defineProps<{
    game: App.DTOs.TGame;
    locations: App.DTOs.TLocation[];
}>();
</script>

<template>
    <div class="mt-4 flex items-center gap-4 text-[10px] text-muted-foreground sm:text-sm">
        <div class="flex items-center gap-1">
            <Clock class="h-3 w-3 sm:h-4 sm:w-4" />
            <span>
                {{ new Date(game.date).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) }}
                {{ new Date(game.date).toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' }) }}
            </span>
        </div>
        <div class="flex items-center gap-1">
            <MapPin class="h-3 w-3 sm:h-4 sm:w-4" />
            <span>{{ locations.find((l) => l.id === game.location_id)?.name }}</span>
        </div>
    </div>
</template>
