<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <!-- <div>
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 text-gray-500 fill-current" />
            </Link>
        </div> -->

    <body class="text-gray-800 bg-white">
        <header class="fixed top-0 z-50 w-full bg-red-500 shadow">
            <div class="flex items-center justify-between px-6 py-4 mx-auto max-w-7xl">
                <!-- Left: Logo -->
                <div class="flex-shrink-0 order-1 md:order-1">
                    <a href="/">
                        <img
                            src="/images/logo.png"
                            alt="Logo"
                            class="h-12" />
                    </a>
                </div>

                <!-- Center: Navigation -->
                <nav class="absolute order-2 hidden space-x-6 text-gray-300 -translate-x-1/2 md:flex left-1/2">
                    <a
                        href="/about"
                        class="hover:text-blue-600"
                        >About Us</a
                    >
                    <a
                        href="/offices"
                        class="font-400"
                        >Offices</a
                    >
                    <a
                        href="/pricing"
                        class="font-400"
                        >Pricing</a
                    >
                    <a
                        href="/contact"
                        class="hover:text-blue-600"
                        >Contact</a
                    >
                </nav>

                <!-- Right: Search -->
                <div class="flex justify-end order-3 md:flex">
                    <div class="mr-4">
                        <a
                            href="/login"
                            class="p-2 bg-gray-400 rounded-sm text-warning-700 hover:text-blue-600"
                            >Login</a
                        >
                    </div>
                    <button class="text-white hover:text-blue-300 focus:outline-none">
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <circle
                                cx="11"
                                cy="11"
                                r="8" />
                            <line
                                x1="21"
                                y1="21"
                                x2="16.65"
                                y2="16.65" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile menu -->
                <div class="order-4 md:hidden">
                    <button class="text-gray-100 focus:outline-none">
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <main class="container p-2 mx-auto">
            <slot />
        </main>

        <footer class="p-6 mt-10 text-center text-white bg-red-500">
            <p>&copy; 2025 GritSpace</p>
        </footer>
    </body>
</template>
