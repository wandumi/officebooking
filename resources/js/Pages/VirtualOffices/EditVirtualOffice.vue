<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    virtualoffices: Object,
    locations: Array,
    pricings: Array,
});

const form = useForm({
    virtualoffice_name: props.virtualoffices.virtualoffice_name,
    location_id: props.virtualoffices.location_id,
    address: props.virtualoffices.address,
    duration: props.virtualoffices.duration,
    discount: props.virtualoffices.discount,
    handling: props.virtualoffices.handling,
    price: props.virtualoffices.price,
    phone_number: props.virtualoffices.phone_number,
    pricing_type: [props.virtualoffices.price_premium, props.virtualoffices.price_standard],
});

const cleanPricingType = () => {
    form.pricing_type = form.pricing_type.filter(value => value !== null);
};

const submit = () => {
    cleanPricingType();
    form.put(route('admin.virtual-office.update', props.virtualoffices.id), {
        onSuccess: () => {
            router.visit(route('admin.virtual-offices'));
        },
    });
};
</script>

<template>
    <Head title="Edit Virtual Office" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Virtual Office</h2>
        </template>

        <div class="p-2">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="max-w-4xl p-6 mx-auto space-y-6">
                    <!-- Search Filter -->
                    <div class="flex flex-col gap-3 mb-4 sm:flex-row sm:items-center sm:justify-between">
                        <h3 class="inline-block py-2 text-2xl font-medium text-black">Edit Virtual Office</h3>

                        <Link
                            :href="route('admin.virtual-offices')"
                            class="inline-block px-4 py-2 text-lg font-medium text-white rounded bg-bluemain hover:bg-bluemain/60">
                            Back
                        </Link>
                    </div>
                    <form
                        @submit.prevent="submit"
                        class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Name -->
                            <div>
                                <label class="block text-lg font-medium text-gray-700">Virtual Office Name</label>
                                <input
                                    v-model="form.virtualoffice_name"
                                    type="text"
                                    class="w-full px-3 py-2 border rounded" />
                                <div
                                    v-if="form.errors.virtualoffice_name"
                                    class="text-sm text-red-600">
                                    {{ form.errors.virtualoffice_name }}
                                </div>
                            </div>

                            <!-- Location -->
                            <div>
                                <label class="block text-lg font-medium text-gray-700">Location</label>
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
                                <label class="block text-lg font-medium text-gray-700">Price</label>
                                <input
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full px-3 py-2 border rounded"
                                    placeholder="e.g. 2000.00" />
                                <div
                                    v-if="form.errors.price"
                                    class="text-sm text-red-600">
                                    {{ form.errors.price }}
                                </div>
                            </div>

                            <!-- Discount -->
                            <div>
                                <label class="block text-lg font-medium text-gray-700">Discount</label>
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

                            <!-- Address -->
                            <div>
                                <label class="block text-lg font-medium text-gray-700">Address</label>
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

                            <!-- Pricing Type -->
                            <div>
                                <label class="block text-lg font-medium text-gray-700">Pricing Type</label>
                                <div class="flex flex-col space-y-2">
                                    <div
                                        v-for="pricing in pricings"
                                        :key="pricing.id"
                                        class="flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="form.pricing_type"
                                            :value="pricing.rate"
                                            :id="pricing.id"
                                            class="form-checkbox" />

                                        <label
                                            :for="pricing.id"
                                            class="ml-2 text-sm">
                                            {{ pricing.category_name }} - {{ pricing.pricing_type }} -
                                            {{
                                                pricing.rate
                                                    ? `R ${Number(pricing.rate).toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                                                    : 'None'
                                            }}
                                        </label>
                                    </div>
                                </div>
                                <div
                                    v-if="form.errors.pricing_type"
                                    class="text-sm text-red-600">
                                    {{ form.errors.pricing_type }}
                                </div>
                            </div>
                        </div>
                        <div class="w-full pt-4 md:col-span-2">
                            <button
                                type="submit"
                                class="block w-full px-4 py-2 text-lg font-medium text-white rounded bg-bluemain hover:bg-bluemain/60"
                                :disabled="form.processing">
                                Update Virtual Office
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
