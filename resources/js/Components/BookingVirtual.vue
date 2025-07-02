<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import Calendar from 'primevue/calendar';
import { format } from 'date-fns';

const props = defineProps({
    virtualId: Number,
    pricingOptions: Object,
    availablePlans: Array,
    buttonName: String,
    selectedPlan: String,
    bookedRanges: Array,
});

const today = new Date();
const successMessage = ref(null);
const bookingConflict = ref(null);

const form = useForm({
    virtual_office_id: props.virtualId,
    plan: props.selectedPlan || props.availablePlans[0] || '',
    selected_dates: [],
    start_date: '',
    end_date: '',
    months: 3,
    selected_price: 0,
});

const currencyFormatter = new Intl.NumberFormat('en-ZA', {
    style: 'currency',
    currency: 'ZAR',
});

const unitPrice = computed(() => props.pricingOptions[form.plan] || 0);

watch(
    [() => form.start_date, () => form.months, () => form.plan],
    () => {
        if (form.start_date) {
            form.months = Math.min(Math.max(form.months, 3), 12);

            const start = new Date(form.start_date);
            const end = new Date(start);
            end.setMonth(start.getMonth() + form.months);
            const formattedEnd = format(end, 'yyyy-MM-dd');
            form.end_date = formattedEnd;

            form.selected_price = unitPrice.value * form.months;

            const current = new Date(start);
            form.selected_dates = [];

            while (current <= end) {
                form.selected_dates.push(format(new Date(current), 'yyyy-MM-dd'));
                current.setDate(current.getDate() + 1);
            }
        }
    },
    { immediate: true }
);

const disabledDates = computed(() => {
    if (!props.bookedRanges || !Array.isArray(props.bookedRanges)) {
        return [];
    }

    return props.bookedRanges
        .filter(booking => booking.plan === form.plan)
        .flatMap(booking => booking.selected_dates)
        .map(dateStr => new Date(dateStr));
});

// Clear conflict error if user changes input
watch([() => form.start_date, () => form.end_date, () => form.selected_dates, () => form.plan], () => {
    if (bookingConflict.value) {
        bookingConflict.value = null;
    }
});

const submit = () => {
    form.post(route('bookingvirtual.store'), {
        preserveScroll: true,
        onError: errors => {
            bookingConflict.value = errors.booking_conflict ?? null;
        },
        onSuccess: () => {
            successMessage.value = 'Booking created successfully!';
            bookingConflict.value = null;

            setTimeout(() => {
                successMessage.value = null;
            }, 4000);
        },
    });
};
</script>

<template>
    <!-- Flash Success Message -->
    <div
        v-if="successMessage"
        class="px-4 py-3 mb-4 text-sm text-green-800 bg-green-100 border border-green-300 rounded">
        {{ successMessage }}
    </div>

    <!-- Conflict Error -->
    <div
        v-if="bookingConflict"
        class="px-4 py-3 mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded">
        {{ bookingConflict }}
    </div>

    <form
        @submit.prevent="submit"
        class="space-y-4">
        <!-- Plan Selection -->
        <div>
            <label class="block font-semibold">Plan</label>
            <select
                v-model="form.plan"
                class="w-full px-3 py-2 border rounded"
                required>
                <option
                    v-for="plan in availablePlans"
                    :key="plan"
                    :value="plan">
                    {{ plan.charAt(0).toUpperCase() + plan.slice(1) }}
                </option>
            </select>
            <div
                v-if="form.errors.plan"
                class="mt-1 text-sm text-red-600">
                {{ form.errors.plan }}
            </div>
        </div>

        <!-- Booking Inputs -->
        <div class="space-y-4">
            <!-- Start Date -->
            <div>
                <label class="block font-semibold">Start Date</label>
                <Calendar
                    v-model="form.start_date"
                    :disabledDates="disabledDates"
                    :minDate="today"
                    :manualInput="false"
                    :disabledDays="[0, 6]"
                    dateFormat="yy-mm-dd"
                    showIcon
                    class="w-full" />
                <div
                    v-if="form.errors.start_date"
                    class="mt-1 text-sm text-red-600">
                    {{ form.errors.start_date }}
                </div>
            </div>

            <!-- Duration Selection -->
            <div>
                <label class="block font-semibold">Duration (Months)</label>
                <select
                    v-model="form.months"
                    class="w-full px-3 py-2 border rounded"
                    required>
                    <option
                        v-for="month in 10"
                        :key="month"
                        :value="month + 2">
                        {{ month + 2 }} Months
                    </option>
                </select>
                <div
                    v-if="form.errors.months"
                    class="mt-1 text-sm text-red-600">
                    {{ form.errors.months }}
                </div>
            </div>

            <!-- End Date -->
            <div>
                <label class="block font-semibold">End Date</label>
                <div class="w-full px-3 py-2 text-gray-700 rounded bg-green-50">
                    {{ form.end_date || 'End date will appear here' }}
                </div>
            </div>
        </div>

        <!-- Total Price -->
        <div>
            <label class="block font-semibold">Total Price</label>
            <input
                type="text"
                :value="currencyFormatter.format(form.selected_price)"
                readonly
                tabindex="-1"
                @focus="e => e.target.blur()"
                class="w-full px-3 py-2 border-0 rounded cursor-default select-none bg-green-50" />
            <input
                type="hidden"
                v-model="form.selected_price" />
            <div
                v-if="form.errors.selected_price"
                class="mt-1 text-sm text-red-600">
                {{ form.errors.selected_price }}
            </div>
        </div>

        <!-- Submit Button -->
        <div class="space-x-2">
            <button
                type="submit"
                class="px-4 py-1 text-sm text-white rounded bg-primary hover:bg-bluemain">
                Book {{ buttonName }}
            </button>
        </div>
    </form>
</template>
