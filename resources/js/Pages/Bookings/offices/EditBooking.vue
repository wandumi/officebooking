<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import BookingForm from '@/Components/BookingForm.vue';

interface Location {
    name: string;
}

interface Amenity {
    id: number;
    amenity_name: string;
}

interface Category {
    id: number;
    name: string;
}

interface Office {
    id?: number;
    office_name?: string;
    location?: Location;
    category_id?: number;
    seats?: number;
    monthly_rate?: number;
    daily_rate?: number;
    price_premium?: number;
    price_standard?: number;
    amenities?: Amenity[];
}

const props = defineProps<{
    office: Office;
    categories: Category[];
    bookedDates: string[];
}>();

const { props: pageProps } = usePage();
const category = props.categories.find(cat => cat.id === props.office.category_id);
const categoryName = category?.name ?? '';

const viewMode = ref<'form' | 'calendar' | null>(null);

const pricingOptions = {
    ...(categoryName === 'Closed Office' && {
        monthly: props.office.monthly_rate,
        daily: props.office.daily_rate,
    }),
    ...(categoryName === 'Dedicated Desk' && {
        premium: props.office.price_premium,
        standard: props.office.price_standard,
    }),
};

const availablePlans = Object.keys(pricingOptions).filter(k => pricingOptions[k] != null);

// Flash success notification
const flash = (pageProps.flash ?? {}) as { success?: string };
const flashMessage = flash.success ?? null;

const showSuccess = ref(!!flashMessage);

if (showSuccess.value) {
    setTimeout(() => {
        showSuccess.value = false;
    }, 4000);
}

function book() {
    viewMode.value = 'form';
}

function checkAvailability() {
    viewMode.value = 'calendar';
}

if (flashMessage) {
    viewMode.value = 'form';
}

const goToBookings = () => {
    router.visit(route('bookingoffices.show'));
};
</script>

<template>
    <Head title="Edit Booking" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">View Booking</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-4 lg:px-8">
                <div class="flex justify-center">
                    <div class="w-full max-w-2xl p-6 space-y-6 bg-white border rounded-lg shadow-md">
                        <!-- Header -->
                        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                            <h3 class="text-xl font-semibold">{{ office.office_name || 'Office' }}</h3>
                            <Link
                                :href="route('booking.offices')"
                                class="inline-block w-full px-4 py-1 text-sm font-medium text-center text-white rounded md:w-auto bg-primary hover:bg-bluemain">
                                Back
                            </Link>
                        </div>

                        <!-- Info -->
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <p><strong>Location:</strong> {{ office.location?.name || 'N/A' }}</p>
                                    <p><strong>Seats:</strong> {{ office.seats || 'N/A' }}</p>
                                    <p><strong>Category:</strong> {{ categoryName || 'N/A' }}</p>
                                </div>

                                <div v-if="office.amenities?.length">
                                    <p><strong>Amenities:</strong></p>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        <span
                                            v-for="(a, index) in office.amenities"
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

                            <!-- Pricing Summary -->
                            <div class="pt-4 border-t border-gray-200">
                                <h4 class="font-semibold text-gray-800">Pricing Options</h4>
                                <ul class="mt-2 space-y-1 text-sm text-gray-700">
                                    <li v-if="office.monthly_rate">
                                        Monthly: <strong>R{{ office.monthly_rate }}</strong>
                                    </li>
                                    <li v-if="office.daily_rate">
                                        Daily: <strong>R{{ office.daily_rate }}</strong>
                                    </li>
                                    <li v-if="office.price_premium">
                                        Premium: <strong>R{{ office.price_premium }}</strong>
                                    </li>
                                    <li v-if="office.price_standard">
                                        Standard: <strong>R{{ office.price_standard }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-x-2">
                            <button
                                @click="book"
                                class="px-4 py-1 text-sm font-semibold text-white rounded bg-primary hover:bg-bluemain">
                                Enquire {{ office.office_name }}
                            </button>
                        </div>

                        <!-- Booking Form -->
                        <div
                            v-if="viewMode === 'form'"
                            class="pt-6 mt-6 border-t border-gray-200">
                            <!-- Success Flash Message -->
                            <div
                                v-if="showSuccess && flashMessage"
                                class="px-4 py-3 mb-4 text-sm text-green-800 bg-green-100 border border-green-300 rounded">
                                {{ flashMessage }}
                            </div>

                            <!-- Booking Conflict Error (Optional fallback if still present in this page) -->
                            <div
                                v-if="pageProps.errors?.booking_conflict"
                                class="px-4 py-3 mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded">
                                {{ pageProps.errors.booking_conflict }}
                            </div>

                            <BookingForm
                                :buttonName="office.office_name"
                                :office-id="office.id"
                                :pricing-options="pricingOptions"
                                :available-plans="availablePlans"
                                :category-id="category.id"
                                :category-name="category.name"
                                :booked-dates="props.bookedDates"
                                :selected-plan="availablePlans[0]" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
