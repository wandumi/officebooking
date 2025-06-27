<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import Calendar from 'primevue/calendar';

const props = defineProps({
    bookableType: String,
    bookableId: Number,
    pricingOptions: Number,
    availablePlans: String,
    selectedDuration: Number,
    buttonName: String,
});

const today = new Date();

const form = useForm({
    bookable_type: props.bookableType,
    bookable_id: props.bookableId,
    plan: props.availablePlans,
    selected_dates: [],
    time_slots: {},
    selected_price: 0,
});

// Helpers
const getDateKey = date => new Date(date).toISOString().split('T')[0];
const normalizedPlan = computed(() => props.availablePlans.toLowerCase().replace(/\s+/g, '_'));
const isHalfDay = computed(() => normalizedPlan.value === 'half_day');
const isFixedPackage = computed(() => [5, 10, 20].includes(props.selectedDuration));

const weekdaysOnly = computed(() =>
    form.selected_dates.filter(date => {
        const d = new Date(date);
        return d.getDay() !== 0 && d.getDay() !== 6;
    })
);

// Trim to selectedDuration for package plans
watch(
    () => form.selected_dates,
    newDates => {
        if (!isFixedPackage.value || isHalfDay.value) return;

        const weekdays = newDates.filter(date => {
            const d = new Date(date);
            return d.getDay() !== 0 && d.getDay() !== 6;
        });

        if (weekdays.length > props.selectedDuration) {
            form.selected_dates = weekdays.slice(0, props.selectedDuration);
        }
    },
    { deep: true }
);

// Time slot selection for Half Day
const getTimeBlock = date => form.time_slots[getDateKey(date)]?.block || '';
const setTimeBlock = (date, e) => {
    const key = getDateKey(date);
    form.time_slots[key] = { block: e.target.value };
};

const halfDayCount = computed(() => {
    if (!isHalfDay.value) return 0;
    return weekdaysOnly.value.reduce((count, date) => {
        const key = getDateKey(date);
        return form.time_slots[key]?.block ? count + 1 : count;
    }, 0);
});

const totalPrice = computed(() => {
    const base = props.pricingOptions || 0;

    if (isHalfDay.value) {
        return base * halfDayCount.value;
    }

    if (isFixedPackage.value) {
        return base;
    }

    return weekdaysOnly.value.length * base;
});

const currencyFormatter = new Intl.NumberFormat('en-ZA', {
    style: 'currency',
    currency: 'ZAR',
});

const submit = () => {
    form.selected_price = totalPrice.value;
    form.post(route('bookings.store'));
};
</script>

<template>
    <form
        @submit.prevent="submit"
        class="space-y-6">
        <!-- Plan -->
        <div>
            <label class="block mb-1 font-semibold">Plan</label>
            <input
                type="text"
                class="w-full px-3 py-2 bg-gray-100 border rounded"
                :value="form.plan"
                readonly />
        </div>

        <!-- Calendar -->
        <div>
            <label class="block font-semibold">
                {{ isHalfDay ? 'Select Half-Day Date(s)' : 'Select Booking Date(s)' }}
            </label>
            <Calendar
                v-model="form.selected_dates"
                selectionMode="multiple"
                :minDate="today"
                :disabledDays="[0, 6]"
                :manualInput="false"
                dateFormat="yy-mm-dd"
                showIcon
                class="w-full" />
        </div>

        <!-- Half Day Slot Picker -->
        <div
            v-if="isHalfDay && weekdaysOnly.length"
            class="space-y-4">
            <div
                v-for="date in weekdaysOnly"
                :key="getDateKey(date)"
                class="p-4 border rounded bg-gray-50">
                <p class="mb-2 text-sm font-semibold">
                    {{ new Date(date).toLocaleDateString() }}
                </p>
                <select
                    class="w-full px-2 py-1 border rounded"
                    :value="getTimeBlock(date)"
                    @change="e => setTimeBlock(date, e)"
                    required>
                    <option
                        disabled
                        value="">
                        Select Half Day
                    </option>
                    <option value="morning">Morning (09:00–12:00)</option>
                    <option value="afternoon">Afternoon (13:00–17:00)</option>
                </select>
            </div>
        </div>

        <!-- Summary -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Selected Weekdays</label>
            <p class="px-3 py-2 border rounded bg-gray-50">
                {{ weekdaysOnly.length }} day(s)
                <template v-if="isHalfDay">, {{ halfDayCount }} block(s) selected</template>
            </p>
        </div>

        <!-- Price -->
        <div>
            <label class="block font-semibold">Total Price</label>
            <input
                type="text"
                :value="currencyFormatter.format(totalPrice)"
                readonly
                tabindex="-1"
                @focus="e => (e.target as HTMLInputElement).blur()"
                class="w-full px-3 py-2 bg-gray-100 border-0 rounded cursor-default select-none" />

            <input
                type="hidden"
                v-model="form.selected_price" />
        </div>

        <!-- Submit -->
        <div>
            <button
                type="submit"
                class="px-4 py-1 text-sm text-white bg-pink-600 rounded hover:bg-pink-700">
                Book {{ props.buttonName }}
            </button>
        </div>
    </form>
</template>
