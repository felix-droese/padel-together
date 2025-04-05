<script setup lang="ts">
import { Listbox } from '@headlessui/vue';
import { cn } from '@/lib/utils';

const model = defineModel<string>();
const props = defineProps<{
  disabled?: boolean;
  class?: string;
}>();
</script>

<template>
  <Listbox v-model="model" :disabled="props.disabled" as="div" :class="cn('w-full', props.class)">
    <div class="relative">
      <slot :value="model" />
    </div>
  </Listbox>
</template>
