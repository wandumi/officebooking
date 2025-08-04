<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <div class="px-4 py-6 sm:px-4 lg:px-2">
            <div class="max-w-md mx-auto sm:max-w-2xl md:max-w-3xl lg:max-w-5xl">
                <div class="overflow-hidden bg-white rounded-md shadow">
                    <div class="p-4 text-base text-gray-900 sm:p-6 sm:text-lg">You're logged in!</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
