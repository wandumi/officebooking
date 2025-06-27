<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import BookingHotDesk from '@/Components/BookingHotDesk.vue';
import { ref } from 'vue';

interface Location {
    name: string;
}

interface HelpDesk {
    id?: number;
    help_desk_name?: string;
    location?: Location;
    price?: number;
    duration?: number | string;
    discount?: number;
    desks?: number;
}

const props = defineProps<{
    helpDesks: HelpDesk;
    locations: Location;
}>();

const viewMode = ref<'form' | 'calendar' | null>(null);

const pricingOptions = {
    price: props.helpDesks.price,
};

function book() {
    viewMode.value = 'form';
}

function checkAvailability() {
    viewMode.value = 'calendar';
}
</script>

<template>
    <Head title="View Hot Desk" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">View Hot Desk</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-4 lg:px-8">
                <div class="flex justify-center">
                    <div class="w-full max-w-2xl p-6 space-y-6 bg-white border rounded-lg shadow-md">
                        <!-- Header -->
                        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                            <h3 class="text-xl font-semibold">{{ helpDesks.help_desk_name || 'Hot Desk' }}</h3>
                            <Link
                                :href="route('booking.offices')"
                                class="inline-block w-full px-4 py-1 text-sm font-medium text-center text-white bg-blue-600 rounded md:w-auto hover:bg-blue-700">
                                Back
                            </Link>
                        </div>

                        <!-- Details -->
                        <div class="grid grid-cols-1 gap-6 text-sm text-gray-700 md:grid-cols-2">
                            <div class="space-y-2">
                                <p><strong>Location:</strong> {{ helpDesks.location?.name || 'N/A' }}</p>
                                <!-- <p><strong>Discount:</strong> {{ helpDesks.discount || 'N/A' }}%</p> -->
                                <p>
                                    <strong>Duration:</strong>
                                    {{
                                        helpDesks.duration === '0.5'
                                            ? 'Half-Day'
                                            : helpDesks.duration
                                              ? helpDesks.duration + ' Days'
                                              : 'N/A'
                                    }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <h4 class="font-semibold text-gray-800">Pricing Option</h4>
                                <p>
                                    Price: <strong>R{{ helpDesks.price ?? '0.00' }}</strong>
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-x-2">
                            <button
                                @click="book"
                                class="px-4 py-1 text-sm text-white bg-pink-600 rounded hover:bg-pink-700">
                                Book {{ helpDesks.help_desk_name || 'Hot Desk' }}
                            </button>
                            <button
                                @click="checkAvailability"
                                class="px-4 py-1 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                                Check Availability
                            </button>
                        </div>

                        <!-- Booking Form -->
                        <div
                            v-if="viewMode === 'form'"
                            class="pt-6 mt-6 border-t border-gray-200">
                            <BookingHotDesk
                                :buttonName="helpDesks.help_desk_name"
                                :bookable-type="'App\\\\Models\\\\HelpDesk'"
                                :bookable-id="helpDesks.id"
                                :pricing-options="helpDesks.price"
                                :available-plans="helpDesks.help_desk_name"
                                :selected-duration="Number(helpDesks.duration)" />
                        </div>

                        <!-- Calendar Placeholder -->
                        <div
                            v-if="viewMode === 'calendar'"
                            class="pt-6 mt-6 border-t border-gray-200">
                            <div class="p-4 text-center text-gray-600 border rounded">
                                🗓️ Calendar Placeholder: Show availability for
                                <strong>{{ helpDesks.help_desk_name }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
