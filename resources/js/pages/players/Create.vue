<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Players',
        href: '/players',
    },
    {
        title: 'Create',
        href: '/',
    },
];

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
});

function submit() {
    form.post(route('players.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
}
</script>

<template>
    <Head title="Create Player" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl">
            <h1 class="mb-8 text-2xl font-semibold">Create Player</h1>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-2">
                    <Label for="first_name">First Name</Label>
                    <Input id="first_name" v-model="form.first_name" type="text" placeholder="Enter first name" :disabled="form.processing" />
                    <InputError :message="form.errors.first_name" />
                </div>

                <div class="space-y-2">
                    <Label for="last_name">Last Name</Label>
                    <Input id="last_name" v-model="form.last_name" type="text" placeholder="Enter last name" :disabled="form.processing" />
                    <InputError :message="form.errors.last_name" />
                </div>

                <div class="space-y-2">
                    <Label for="email">Email <span class="text-gray-500">(optional)</span></Label>
                    <Input id="email" v-model="form.email" type="email" placeholder="Enter email (optional)" :disabled="form.processing" />
                    <InputError :message="form.errors.email" />
                </div>

                <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Creating...' : 'Create Player' }}
                </Button>
            </form>
        </div>
    </AppLayout>
</template>
