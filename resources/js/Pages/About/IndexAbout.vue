<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';

</script>

<template>
    <GuestLayout>
        <!-- Header Section -->
       <section class="w-full bg-center bg-cover" style="background-image: url('/files_grits/grit_services.jpg');">
            <div class="py-24 text-center">
                <h1 class="text-4xl font-bold text-white">About Us</h1>
            </div>
        </section>

        <!-- Content Section -->
        <section class="px-10 py-16 mx-auto max-w-7xl">
        <!-- Headings -->
        <div class="mb-12 text-center">
            <h2 class="mb-4 text-3xl font-bold">The Modern Spaces Converted For Digital Innovations</h2>
            <p class="max-w-2xl mx-auto text-lg text-gray-600">
            Possim sapientem in eos. Cum graeci civibus appareat ne sed omy at usu id illud utamur eleifend. Has sonet tincidunt sit placerat mel nonumy impetus partem deseruise tempor primis singulis
            </p>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <div class="p-6 text-center border rounded-lg shadow">
                <div class="mb-4 text-4xl">🚲</div>
                <h3 class="mb-2 text-xl font-semibold">Free Parking</h3>
                <p class="text-gray-600">Nostro putant laboramus id ius. Per facilis euripidis ad prompta vocibus molestiae at est labitur equidem in has eum.</p>
            </div>
            <div class="p-6 text-center border rounded-lg shadow">
                <div class="mb-4 text-4xl">📶</div>
                <h3 class="mb-2 text-xl font-semibold">Free Internet</h3>
                <p class="text-gray-600">Nostro putant laboramus id ius. Per facilis euripidis ad prompta vocibus molestiae at est labitur equidem in has eum.</p>
            </div>
            <div class="p-6 text-center border rounded-lg shadow">
                <div class="mb-4 text-4xl">🖨️</div>
                <h3 class="mb-2 text-xl font-semibold">Office Supplies</h3>
                <p class="text-gray-600">Nostro putant laboramus id ius. Per facilis euripidis ad prompta vocibus molestiae at est labitur equidem in has eum.</p>
            </div>
            <div class="p-6 text-center border rounded-lg shadow">
            <div class="mb-4 text-4xl">🧘‍♂️</div>
            <h3 class="mb-2 text-xl font-semibold">Relax Room</h3>
            <p class="text-gray-600">Nostro putant laboramus id ius. Per facilis euripidis ad prompta vocibus molestiae at est labitur equidem in has eum.</p>
            </div>
            <div class="p-6 text-center border rounded-lg shadow">
            <div class="mb-4 text-4xl">🏢</div>
            <h3 class="mb-2 text-xl font-semibold">Conference Rooms</h3>
            <p class="text-gray-600">Nostro putant laboramus id ius. Per facilis euripidis ad prompta vocibus molestiae at est labitur equidem in has eum.</p>
            </div>
            <div class="p-6 text-center border rounded-lg shadow">
            <div class="mb-4 text-4xl">☕</div>
            <h3 class="mb-2 text-xl font-semibold">Coffee Shop</h3>
            <p class="text-gray-600">Nostro putant laboramus id ius. Per facilis euripidis ad prompta vocibus molestiae at est labitur equidem in has eum.</p>
            </div>
        </div>
        </section>

    </GuestLayout>
</template>