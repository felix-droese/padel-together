<script setup lang="ts">
import { cn } from '@/lib/utils';

const props = defineProps<{
  placeholder?: string;
  class?: string;
}>();
</script>

<template>
  <span :class="cn('block truncate', props.class)">
    <slot>{{ props.placeholder }}</slot>
  </span>
</template>
