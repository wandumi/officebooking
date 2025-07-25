<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
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
    price: number;
    price_premium: number;
    price_standard: number;
}

interface BookedVirtual {
    plan: string;
    selected_dates: string[];
}

const props = defineProps<{
    virtual: Virtual;
    locations: Location;
    bookedRanges: BookedVirtual[];
}>();

const { props: pageProps } = usePage();
const flash = (pageProps.flash ?? {}) as { success?: string };
const flashMessage = flash.success ?? null;

// ✅ Create reactive error value
const bookingError = computed(() => pageProps.errors?.booking_conflict ?? null);

const viewMode = ref<'form' | 'calendar' | null>(null);

const pricingOptions = {
    premium: props.virtual.price_premium,
    standard: props.virtual.price_standard,
};

const availablePlans = Object.keys(pricingOptions).filter(key => pricingOptions[key] != null);

function book() {
    viewMode.value = 'form';
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
                            <h3 class="text-xl font-semibold">
                                {{ virtual.virtualoffice_name || 'Virtual Office' }}
                            </h3>
                            <Link
                                :href="route('virtual.home')"
                                class="inline-block w-full px-4 py-2 text-sm font-medium text-center text-white rounded md:w-auto bg-primary hover:bg-bluemain">
                                Back
                            </Link>
                        </div>

                        <!-- Details -->
                        <div class="grid grid-cols-1 gap-6 text-sm text-gray-700 md:grid-cols-2">
                            <div class="space-y-2">
                                <p><strong>Location:</strong> {{ virtual.location?.name || 'N/A' }}</p>
                                <p><strong>Address:</strong> {{ virtual.address || 'N/A' }}</p>
                                <p><strong>Base Price:</strong> R{{ virtual.price || '0.00' }}</p>
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

                        <!-- Booking Form -->
                        <div class="pt-6 mt-6 border-t border-gray-200">
                            <BookingVirtual
                                :virtual-Id="virtual.id"
                                :buttonName="virtual.virtualoffice_name"
                                :pricing-options="pricingOptions"
                                :available-plans="availablePlans"
                                :selected-plan="availablePlans[0]"
                                :booked-ranges="bookedRanges" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
