<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import BookingBoardroom from '@/Components/BookingBoardroom.vue';

interface Location {
    id: number;
    name: string;
    address?: string;
    city?: string;
}

interface Amenity {
    id: number;
    amenity_name: string;
}

interface Boardroom {
    id: number;
    boardroom_name: string;
    location?: Location;
    seats?: number;
    hourly_price?: number;
    daily_price?: number;
    amenities?: Amenity[];
}

const props = defineProps<{
    boardroom: Boardroom;
    locations: Location[];
    amenities: Amenity[];
}>();

const pricingOptions = {
    hourly: props.boardroom.hourly_price,
    daily: props.boardroom.daily_price,
};

const availablePlans = Object.keys(pricingOptions).filter(key => pricingOptions[key] != null);

const viewMode = ref<'form' | 'calendar' | null>(null);
const selectedPlan = ref<string | null>(null);
</script>

<template>
    <Head title="View Boardroom" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">View Boardroom</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-4 lg:px-8">
                <div class="flex justify-center">
                    <div class="w-full max-w-2xl p-6 space-y-6 bg-white border rounded-lg shadow-md">
                        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                            <h3 class="text-xl font-semibold">{{ boardroom.boardroom_name || 'Boardroom' }}</h3>
                            <Link
                                :href="route('booking.boardrooms')"
                                class="inline-block w-full px-4 py-1 text-sm font-medium text-center text-white rounded bg-primary md:w-auto hover:bg-bluemain">
                                Back
                            </Link>
                        </div>

                        <div class="grid grid-cols-1 gap-6 text-sm text-gray-700 md:grid-cols-2">
                            <div class="space-y-2">
                                <p><strong>Location:</strong> {{ boardroom.location?.name || 'N/A' }}</p>
                                <p><strong>Seats:</strong> {{ boardroom.seats ?? 'N/A' }}</p>
                            </div>

                            <div
                                v-if="boardroom.amenities?.length"
                                class="space-y-2">
                                <p><strong>Amenities:</strong></p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span
                                        v-for="(a, index) in boardroom.amenities"
                                        :key="a.id"
                                        :class="[
                                            'px-2 py-1 text-xs text-white rounded',
                                            [
                                                'bg-blue-500',
                                                'bg-green-500',
                                                'bg-purple-500',
                                                'bg-pink-500',
                                                'bg-yellow-500',
                                                'bg-indigo-500',
                                            ][index % 6],
                                        ]">
                                        {{ a.amenity_name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Price Selection -->
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-800">Pricing Options</h4>
                            <ul class="mt-2 space-y-1 text-sm text-gray-700">
                                <li>
                                    Hourly Rate: <strong>R{{ boardroom.hourly_price ?? '0.00' }}</strong>
                                </li>
                                <li>
                                    Full Day Rate: <strong>R{{ boardroom.daily_price ?? '0.00' }}</strong>
                                </li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-x-2">
                            <button
                                @click="viewMode = 'form'"
                                class="px-4 py-1 text-sm text-white rounded bg-primary hover:bg-bluemain">
                                Enquire {{ boardroom.boardroom_name || 'Boardroom' }}
                            </button>
                        </div>

                        <!-- Booking Form -->
                        <div
                            v-if="viewMode === 'form'"
                            class="pt-6 mt-6 border-t border-gray-200">
                            <BookingBoardroom
                                :buttonName="boardroom.boardroom_name"
                                :boardroom-id="boardroom.id"
                                :pricing-options="pricingOptions"
                                :available-plans="availablePlans"
                                :selected-plan="selectedPlan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
