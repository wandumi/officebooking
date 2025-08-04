<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import { ref, watch, computed } from 'vue';
import Calendar from 'primevue/calendar';
import { addMonths, addYears, eachDayOfInterval } from 'date-fns';
import StatusFeedback from '@/Components/StatusFeedback.vue';

const props = defineProps({
    officeId: Number,
    pricingOptions: Object,
    availablePlans: Array,
    buttonName: String,
    categoryName: String,
    selectedPlan: String,
    categoryId: Number,
    bookedDates: {
        type: Array,
        default: () => [],
    },
});

const dailyPlans = ['daily'];
const today = new Date();

const successMessage = ref(null);
const bookingConflict = ref(null);

// Form setup
const form = useForm({
    office_id: props.officeId,
    plan: props.selectedPlan || props.availablePlans[0] || '',
    selected_dates: [],
    weekdays_count: 0,
    start_date: null,
    months: 1,
    end_date: null,
    total_price: 0,
    category_id: props.categoryId,
});

const unitPrice = computed(() => props.pricingOptions[form.plan] || 0);

const weekdaysCount = computed(
    () =>
        form.selected_dates.filter(date => {
            const day = new Date(date).getDay();
            return day !== 0 && day !== 6;
        }).length
);

const generateDisabledWeekends = () => {
    const future = addYears(today, 2);
    const allDates = eachDayOfInterval({ start: today, end: future });
    return allDates.filter(date => [0, 6].includes(date.getDay()));
};

const disabledWeekends = computed(() => (dailyPlans.includes(form.plan) ? generateDisabledWeekends() : []));

// Monthly logic
watch([() => form.start_date, () => form.months, () => form.plan], () => {
    if (!dailyPlans.includes(form.plan) && form.start_date) {
        const start = new Date(form.start_date);
        const end = addMonths(start, form.months);
        end.setDate(start.getDate());
        form.end_date = end.toISOString().split('T')[0];
        form.total_price = form.months * unitPrice.value;
    }
});

// Daily logic
watch(
    [() => form.plan, () => form.selected_dates],
    () => {
        if (dailyPlans.includes(form.plan)) {
            form.weekdays_count = weekdaysCount.value;
            const sorted = [...form.selected_dates].sort();
            form.start_date = sorted[0] || null;
            form.end_date = sorted[sorted.length - 1] || null;
            form.total_price = unitPrice.value * weekdaysCount.value;
        }
    },
    { immediate: true }
);

// Reset on plan switch
watch(
    () => form.plan,
    () => {
        form.selected_dates = [];
        form.weekdays_count = 0;
        form.start_date = null;
        form.end_date = null;
        form.months = 1;
        form.total_price = 0;
    }
);

// Clear error when date or plan is changed
watch([() => form.selected_dates, () => form.start_date, () => form.end_date, () => form.plan], () => {
    if (bookingConflict.value) {
        bookingConflict.value = null;
    }
});

const currencyFormatter = new Intl.NumberFormat('en-ZA', {
    style: 'currency',
    currency: 'ZAR',
});

const submit = () => {
    if (dailyPlans.includes(form.plan) && form.selected_dates.length > 0) {
        const sorted = [...form.selected_dates].sort();
        form.start_date = sorted[0];
        form.end_date = sorted[sorted.length - 1];
    }

    form.weekdays_count = weekdaysCount.value;

    form.post(route('bookingoffice.store'), {
        preserveScroll: true,
        onError: errors => {
            bookingConflict.value = errors.booking_conflict ?? null;
        },
        onSuccess: () => {
            successMessage.value = 'Booking created successfully!';
            bookingConflict.value = null;

            setTimeout(() => {
                successMessage.value = null;

                if (props.categoryName === 'Dedicated Desk') {
                    Inertia.visit(route('bookingdedicated.show'));
                }

                if (props.categoryName === 'Closed Office') {
                    Inertia.visit(route('bookingoffices.show'));
                }
            }, 1500);
        },
    });
};
</script>

<template>
    <StatusFeedback
        :conflict="bookingConflict"
        :success="successMessage" />

    <form
        @submit.prevent="submit"
        class="pt-5 space-y-4">
        <!-- Plan Selector -->
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
                {{
                    availablePlans
                }}
            </select>
        </div>

        <!-- Daily Booking -->
        <div
            v-if="dailyPlans.includes(form.plan)"
            class="space-y-4">
            <div>
                <label class="block font-semibold">Select Dates</label>
                <Calendar
                    v-model="form.selected_dates"
                    selectionMode="multiple"
                    :minDate="today"
                    :disabledDates="[...disabledWeekends, ...bookedDates.map(d => new Date(d))]"
                    :disabledDays="[0, 6]"
                    :manualInput="false"
                    dateFormat="yy-mm-dd"
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

        <!-- Monthly Booking -->
        <div
            v-else
            class="space-y-4">
            <div>
                <label class="block font-semibold">Start Date</label>
                <Calendar
                    v-model="form.start_date"
                    :minDate="today"
                    dateFormat="yy-mm-dd"
                    showIcon
                    :manualInput="false"
                    class="w-full" />
                <span
                    v-if="form.errors.start_date"
                    class="text-sm text-red-600">
                    {{ form.errors.start_date }}
                </span>
            </div>
            <div>
                <label class="block font-semibold">Duration (Months)</label>
                <select
                    v-model="form.months"
                    class="w-full px-3 py-2 border rounded"
                    required>
                    <option
                        v-for="m in 12"
                        :key="m"
                        :value="m">
                        {{ m }} {{ m === 1 ? 'Month' : 'Months' }}
                    </option>
                </select>
            </div>
            <div>
                <label class="block font-semibold">End Date</label>
                <input
                    type="text"
                    v-model="form.end_date"
                    class="w-full px-3 py-2 border-0 rounded bg-green-50"
                    readonly
                    tabindex="-1"
                    @focus="e => e.target.blur()" />
                <span
                    v-if="form.errors.end_date"
                    class="text-sm text-red-600">
                    {{ form.errors.end_date }}
                </span>
            </div>
        </div>

        <!-- Total Price -->
        <div>
            <label class="block font-semibold">Total Price</label>
            <input
                type="text"
                :value="currencyFormatter.format(form.total_price)"
                readonly
                tabindex="-1"
                @focus="e => e.target.blur()"
                class="w-full px-3 py-2 bg-gray-100 border-0 rounded cursor-default select-none" />
            <input
                type="hidden"
                v-model="form.total_price" />
        </div>

        <!-- Hidden Fields -->
        <input
            type="hidden"
            v-model="form.office_id" />
        <input
            type="hidden"
            v-model="form.start_date" />
        <input
            type="hidden"
            v-model="form.end_date" />
        <input
            type="hidden"
            v-model="form.weekdays_count" />
        <input
            type="hidden"
            v-model="form.plan" />
        <input
            type="hidden"
            v-model="form.category_id" />

        <!-- Submit -->
        <div>
            <button
                type="submit"
                class="px-4 py-2 text-sm text-white rounded bg-primary hover:bg-bluemain">
                Book {{ buttonName }}
            </button>
        </div>
    </form>
</template>
