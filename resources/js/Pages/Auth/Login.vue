<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div
            v-if="status"
            class="mb-2 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <!-- Full-screen centered form -->
        <div class="flex items-center justify-center min-h-[85vh] px-4">
            <form
                @submit.prevent="submit"
                class="w-full max-w-md space-y-1">
                <div>
                    <InputLabel
                        for="email"
                        value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="block w-full mt-1"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username" />
                    <InputError
                        class="mt-2"
                        :message="form.errors.email" />
                </div>

                <div class="pb-5">
                    <InputLabel
                        for="password"
                        value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        class="block w-full mt-1"
                        v-model="form.password"
                        required
                        autocomplete="current-password" />
                    <InputError
                        class="mt-2"
                        :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between pb-3">
                    <label class="flex items-center">
                        <Checkbox
                            name="remember"
                            v-model:checked="form.remember" />
                        <span class="text-sm text-gray-600 ms-2">Remember me</span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-gray-600 underline hover:text-gray-900">
                        Forgot your password?
                    </Link>
                </div>

                <div class="flex items-center justify-between">
                    <Link
                        :href="route('register')"
                        class="text-sm text-gray-600 underline hover:text-gray-900">
                        Register
                    </Link>

                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        Log in
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
