<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

</script>

<template>

    <GuestLayout>
        <Head title="Contacts" />
        
        <div class="container mx-auto">
            <!-- Header Section -->
               <section class="w-full bg-center bg-cover" style="background-image: url('/files_grits/grit_services.jpg');">
            <div class="py-24 text-center">
                <h1 class="text-4xl font-bold text-white">Contact</h1>
            </div>
        </section>

            <!-- Info Section -->
            <section class="w-full pt-12 pb-5">
                <div class="container grid grid-cols-1 mx-auto">
                 
                    <div class="text-center">
                        <h2 class="text-2xl font-bold">What’s The Next Step</h2>
                    </div>
                   
                </div>
            </section>

            <!-- Contact Info Section -->
            <section class="w-full py-12 bg-gray-50">
                <div class="container grid grid-cols-1 gap-2 mx-auto md:grid-cols-3 lg:grid-cols-3">
                    <!-- Head Office -->
                    <div class="text-center">
                        <i class="text-4xl text-blue-500 fi0e65-fi-0e65-location"></i>
                        <h3 class="mt-4 text-lg font-semibold">Head Office</h3>
                        <p>3721 Dogwood Road,<br> Brooklyn, NY 10019 USA</p>
                    </div>

                    <!-- Phone Us -->
                    <div class="text-center">
                        <i class="text-4xl text-blue-500 fi0e65-fi-0e65-phone"></i>
                        <h3 class="mt-4 text-lg font-semibold">Phone Us</h3>
                        <p>+ 1 (800) 923 – 3687 – 4500<br>+ 1 (800) 923 – 4500 – 3687</p>
                    </div>

                    <!-- Message Us -->
                    <div class="text-center">
                        <i class="text-4xl text-blue-500 fi0e65-fi-0e65-email"></i>
                        <h3 class="mt-4 text-lg font-semibold">Message Us</h3>
                        <p>support@example.com<br>my-projects@example.com</p>
                    </div>
                </div>
            </section>

            <!-- Contact Form Section -->
            <section class="w-full py-12 bg-gray-50">
                <div class="container mx-auto">
                    <form action="/picozo/demo/contact/#wpcf7-f2069-p8-o1" method="post" class="space-y-4">
                        <input type="text" name="your-name" placeholder="Full Name" class="w-full p-3 border border-gray-300 rounded-lg" required>
                        <input type="email" name="your-email" placeholder="Email Address" class="w-full p-3 border border-gray-300 rounded-lg" required>
                        <input type="text" name="your-subject" placeholder="Subject" class="w-full p-3 border border-gray-300 rounded-lg">
                        <textarea name="your-message" placeholder="Message" class="w-full p-3 border border-gray-300 rounded-lg" rows="4" required></textarea>
                        <div class="text-center">
                            <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-700">Submit Now</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>

    </GuestLayout>

</template>