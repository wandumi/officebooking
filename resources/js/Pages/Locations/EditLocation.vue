<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    locations: Object,
});

const form = useForm({
    name: props.locations.name,
    address: props.locations.address,
    city: props.locations.city,
});

const submit = () => {
    form.put(route('admin.location.update', props.locations.id), {
        onSuccess: () => {
            router.visit(route('admin.locations'));
        },
    });
};
</script>

<template>
    <Head title="Edit Amenity" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Amenity</h2>
        </template>

        <div class="p-2">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="max-w-4xl p-6 mx-auto space-y-6">
                    <!-- Search Filter -->
                    <div class="flex flex-col gap-3 mb-4 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="inline-block py-2 text-2xl font-medium text-black">Edit Location</h3>

                        <Link
                            :href="route('admin.locations')"
                            class="inline-block px-3 py-2 text-sm font-medium text-white rounded bg-bluemain hover:bg-bluemain/60">
                            Back
                        </Link>
                    </div>

                    <form
                        @submit.prevent="submit"
                        class="space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Office Name -->
                            <div>
                                <label class="w-full text-sm font-medium text-gray-700">Name</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full px-3 py-2 border rounded" />
                                <div
                                    v-if="form.errors.name"
                                    class="text-sm text-red-600">
                                    {{ form.errors.name }}
                                </div>
                            </div>
                            <!-- Address -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                <input
                                    v-model="form.address"
                                    type="text"
                                    class="w-full px-3 py-2 border rounded" />
                                <div
                                    v-if="form.errors.address"
                                    class="text-sm text-red-600">
                                    {{ form.errors.address }}
                                </div>
                            </div>

                            <!-- Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">City</label>
                                <input
                                    v-model="form.city"
                                    type="text"
                                    class="w-full px-3 py-2 border rounded" />
                                <div
                                    v-if="form.errors.city"
                                    class="text-sm text-red-600">
                                    {{ form.errors.city }}
                                </div>
                            </div>
                        </div>
                        <div class="w-full pt-2 md:col-span-2">
                            <button
                                type="submit"
                                class="block w-full px-2 py-2 text-sm font-medium text-white rounded bg-bluemain hover:bg-bluemain/60"
                                :disabled="form.processing">
                                Update Location
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
