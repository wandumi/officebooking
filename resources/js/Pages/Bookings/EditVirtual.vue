<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import BookingVirtual from '@/Components/BookingVirtual.vue';

interface Location {
    name: string;
}

interface Virtual {
    id?: number;
    location?: Location;
    virtualoffice_name?: string;
    address?: string;
    discount: number;
    phone_number: number;
    price: number;
    handling: string;
    duration: number;
    price_premium: number;
    price_standard: number;
}

const props = defineProps<{
    virtual: Virtual;
    locations: Location;
}>();

const viewMode = ref<'form' | 'calendar' | null>(null);

const pricingOptions = {
    premium: props.virtual.price_premium,
    standard: props.virtual.price_standard,
};

const availablePlans = Object.keys(pricingOptions).filter(key => pricingOptions[key] != null);

function book() {
    viewMode.value = 'form';
}

function checkAvailability() {
    viewMode.value = 'calendar';
}
</script>

<template>
    <Head title="View Virtual Office" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">View Virtual Office</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-4 lg:px-8">
                <div class="flex justify-center">
                    <div class="w-full max-w-2xl p-6 space-y-6 bg-white border rounded-lg shadow-md">
                        <!-- Header -->
                        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                            <h3 class="text-xl font-semibold">{{ virtual.virtualoffice_name || 'Virtual Office' }}</h3>
                            <Link
                                :href="route('booking.offices')"
                                class="inline-block w-full px-4 py-1 text-sm font-medium text-center text-white bg-blue-600 rounded md:w-auto hover:bg-blue-700">
                                Back
                            </Link>
                        </div>

                        <!-- Details -->
                        <div class="grid grid-cols-1 gap-6 text-sm text-gray-700 md:grid-cols-2">
                            <div class="space-y-2">
                                <p><strong>Location:</strong> {{ virtual.location?.name || 'N/A' }}</p>
                                <p><strong>Address:</strong> {{ virtual.address || 'N/A' }}</p>
                                <p><strong>Phone Number:</strong> {{ virtual.phone_number || 'N/A' }}</p>
                                <p><strong>Base Price:</strong> R{{ virtual.price || '0.00' }}</p>
                                <p><strong>Handling:</strong> {{ virtual.handling || 'N/A' }}</p>
                                <p><strong>Duration:</strong> {{ virtual.duration || 'N/A' }} days</p>
                            </div>

                            <div class="space-y-2">
                                <h4 class="font-semibold text-gray-800">Pricing Options</h4>
                                <p v-if="virtual.price_premium">
                                    Premium: <strong>R{{ virtual.price_premium }}</strong>
                                </p>
                                <p v-if="virtual.price_standard">
                                    Standard: <strong>R{{ virtual.price_standard }}</strong>
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-x-2">
                            <button
                                @click="book"
                                class="px-4 py-1 text-sm font-semibold text-white bg-pink-600 rounded hover:bg-pink-700">
                                Book {{ virtual.virtualoffice_name || 'Office' }}
                            </button>
                            <button
                                @click="checkAvailability"
                                class="px-4 py-1 text-sm font-semibold text-white bg-blue-600 rounded hover:bg-blue-700">
                                Check Availability
                            </button>
                        </div>

                        <!-- Booking Form -->
                        <div
                            v-if="viewMode === 'form'"
                            class="pt-6 mt-6 border-t border-gray-200">
                            <BookingVirtual
                                :buttonName="virtual.virtualoffice_name"
                                :bookable-type="'App\\\\Models\\\\VirtualOffice'"
                                :bookable-id="virtual.id"
                                :pricing-options="pricingOptions"
                                :available-plans="availablePlans"
                                :selected-plan="availablePlans[0]" />
                        </div>

                        <!-- Calendar Placeholder -->
                        <div
                            v-if="viewMode === 'calendar'"
                            class="pt-6 mt-6 border-t border-gray-200">
                            <div class="p-4 text-center text-gray-600 border rounded">
                                📅 Calendar Placeholder: Show availability for
                                <strong>{{ virtual.virtualoffice_name }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
