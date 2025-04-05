<script setup lang="ts">
import { Listbox } from '@headlessui/vue';
import { cn } from '@/lib/utils';

const model = defineModel<string>();
const props = defineProps<{
  disabled?: boolean;
}>();
</script>

<template>
  <Listbox v-model="model" :disabled="props.disabled" :class="cn('w-full', $attrs.class ?? '')">
    <slot />
  </Listbox>
</template>
