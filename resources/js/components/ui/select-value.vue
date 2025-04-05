<script setup lang="ts">
import { ListboxLabel } from '@headlessui/vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
  placeholder?: string;
  class?: string;
}>();
</script>

<template>
  <ListboxLabel
    :class="cn('text-sm', props.class)"
  >
    <slot>{{ props.placeholder }}</slot>
  </ListboxLabel>
</template>
