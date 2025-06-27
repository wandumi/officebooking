<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch, computed, ref } from 'vue';
import Calendar from 'primevue/calendar';
import { differenceInCalendarMonths, isBefore } from 'date-fns';

const props = defineProps({
    bookableType: String,
    bookableId: Number,
    pricingOptions: Object,
    availablePlans: Array,
    buttonName: String,
    selectedPlan: String,
});

const dailyPlans = ['daily'];
const hourlyPlans = ['hourly'];
const today = new Date();

const form = useForm({
    bookable_type: props.bookableType,
    bookable_id: props.bookableId,
    plan: props.selectedPlan || props.availablePlans[0] || '',
    selected_dates: [],
    start_date: '',
    end_date: '',
    months: 1,
    selected_price: 0,
});

const unitPrice = computed(() => props.pricingOptions[form.plan] || 0);

const selectedDateTimes = ref({}); // { '2025-06-25': ['06:00', '07:00'] }

const weekdaysCount = computed(() => {
    return form.selected_dates.filter(date => {
        const day = new Date(date).getDay();
        return day !== 0 && day !== 6;
    }).length;
});

const calculateMonthCount = (start, end) => {
    if (!start || !end || isBefore(end, start)) return 0;

    const sameDay = start.getDate() === end.getDate();
    const fullMonths = differenceInCalendarMonths(end, start);
    return sameDay ? fullMonths || 1 : fullMonths + 1;
};

// Reset when plan changes
watch(
    () => form.plan,
    () => {
        form.selected_dates = [];
        form.start_date = '';
        form.end_date = '';
        form.months = 1;
        form.selected_price = 0;
        selectedDateTimes.value = {};
    }
);

// Pricing logic (deep watch)
watch(
    () => ({
        plan: form.plan,
        selected_dates: form.selected_dates,
        start_date: form.start_date,
        end_date: form.end_date,
        months: form.months,
        selected_times: selectedDateTimes.value,
    }),
    () => {
        if (!form.plan) return;

        if (hourlyPlans.includes(form.plan)) {
            let totalHours = Object.values(selectedDateTimes.value).reduce(
                (sum, slots) => sum + (Array.isArray(slots) ? slots.length : 0),
                0
            );
            form.selected_price = unitPrice.value * totalHours;
        } else if (dailyPlans.includes(form.plan)) {
            form.selected_price = unitPrice.value * weekdaysCount.value;
        } else {
            if (!form.start_date || !form.end_date) return;
            const start = new Date(form.start_date);
            const end = new Date(form.end_date);
            const monthCount = calculateMonthCount(start, end);
            form.months = monthCount;
            form.selected_price = unitPrice.value * monthCount;
        }
    },
    { immediate: true, deep: true }
);

const submit = () => {
    form.post(route('bookings.store'));
};

// Time slots: 6AM to 9PM
const timeSlots = Array.from({ length: 16 }, (_, i) => {
    const hour = i + 6;
    return `${hour.toString().padStart(2, '0')}:00`;
});

const toggleTimeSlot = (dateKey, time) => {
    if (!selectedDateTimes.value[dateKey]) {
        selectedDateTimes.value[dateKey] = [];
    }

    const index = selectedDateTimes.value[dateKey].indexOf(time);
    if (index > -1) {
        selectedDateTimes.value[dateKey].splice(index, 1);
    } else {
        selectedDateTimes.value[dateKey].push(time);
    }
};

const currencyFormatter = new Intl.NumberFormat('en-ZA', {
    style: 'currency',
    currency: 'ZAR',
});
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

        <!-- HOURLY PLAN -->
        <div
            v-if="hourlyPlans.includes(form.plan)"
            class="space-y-6">
            <div>
                <label class="block font-semibold">Select Dates</label>
                <Calendar
                    v-model="form.selected_dates"
                    selectionMode="multiple"
                    :minDate="today"
                    :disabledDays="[0, 6]"
                    :manualInput="false"
                    dateFormat="dd-mm-yy"
                    showIcon
                    class="w-full" />
            </div>

            <div
                v-for="date in form.selected_dates"
                :key="date.toISOString()"
                class="space-y-2">
                <div class="font-semibold">
                    {{ new Date(date).toDateString() }}
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="slot in timeSlots"
                        :key="slot"
                        type="button"
                        class="px-2 py-1 text-sm border rounded"
                        :class="{
                            'bg-blue-600 text-white':
                                selectedDateTimes[new Date(date).toISOString().split('T')[0]]?.includes(slot),
                            'bg-gray-100':
                                !selectedDateTimes[new Date(date).toISOString().split('T')[0]]?.includes(slot),
                        }"
                        @click="toggleTimeSlot(new Date(date).toISOString().split('T')[0], slot)">
                        {{ slot }}
                    </button>
                </div>
            </div>
        </div>

        <!-- DAILY PLAN -->
        <div
            v-else-if="dailyPlans.includes(form.plan)"
            class="space-y-4">
            <div>
                <label class="block font-semibold">Select Dates</label>
                <Calendar
                    v-model="form.selected_dates"
                    selectionMode="multiple"
                    :minDate="today"
                    :disabledDays="[0, 6]"
                    dateFormat="dd-mm-yy"
                    showIcon
                    class="w-full" />
            </div>
            <div>
                <label class="block font-semibold">Weekdays Selected</label>
                <div class="px-3 py-2 border rounded bg-gray-50">
                    {{ weekdaysCount }} {{ weekdaysCount === 1 ? 'day' : 'days' }}
                </div>
            </div>
        </div>

        <!-- MONTHLY PLAN -->
        <div
            v-else
            class="space-y-4">
            <div>
                <label class="block font-semibold">Start Date</label>
                <Calendar
                    v-model="form.start_date"
                    :minDate="today"
                    :disabledDays="[0, 6]"
                    dateFormat="yy-mm-dd"
                    showIcon
                    class="w-full" />
            </div>
            <div>
                <label class="block font-semibold">End Date</label>
                <Calendar
                    v-model="form.end_date"
                    :minDate="form.start_date || today"
                    dateFormat="yy-mm-dd"
                    showIcon
                    class="w-full" />
            </div>
            <div>
                <label class="block font-semibold">Months</label>
                <input
                    type="number"
                    v-model="form.months"
                    min="1"
                    class="w-full px-3 py-2 border rounded"
                    readonly />
            </div>
        </div>

        <!-- PRICE -->
        <div>
            <label class="block font-semibold">Total Price</label>
            <input
                type="text"
                :value="currencyFormatter.format(form.selected_price)"
                readonly
                tabindex="-1"
                @focus="e => e.target.blur()"
                class="w-full px-3 py-2 bg-gray-100 border-0 rounded cursor-default select-none" />

            <input
                type="hidden"
                v-model="form.selected_price" />
        </div>

        <!-- SUBMIT -->
        <div>
            <button
                type="submit"
                class="px-4 py-1 text-sm text-white bg-pink-600 rounded hover:bg-pink-700">
                Book {{ buttonName }}
            </button>
        </div>
    </form>
</template>
