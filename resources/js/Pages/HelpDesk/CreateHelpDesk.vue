<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const form = useForm({
    location_id: '',
    help_desk_name: '',
    price: '',
    duration: '',
    desks: '',
    discount: '',
});

const props = defineProps({
    locations: Array,
});
</script>

<template>
    <Head title="Create an Office" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Hot Desk</h2>
        </template>

        <div class="py-2">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="max-w-4xl p-6 mx-auto space-y-6">
                    <div class="flex flex-col gap-3 mb-4 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="inline-block py-2 text-2xl font-medium text-black">Create Hot Desk</h3>

                        <Link
                            :href="route('admin.help-desks')"
                            class="inline-block px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                            Back
                        </Link>
                    </div>

                    <form
                        @submit.prevent="form.post(route('admin.help-desk.store'))"
                        class="space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- help Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Hot Desk Name</label>
                                    <input
                                        v-model="form.help_desk_name"
                                        type="text"
                                        class="w-full px-3 py-2 border rounded" />
                                    <div
                                        v-if="form.errors.help_desk_name"
                                        class="text-sm text-red-600">
                                        {{ form.errors.help_desk_name }}
                                    </div>
                                </div>

                                <!-- Location -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Location</label>
                                    <select
                                        v-model="form.location_id"
                                        class="w-full px-3 py-2 border rounded">
                                        <option value="">Select Location</option>
                                        <option
                                            v-for="loc in locations"
                                            :key="loc.id"
                                            :value="loc.id">
                                            {{ loc.name }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="form.errors.location_id"
                                        class="text-sm text-red-600">
                                        {{ form.errors.location_id }}
                                    </div>
                                </div>

                                <!-- Price -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Price</label>
                                    <input
                                        v-model="form.price"
                                        type="number"
                                        step="1"
                                        min="0"
                                        class="w-full px-3 py-2 border rounded"
                                        placeholder="e.g. 2" />
                                    <div
                                        v-if="form.errors.price"
                                        class="text-sm text-red-600">
                                        {{ form.errors.price }}
                                    </div>
                                </div>

                                <!-- Office Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Duration</label>
                                    <input
                                        v-model="form.duration"
                                        type="number"
                                        class="w-full px-3 py-2 border rounded" />
                                    <div
                                        v-if="form.errors.duration"
                                        class="text-sm text-red-600">
                                        {{ form.errors.duration }}
                                    </div>
                                </div>

                                <!-- Price -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Desks</label>
                                    <input
                                        v-model="form.desks"
                                        type="number"
                                        step="1"
                                        min="0"
                                        class="w-full px-3 py-2 border rounded"
                                        placeholder="e.g. 2" />
                                    <div
                                        v-if="form.errors.desks"
                                        class="text-sm text-red-600">
                                        {{ form.errors.desks }}
                                    </div>
                                </div>

                                <!-- Discount -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Discount</label>
                                    <input
                                        v-model="form.discount"
                                        type="number"
                                        step="1"
                                        min="0"
                                        class="w-full px-3 py-2 border rounded"
                                        placeholder="e.g. 2" />
                                    <div
                                        v-if="form.errors.discount"
                                        class="text-sm text-red-600">
                                        {{ form.errors.discount }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full pt-4 md:col-span-2">
                            <button
                                type="submit"
                                class="block w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700"
                                :disabled="form.processing">
                                Create Help Desk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
