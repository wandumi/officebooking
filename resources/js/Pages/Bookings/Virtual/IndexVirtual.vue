<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Location {
    name: string;
    address: string;
    city: string;
}

interface Virtual {
    id?: number;
    location?: Location;
    virtualoffice_name?: string;
    address?: string;
    discount: number;
    price: number;
    duration: number;
    price_premium: number;
    price_standard: number;
}

const props = defineProps<{ virtualOffices: Virtual[] }>();
const selectedLocation = ref<string>('');

// Unique locations
const locations = computed(() => {
    const names: string[] = [];
    for (const vo of props.virtualOffices) {
        if (vo.location?.name && !names.includes(vo.location.name)) {
            names.push(vo.location.name);
        }
    }
    return names;
});

// Default location
if (!selectedLocation.value && locations.value.length > 0) {
    selectedLocation.value = locations.value[0];
}

// Filtered by location
const filtered = computed(() => props.virtualOffices.filter(vo => vo.location?.name === selectedLocation.value));

function goToVirtual(id?: number) {
    if (id) router.visit(`/virtual-booking/${id}`);
}
</script>

<template>
    <Head title="Virtual Office Plans" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Virtual Office Plans</h2>
        </template>

        <div class="flex flex-col gap-6 px-4 py-6 mx-auto max-w-7xl lg:flex-row lg:gap-8 lg:px-8">
            <!-- Sidebar -->
            <div class="w-full space-y-2 lg:w-1/4 sm:px-2">
                <h3 class="text-sm font-medium text-gray-600 uppercase">Locations</h3>
                <ul class="space-y-1">
                    <li
                        v-for="loc in locations"
                        :key="loc"
                        @click="selectedLocation = loc"
                        :class="[
                            'cursor-pointer px-2 py-1 text-sm rounded',
                            selectedLocation === loc
                                ? 'bg-primary text-white font-semibold'
                                : 'hover:bg-gray-100 text-gray-800',
                        ]">
                        {{ loc }}
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="flex-1 space-y-6">
                <div
                    v-if="filtered.length > 0"
                    class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ selectedLocation }} – Available Plans</h3>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div
                            v-for="vo in filtered"
                            :key="vo.id"
                            class="bg-white border border-gray-300 divide-y divide-gray-200 rounded-lg shadow-sm">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold text-gray-900">{{ vo.virtualoffice_name }}</h2>
                                <p class="mt-2 text-sm text-gray-600">{{ vo.location?.address }}</p>

                                <!-- Pricing -->
                                <p
                                    v-if="vo.virtualoffice_name?.toLowerCase().includes('standard')"
                                    class="mt-6">
                                    <span class="text-3xl font-bold text-gray-800">R {{ vo.price_standard }}</span>
                                    <span class="text-sm text-gray-500"> (Standard)</span>
                                </p>
                                <p
                                    v-if="vo.virtualoffice_name?.toLowerCase().includes('premium')"
                                    class="mt-6">
                                    <span class="text-3xl font-bold text-yellow-700">R {{ vo.price_premium }}</span>
                                    <span class="text-sm text-gray-500"> (Premium)</span>
                                </p>

                                <div class="mt-4 space-y-1 text-xs text-gray-600">
                                    <p>Discount: {{ vo.discount }}%</p>
                                </div>

                                <button
                                    @click="goToVirtual(vo.id)"
                                    class="w-full py-2 mt-6 text-sm font-semibold text-white rounded-md bg-bluemain hover:bg-bluemain/90 sm:text-base">
                                    Enquire Now
                                </button>
                            </div>

                            <div class="p-4">
                                <h4 class="text-xs font-semibold text-gray-600 uppercase">What’s Included</h4>
                                <ul class="mt-4 space-y-2 text-xs text-gray-700">
                                    <li class="flex items-start space-x-2">
                                        <svg
                                            class="w-5 h-5 text-green-500"
                                            fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 01 0 1.414L8.414 15 4 10.586a1 1 0 011.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Mail handling</span>
                                    </li>
                                    <li class="flex items-start space-x-2">
                                        <svg
                                            class="w-5 h-5 text-green-500"
                                            fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 01 0 1.414L8.414 15 4 10.586a1 1 0 011.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Receptionist support</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="italic text-gray-500">
                    No plans available for this location.
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
