<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
// import FormattedDate from "@/Components/FormatDate.vue";

const props = defineProps({
    boardrooms: Object,
    filters: Object,
    amenities: Object,
    can: Object,
});

const page = usePage();
const search = ref(props.filters.search ?? '');
const successMessage = ref(null);
const flashMessage = computed(() => page.props?.flash?.success || null);
const showMessage = computed(() => {
    return !!(flashMessage.value?.trim?.() || successMessage.value?.trim?.());
});
const showModal = ref(false);
const boardroomToDelete = ref(null);

watch(showMessage, msg => {
    if (msg) {
        setTimeout(() => {
            successMessage.value = null;
        }, 3000);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});

watch(search, value => {
    router.get(
        route('admin.boardrooms'),
        { search: value },
        {
            preserveState: true,
            replace: true,
        }
    );
});

const confirmDelete = id => {
    showModal.value = true;
    boardroomToDelete.value = id;
};

const deleteboardroom = () => {
    if (boardroomToDelete.value) {
        router.delete(route('admin.boardrooms.destroy', boardroomToDelete.value), {
            preserveScroll: true,
            onSuccess: () => {
                successMessage.value = 'boardroom deleted successfully.';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            onFinish: () => {
                showModal.value = false;
                boardroomToDelete.value = null;
            },
        });
    }
};

const formatLabel = label => {
    if (label === '&laquo; Previous') return 'Prev';
    if (label === 'Next &raquo;') return 'Next';
    return label;
};

const pillColors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-teal-500', 'bg-orange-500', 'bg-yellow-500'];

const getPillColor = index => {
    return pillColors[index % pillColors.length];
};
</script>

<template>
    <Head title="Boardrooms" />

    <AuthenticatedLayout>
        <!-- Success Notification -->

        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Boardrooms</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <template v-if="showMessage">
                    <div class="p-3 mb-4 text-green-800 bg-green-100 rounded">
                        {{ successMessage || flashMessage || '✔️ Success' }}
                    </div>
                </template>

                <div class="p-2">
                    <!-- Search Filter -->
                    <div class="flex flex-col gap-3 mb-4 sm:flex-row sm:items-center sm:justify-between">
                        <Link
                            :href="route('admin.boardrooms.create')"
                            class="inline-block px-3 py-2 text-sm font-medium text-white rounded bg-primary hover:bg-bluemain/60">
                            + Add boardroom
                        </Link>

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search..."
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded-md shadow-sm sm:w-48 focus:outline-none focus:ring-2 focus:ring-bluemain" />
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-sm font-medium text-left text-gray-700">ID</th>
                                    <th class="px-6 py-3 text-sm font-medium text-left text-gray-700">
                                        Boardroom Name
                                    </th>
                                    <th class="px-6 py-3 text-sm font-medium text-left text-gray-700">Location</th>
                                    <th class="px-6 py-3 text-sm font-medium text-left text-gray-700">Seats</th>
                                    <th class="px-6 py-3 text-sm font-medium text-left text-gray-700">Hourly Price</th>
                                    <th class="px-6 py-3 text-sm font-medium text-left text-gray-700">Daily Price</th>
                                    <th class="px-6 py-3 text-sm font-medium text-left text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="boardroom in boardrooms.data"
                                    :key="boardroom.id">
                                    <td class="px-6 py-4 text-sm text-gray-800">{{ boardroom.id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-800">{{ boardroom.boardroom_name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-800">{{ boardroom.location.name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-800">{{ boardroom.seats }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-800">
                                        {{
                                            boardroom.hourly_price
                                                ? `R ${Number(boardroom.hourly_price).toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                                                : 'None'
                                        }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800">
                                        {{
                                            boardroom.daily_price
                                                ? `R ${Number(boardroom.daily_price).toLocaleString('en-ZA', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                                                : 'None'
                                        }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-800">
                                        <div class="flex space-x-1">
                                            <button
                                                @click="$inertia.visit(route('admin.boardrooms.edit', boardroom.id))"
                                                class="px-3 py-1 text-sm text-white rounded bg-bluemain hover:bg-bluemain/60">
                                                Edit
                                            </button>
                                            <button
                                                @click="confirmDelete(boardroom.id)"
                                                class="px-3 py-1 text-sm text-white bg-red-500 rounded hover:bg-red-600">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-between mt-4">
                        <div class="text-sm text-gray-600">
                            Showing
                            <span class="font-medium">{{ boardrooms.from }}</span>
                            to
                            <span class="font-medium">{{ boardrooms.to }}</span>
                            of
                            <span class="font-medium">{{ boardrooms.total }}</span> results
                        </div>

                        <div class="flex space-x-1">
                            <template
                                v-for="(link, index) in boardrooms.links"
                                :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-bluemain/60 hover:text-white"
                                    :class="link.active ? 'bg-bluemain text-white' : 'text-gray-700'"
                                    v-html="formatLabel(link.label)" />
                                <span
                                    v-else
                                    class="px-3 py-1 text-sm text-gray-400 border border-gray-300 rounded-md cursor-not-allowed"
                                    v-html="formatLabel(link.label)" />
                            </template>
                        </div>
                    </div>
                </div>

                <template v-if="showModal">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="w-full max-w-md p-6 bg-white rounded shadow">
                            <h2 class="mb-4 text-lg font-semibold">Confirm Delete</h2>
                            <p class="mb-6">
                                Are you sure you want to delete this boardroom? This action cannot be undone.
                            </p>
                            <div class="flex justify-end space-x-3">
                                <button
                                    v-if="can['edit boadrooms']"
                                    @click="showModal = false"
                                    class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded hover:bg-gray-200">
                                    Cancel
                                </button>
                                <button
                                    v-if="can['delete boardrooms']"
                                    @click="deleteboardroom"
                                    class="px-4 py-2 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Fixed width for larger screens */
.price-column {
    width: 150px;
}

.amenities-column {
    width: 250px;
}

/* Make the columns dynamic on smaller screens */
@media (max-width: 768px) {
    .price-column,
    .amenities-column {
        width: auto;
        min-width: 120px;
    }
}
</style>
