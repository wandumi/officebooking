<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface Office {
    id: number;
    office_name: string;
    category?: { name: string };
    location?: { name: string };
    pricing?: { premium_price?: number; standard_price?: number };
    seats?: number;
    monthly_rate?: number;
    daily_rate?: number;
}

interface HotDesk {
    id: number;
    help_desk_name: string;
    location?: { name: string };
    price?: number;
    duration?: string;
    desks?: number;
    discount?: number;
}

interface Location {
    name: string;
    address?: string;
    city?: string;
}

const props = defineProps<{
    offices: Office[];
    hotDesks: HotDesk[];
    locations: Location[];
}>();

const tabs = ['All', 'Closed Offices', 'Dedicated Desks', 'Hot Desks'];
const activeTab = ref('All');
const selectedLocation = ref('All');

// Filters
const closedOffices = computed(() =>
    props.offices.filter(
        o =>
            o.category?.name === 'Closed Office' &&
            (selectedLocation.value === 'All' || o.location?.name === selectedLocation.value)
    )
);

const dedicatedDesks = computed(() =>
    props.offices.filter(
        o =>
            o.category?.name === 'Dedicated Desk' &&
            (selectedLocation.value === 'All' || o.location?.name === selectedLocation.value)
    )
);

const filteredHotDesks = computed(() =>
    props.hotDesks.filter(help => selectedLocation.value === 'All' || help.location?.name === selectedLocation.value)
);

function goToOffice(officeId: number) {
    router.visit(`/booking-offices/${officeId}`);
}

function goToVirtual(Id: number) {
    router.visit(`/virtual-booking/${Id}`);
}

function goToHotDesk(Id: number) {
    router.visit(`/booking-hotdesk/${Id}`);
}
</script>

<template>
    <Head title="Bookings Admin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Bookings</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="px-6 py-10 mx-auto max-w-7xl">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                        <!-- LEFT TABS -->
                        <div class="col-span-1">
                            <!-- Mobile Dropdown -->
                            <div class="mb-4 md:hidden">
                                <select
                                    v-model="activeTab"
                                    class="w-full px-3 py-1 text-sm border border-pink-300 rounded focus:outline-none focus:ring">
                                    <option
                                        v-for="tab in tabs"
                                        :key="tab"
                                        :value="tab">
                                        {{ tab }}
                                    </option>
                                </select>
                            </div>

                            <!-- Desktop Vertical Tab Buttons -->
                            <div class="hidden space-y-2 md:block">
                                <button
                                    v-for="tab in tabs"
                                    :key="tab"
                                    @click="activeTab = tab"
                                    :class="[
                                        'w-full text-left px-2 py-1 text-sm rounded-md transition',
                                        activeTab === tab
                                            ? 'bg-primary text-white'
                                            : 'bg-gray-100 text-gray-800 hover:bg-gray-200',
                                    ]">
                                    {{ tab }}
                                </button>
                            </div>

                            <!-- LOCATION FILTER -->
                            <div class="mt-6">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Filter by Location</label>
                                <select
                                    v-model="selectedLocation"
                                    class="w-full px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring">
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

                        <!-- RIGHT VIEW -->
                        <div class="col-span-1 space-y-12 md:col-span-3">
                            <!-- ALL -->
                            <div
                                v-if="activeTab === 'All'"
                                class="space-y-12">
                                <div v-if="closedOffices.length">
                                    <h3 class="text-xl font-semibold">🏢 Closed Offices</h3>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        <div
                                            v-for="office in closedOffices"
                                            :key="office.id"
                                            class="p-4 bg-white rounded shadow">
                                            <h4 class="font-semibold">{{ office.office_name }}</h4>
                                            <p class="text-sm text-gray-500">Location: {{ office.location?.name }}</p>
                                            <p class="text-sm text-gray-500">Seats: {{ office.seats }}</p>
                                            <button
                                                @click="goToOffice(office.id)"
                                                class="px-4 py-1 mt-2 text-sm text-white rounded bg-bluemain hover:bg-bluemain/90">
                                                Enquire {{ office.office_name }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="dedicatedDesks.length">
                                    <h3 class="text-xl font-semibold">🪑 Dedicated Desks</h3>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        <div
                                            v-for="desk in dedicatedDesks"
                                            :key="desk.id"
                                            class="p-4 bg-white rounded shadow">
                                            <h4 class="font-semibold">{{ desk.office_name }}</h4>
                                            <p class="text-sm text-gray-500">Location: {{ desk.location?.name }}</p>
                                            <p class="text-sm text-gray-500">Seats: {{ desk.seats }}</p>

                                            <button
                                                @click="goToOffice(desk.id)"
                                                class="px-4 py-1 mt-2 text-sm text-white rounded bg-bluemain hover:bg-bluemain/90">
                                                Enquire {{ desk.office_name }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="filteredHotDesks.length">
                                    <h3 class="text-xl font-semibold">🧾 Hot Desks</h3>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        <div
                                            v-for="desk in filteredHotDesks"
                                            :key="desk.help_desk_name"
                                            class="p-4 bg-white rounded shadow">
                                            <h4 class="font-semibold">{{ desk.help_desk_name }}</h4>
                                            <p class="text-sm text-gray-500">Location: {{ desk.location?.name }}</p>
                                            <p class="text-sm">
                                                Duration:
                                                {{
                                                    desk.duration === '0.5'
                                                        ? 'Half-Day'
                                                        : desk.duration
                                                          ? desk.duration + ' Days'
                                                          : 'N/A'
                                                }}
                                            </p>
                                            <button
                                                @click="goToHotDesk(desk.id)"
                                                class="px-4 py-1 mt-2 text-sm text-white rounded bg-bluemain hover:bg-bluemain/90">
                                                Enquire {{ desk.help_desk_name }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Closed Offices -->
                            <div v-else-if="activeTab === 'Closed Offices'">
                                <h3 class="mb-4 text-xl font-semibold">🏢 Closed Offices</h3>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div
                                        v-for="office in closedOffices"
                                        :key="office.id"
                                        class="p-4 bg-white rounded shadow">
                                        <h4 class="font-semibold">{{ office.office_name }}</h4>
                                        <p class="text-sm text-gray-500">Location: {{ office.location?.name }}</p>
                                        <p class="text-sm text-gray-500">Seats: {{ office.seats }}</p>
                                        <button
                                            @click="goToOffice(office.id)"
                                            class="px-4 py-1 mt-2 text-sm text-white rounded bg-bluemain hover:bg-bluemain/90">
                                            Enquire {{ office.office_name }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Dedicated Desks -->
                            <div v-else-if="activeTab === 'Dedicated Desks'">
                                <h3 class="mb-4 text-xl font-semibold">🪑 Dedicated Desks</h3>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div
                                        v-for="desk in dedicatedDesks"
                                        :key="desk.id"
                                        class="p-4 bg-white rounded shadow">
                                        <h4 class="font-semibold">{{ desk.office_name }}</h4>
                                        <p class="text-sm text-gray-500">Location: {{ desk.location?.name }}</p>
                                        <p class="text-sm text-gray-500">Seats: {{ desk.seats }}</p>
                                        <button
                                            @click="goToOffice(desk.id)"
                                            class="px-4 py-1 mt-2 text-sm text-white rounded bg-bluemain hover:bg-bluemain/90">
                                            Enquire {{ desk.office_name }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Hot Desks -->
                            <div v-else-if="activeTab === 'Hot Desks'">
                                <h3 class="mb-4 text-xl font-semibold">🧾 Hot Desks</h3>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    <div
                                        v-for="desk in filteredHotDesks"
                                        :key="desk.help_desk_name"
                                        class="p-4 bg-white rounded shadow">
                                        <h4 class="font-semibold">{{ desk.help_desk_name }}</h4>
                                        <p class="text-sm">Location: {{ desk.location?.name }}</p>
                                        <p class="text-sm">
                                            Duration:
                                            {{
                                                desk.duration === '0.5'
                                                    ? 'Half-Day'
                                                    : desk.duration
                                                      ? desk.duration + ' Days'
                                                      : 'N/A'
                                            }}
                                        </p>
                                        <button
                                            @click="goToHotDesk(desk.id)"
                                            class="px-4 py-1 mt-2 text-sm text-white rounded bg-bluemain hover:bg-bluemain/90">
                                            Enquire {{ desk.help_desk_name }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
