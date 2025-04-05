<script setup lang="ts">
import { ListboxOption } from '@headlessui/vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
  value: string;
  class?: string;
}>();
</script>

<template>
  <ListboxOption :value="props.value" :class="cn('relative flex w-full cursor-default select-none items-center rounded-sm py-2 px-4 hover:bg-gray-100 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50', props.class)">
    <slot />
  </ListboxOption>
</template>
