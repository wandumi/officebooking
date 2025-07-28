<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

const page = usePage();
const can = page.props.can || {};
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <!-- Left Section -->
                        <div class="flex items-center gap-10">
                            <!-- Logo -->
                            <Link
                                :href="route('dashboard')"
                                class="flex items-center">
                                <ApplicationLogo class="block w-auto h-12 text-gray-800 fill-current" />
                            </Link>

                            <!-- Navigation Links -->
                            <div class="items-center hidden space-x-6 sm:flex">
                                <!-- Dropdown: Spaces -->
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                    >Dashboard</NavLink
                                >

                                <NavLink
                                    :href="route('booking.offices')"
                                    v-if="can['view book offices']"
                                    >Grit Spaces</NavLink
                                >

                                <NavLink
                                    :href="route('virtual.home')"
                                    v-if="can['view book boardrooms']"
                                    >Virtual Office</NavLink
                                >

                                <NavLink
                                    :href="route('booking.boardrooms')"
                                    v-if="can['view book boardrooms']"
                                    >Boardrooms</NavLink
                                >

                                <Dropdown
                                    align="right"
                                    width="48">
                                    <template #trigger>
                                        <button
                                            class="flex items-center gap-1 text-sm font-medium text-gray-600 transition hover:text-gray-800 focus:outline-none">
                                            Bookings
                                            <svg
                                                class="w-4 h-4"
                                                fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink
                                            :href="route('bookingoffices.show')"
                                            v-if="can['view book extras']"
                                            >Closed Offices</DropdownLink
                                        >

                                        <DropdownLink
                                            :href="route('bookingdedicated.show')"
                                            v-if="can['view book extras']"
                                            >Dedicated Desks</DropdownLink
                                        >

                                        <DropdownLink
                                            :href="route('bookinghotdesk.show')"
                                            v-if="can['view book extras']"
                                            >Hot Desks</DropdownLink
                                        >
                                        <DropdownLink
                                            :href="route('bookingvirtual.show')"
                                            v-if="can['view book extras']"
                                            >Virtual Offices</DropdownLink
                                        >
                                        <DropdownLink
                                            :href="route('bookingboardroom.show')"
                                            v-if="can['view book extras']"
                                            >Boardrooms</DropdownLink
                                        >
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Right Section: User Avatar -->
                        <div class="items-center hidden space-x-2 sm:flex">
                            <Dropdown
                                v-if="can['manage settings']"
                                align="right"
                                width="48">
                                <template #trigger>
                                    <button
                                        class="flex items-center gap-1 text-sm font-medium text-gray-600 transition hover:text-gray-800 focus:outline-none">
                                        Product Settings
                                        <svg
                                            class="w-4 h-4"
                                            fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink
                                        v-if="can['view offices']"
                                        :href="route('admin.closedoffices')"
                                        >Closed Offices</DropdownLink
                                    >
                                    <DropdownLink
                                        v-if="can['view offices']"
                                        :href="route('admin.dedicateddesk')"
                                        >Dedicated Desks</DropdownLink
                                    >
                                    <DropdownLink
                                        v-if="can['view help desks']"
                                        :href="route('admin.help-desks')"
                                        >Hot Desks</DropdownLink
                                    >
                                    <DropdownLink
                                        v-if="can['view virtual offices']"
                                        :href="route('admin.virtual-offices')"
                                        >Virtual Offices</DropdownLink
                                    >
                                    <DropdownLink
                                        v-if="can['view boardrooms']"
                                        :href="route('admin.boardrooms')"
                                        >Boardrooms</DropdownLink
                                    >
                                </template>
                            </Dropdown>

                            <Dropdown
                                v-if="can['manage settings']"
                                align="right"
                                width="48">
                                <template #trigger>
                                    <button
                                        class="flex items-center gap-1 text-sm font-medium text-gray-600 transition hover:text-gray-800 focus:outline-none">
                                        System Settings
                                        <svg
                                            class="w-4 h-4"
                                            fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('admin.locations')">Locations</DropdownLink>
                                    <DropdownLink :href="route('admin.categories')">Categories</DropdownLink>
                                    <DropdownLink :href="route('admin.offices_rates')">Service Levels</DropdownLink>
                                    <DropdownLink :href="route('admin.amenities')">Amenities</DropdownLink>
                                </template>
                            </Dropdown>

                            <Dropdown
                                align="right"
                                width="48">
                                <template #trigger>
                                    <div class="flex items-center space-x-1">
                                        <div class="text-sm font-medium text-primary">
                                            {{ $page.props.auth.user.name }}
                                        </div>
                                        <button
                                            class="flex items-center overflow-hidden transition rounded-full focus:outline-none hover:ring-2 hover:ring-gray-300">
                                            <img
                                                src="/files_grits/user.png"
                                                alt="User Avatar"
                                                class="object-cover w-5 h-5 rounded-full" />
                                        </button>
                                    </div>
                                </template>

                                <template #content>
                                    <DropdownLink
                                        :href="route('admin.manage.user')"
                                        v-if="can['manage settings']"
                                        >Manage</DropdownLink
                                    >
                                    <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        >Log Out</DropdownLink
                                    >
                                </template>
                            </Dropdown>
                        </div>

                        <!-- Hamburger Menu (Mobile) -->
                        <div class="flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="p-2 text-gray-400 rounded-md hover:bg-gray-100 hover:text-gray-600 focus:outline-none">
                                <svg
                                    class="w-6 h-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        v-if="!showingNavigationDropdown"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path
                                        v-else
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden">
                    <!-- Mobile User Info -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4 space-x-3">
                            <img
                                src="/files_grits/40.jpg"
                                alt="User Avatar"
                                class="object-cover w-8 h-8 rounded-full" />
                            <div>
                                <div class="text-base font-medium text-primary">{{ $page.props.auth.user.name }}</div>
                                <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.user.email }}</div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                >Log Out</ResponsiveNavLink
                            >
                        </div>
                    </div>
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                            >Dashboard</ResponsiveNavLink
                        >
                        <ResponsiveNavLink :href="route('admin.boardrooms')">Boardroom</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.virtual-offices')">Virtual</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.help-desks')">Help Desk</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.offices')">Offices</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.locations')">Locations</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.categories')">Categories</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.offices_rates')">Office Rates</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.amenities')">Amenities</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('booking.offices')">Book Offices</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('booking.boardrooms')">Book Boardroom</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('booking.extras')">Extras</ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('admin.manage.user')"
                            v-if="can['manage settings']"
                            >Manage</ResponsiveNavLink
                        >
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                v-if="$slots.header"
                class="shadow">
                <div class="px-4 py-6 mx-auto text-lg max-w-7xl sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
