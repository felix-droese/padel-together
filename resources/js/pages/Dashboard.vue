<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

interface Location {
    id: number;
    name: string;
}

defineProps<{
    locations: Location[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('locations.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl space-y-6">
            <div class="space-y-2">
                <h2 class="text-xl font-semibold">Add New Location</h2>
                <p class="text-sm text-muted-foreground">Create a new location for your padel matches.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-2">
                    <Label for="name">Location Name</Label>
                    <Input id="name" v-model="form.name" type="text" placeholder="Enter location name" :disabled="form.processing" />
                    <InputError :message="form.errors.name" />
                </div>

                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Creating...' : 'Create Location' }}
                </Button>
            </form>

            <div class="space-y-4">
                <h2 class="text-xl font-semibold">Existing Locations</h2>
                <div v-if="locations.length === 0" class="text-sm text-muted-foreground">No locations have been created yet.</div>
                <div v-else class="grid gap-4">
                    <div v-for="location in locations" :key="location.id" class="rounded-lg border p-4">
                        <h3 class="font-medium">{{ location.name }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
