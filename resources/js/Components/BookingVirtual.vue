<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, computed } from 'vue';
import Calendar from 'primevue/calendar';

const props = defineProps({
    bookableType: String,
    bookableId: Number,
    pricingOptions: Object,
    availablePlans: Array,
    buttonName: String,
    selectedPlan: String,
});

const today = new Date();

const form = useForm({
    bookable_type: props.bookableType,
    bookable_id: props.bookableId,
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

const endDateObject = computed(() => {
    return form.end_date ? new Date(form.end_date) : null;
});

const calculateEndDate = (startDate, monthsDuration) => {
    if (!startDate || monthsDuration < 3 || monthsDuration > 12) return null;

    const start = new Date(startDate);
    start.setMonth(start.getMonth() + monthsDuration);

    // Adjust if resulting day overflows to next month
    if (start.getDate() !== new Date(start.getFullYear(), start.getMonth() + 1, 0).getDate()) {
        start.setDate(0);
    }

    return start.toISOString().split('T')[0];
};

watch(
    [() => form.start_date, () => form.months, () => form.plan],
    () => {
        if (form.start_date) {
            if (form.months < 3) form.months = 3;
            if (form.months > 12) form.months = 12;

            const calculatedEndDate = calculateEndDate(form.start_date, form.months);
            form.end_date = calculatedEndDate;

            const monthCount = form.months;
            form.selected_price = unitPrice.value * monthCount;
        }
    },
    { immediate: true }
);

const submit = () => {
    form.post(route('bookings.store'));
};
</script>

<template>
    <form
        @submit.prevent="submit"
        class="space-y-4">
        <!-- Plan -->
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
        </div>

        <!-- Booking Inputs -->
        <div class="space-y-4">
            <div>
                <label class="block font-semibold">Start Date</label>
                <Calendar
                    v-model="form.start_date"
                    :minDate="today"
                    :manualInput="false"
                    :disabledDays="[0, 6]"
                    dateFormat="yy-mm-dd"
                    showIcon
                    class="w-full" />
            </div>

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
            </div>

            <div>
                <label class="block font-semibold">End Date</label>
                <div class="w-full px-3 py-2 text-gray-700 rounded bg-green-50">
                    {{ form.end_date ? form.end_date : 'End date will appear here' }}
                </div>
            </div>
        </div>

        <!-- Price -->
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
        </div>

        <!-- Submit -->
        <div>
            <button
                type="submit"
                class="px-4 py-1 text-sm text-white bg-pink-600 rounded hover:bg-pink-700">
                Book {{ buttonName }}
            </button>
        </div>
    </form>
</template>
