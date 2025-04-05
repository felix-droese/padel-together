<script setup lang="ts">
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from './index';
import { Button } from '@/components/ui/button';
import { X, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Option {
    value: string | number;
    label: string;
}

const props = defineProps<{
    modelValue: string | number | undefined | null;
    options: Option[];
    placeholder?: string;
    disabled?: boolean;
    clearable?: boolean;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string | number | undefined | null];
}>();

const searchTerm = ref('');
const isOpen = ref(false);

const selectedOption = computed(() => {
    return props.options.find(option => option.value === props.modelValue);
});

const filteredOptions = computed(() => {
    if (!searchTerm.value) return props.options;
    const term = searchTerm.value.toLowerCase();
    return props.options.filter(option =>
        option.label.toLowerCase().includes(term)
    );
});

function clearSelection() {
    emit('update:modelValue', undefined);
}

function handleSearchInput(event: Event) {
    const input = event.target as HTMLInputElement;
    searchTerm.value = input.value;
}
</script>

<template>
    <div class="flex gap-2">
        <Select
            :model-value="modelValue"
            @update:model-value="emit('update:modelValue', $event)"
            :disabled="disabled"
            @open-change="isOpen = $event"
        >
            <SelectTrigger>
                <SelectValue>
                    {{ selectedOption?.label || placeholder || 'Select an option' }}
                </SelectValue>
            </SelectTrigger>
            <SelectContent>
                <div class="relative px-2 py-1">
                    <Search class="absolute left-4 top-2.5 h-4 w-4 text-muted-foreground" />
                    <input
                        type="text"
                        :value="searchTerm"
                        @input="handleSearchInput"
                        placeholder="Search..."
                        class="w-full rounded-md border border-input bg-background px-8 py-1 text-sm outline-none placeholder:text-muted-foreground focus:ring-1 focus:ring-ring"
                    />
                </div>
                <div class="max-h-[300px] overflow-y-auto">
                    <SelectItem
                        v-for="option in filteredOptions"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </SelectItem>
                </div>
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
