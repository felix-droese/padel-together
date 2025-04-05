<script setup lang="ts">
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from './index';
import { Button } from '@/components/ui/button';
import { X } from 'lucide-vue-next';
import { computed } from 'vue';

interface Option {
    value: string | number;
    label: string;
}

const props = defineProps<{
    modelValue: string | number | undefined;
    options: Option[];
    placeholder?: string;
    disabled?: boolean;
    clearable?: boolean;
    class?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string | number | undefined];
}>();

const selectedOption = computed(() => {
    return props.options.find(option => option.value === props.modelValue);
});

function clearSelection() {
    emit('update:modelValue', undefined);
}
</script>

<template>
    <div class="flex gap-2">
        <Select
            :model-value="modelValue"
            @update:model-value="emit('update:modelValue', $event)"
            :disabled="disabled"
            :class="class"
        >
            <SelectTrigger>
                <SelectValue>
                    {{ selectedOption?.label || placeholder || 'Select an option' }}
                </SelectValue>
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="option in options"
                    :key="option.value"
                    :value="option.value"
                >
                    {{ option.label }}
                </SelectItem>
            </SelectContent>
        </Select>
        <Button
            v-if="clearable && modelValue"
            @click="clearSelection"
            variant="ghost"
            size="icon"
            class="h-10 w-10"
        >
            <X class="h-4 w-4" />
        </Button>
    </div>
</template>
