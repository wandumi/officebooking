// In Boardrooms/Index.vue
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Boardroom {
    id: number;
    boardroom_name: string;
    location?: { name: string };
    seats?: number;
    hourly_price?: number;
    daily_price?: number;
}

interface Location {
    name: string;
    address?: string;
    city?: string;
}

const props = defineProps<{
    boardrooms: Boardroom[];
    locations: Location[];
}>();

const tabs = ['All', 'Boardrooms'];
const activeTab = ref('All');
const selectedLocation = ref('All');

const filteredBoardrooms = computed(() =>
    props.boardrooms.filter(br => selectedLocation.value === 'All' || br.location?.name === selectedLocation.value)
);

function goToBoardroom(boardroomId: number) {
    router.visit(`/booking-boardrooms/${boardroomId}`);
}
</script>

<template>
    <Head title="Bookings Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Boardroom</h2>
        </template>

        <div class="px-5 py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                    <div class="col-span-1">
                        <select
                            v-model="activeTab"
                            class="w-full px-3 py-1 mb-4 text-sm border border-gray-300 rounded md:hidden">
                            <option
                                v-for="tab in tabs"
                                :key="tab"
                                :value="tab">
                                {{ tab }}
                            </option>
                        </select>

                        <div class="hidden space-y-2 md:block">
                            <button
                                v-for="tab in tabs"
                                :key="tab"
                                @click="activeTab = tab"
                                :class="[
                                    'w-full text-left px-4 py-1 rounded-md transition',
                                    activeTab === tab
                                        ? 'bg-pink-600 text-white'
                                        : 'bg-gray-100 text-gray-800 hover:bg-gray-200',
                                ]">
                                {{ tab }}
                            </button>
                        </div>

                        <div class="mt-6">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Filter by Location</label>
                            <select
                                v-model="selectedLocation"
                                class="w-full px-3 py-1 text-sm border border-gray-300 rounded">
                                <option value="All">All Locations</option>
                                <option
                                    v-for="loc in props.locations"
                                    :key="loc.name"
                                    :value="loc.name">
                                    {{ loc.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-span-1 space-y-12 md:col-span-3">
                        <div
                            v-if="activeTab === 'All'"
                            class="space-y-12">
                            <div v-if="filteredBoardrooms.length">
                                <h3 class="mb-4 text-xl font-semibold">🏛️ Boardrooms</h3>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div
                                        v-for="room in filteredBoardrooms"
                                        :key="room.id"
                                        class="p-4 bg-white rounded shadow">
                                        <h4 class="font-semibold">{{ room.boardroom_name }}</h4>
                                        <p class="text-sm text-gray-500">Location: {{ room.location?.name }}</p>
                                        <p class="text-sm text-gray-500">Seats: {{ room.seats }}</p>
                                        <p class="text-sm text-gray-400">ID: {{ room.id }}</p>
                                        <button
                                            @click="goToBoardroom(room.id)"
                                            class="px-4 py-1 mt-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                                            Enquire
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="activeTab === 'Boardrooms'">
                            <h3 class="mb-4 text-xl font-semibold">🏛️ Boardrooms</h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <div
                                    v-for="room in filteredBoardrooms"
                                    :key="room.id"
                                    class="p-4 bg-white rounded shadow">
                                    <h4 class="font-semibold">{{ room.boardroom_name }}</h4>
                                    <p class="text-sm text-gray-500">Location: {{ room.location?.name }}</p>
                                    <p class="text-sm text-gray-500">Seats: {{ room.seats }}</p>
                                    <button
                                        @click="goToBoardroom(room.id)"
                                        class="px-4 py-1 mt-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                                        Enquire
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
