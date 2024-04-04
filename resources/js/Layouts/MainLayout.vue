<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import { HalfCircleSpinner } from 'epic-spinners';
    import Loader from '@/Utilities/loader.js';

   

</script>


<template>
    <Head title="Client Intake" />

    <header class="bg-green-900  text-white text-center py-4">
        Evergreen By Design Client Intake
    </header>
    <main class="flex justify-start relative">
        <!-- Loader  -->
        <div v-if="Loader.isLoading" id="loading" class="absolute top-0 right-0 left-0 bottom-0 bg-white bg-opacity-75 z-50">
            <div id="loading-container" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">

                <div>
                    <p class="text-3xl"><span class="mb-4 block animate-pulse">{{ Loader.action }}</span></p>
                    <half-circle-spinner class="block mx-auto"
                                            :animation-duration="1000"
                                            :size="60"
                                            color="#22c55e"
                                    />
                </div>
                
            </div>
        </div>
        <aside class="w-1/6 border-r-2 border-gray-600 px-6 py-4 text-center text-lg">
            <nav>
                <ul>
                    <li class="mb-4"><Link :href="route('clients.index')">Clients</Link></li>
                    <li class="mb-4"><Link :href="route('clients.create')">New Intake</Link></li>  
                </ul>
            </nav>
        </aside>
        <section id="main-section" class="w-full px-6 py-4">
            <slot />
        </section>
    </main>
    <footer class="bg-green-900 text-white text-center py-4">
        <p class="mb-0">Copyright 2024</p>
    </footer>

</template>


<style>

body {
    overflow: hidden;
}

#main-section {
    @apply overflow-y-auto h-screen pb-36
}

a:not(.btn, .btn-component) {
    @apply text-blue-600 transition-all;
}

a:hover:not(.btn, .btn-component) {
    @apply text-blue-300;
}

a:active:not(.btn, .btn-component) {
    @apply text-gray-300;
}

h2 {
    @apply text-2xl font-semibold;
}

h3 {
    @apply text-xl font-semibold;
}

p {
    @apply mb-2;
}

.btn {
    @apply py-2 inline-block px-4 text-white rounded-md shadow-lg active:shadow-sm transform active:scale-95 active:bg-gray-500 transition-all bg-blue-500;
}

.btn:hover {
    @apply bg-blue-400;
}

.btn-safe {
    @apply bg-green-600;
}

.btn-safe:hover {
    @apply bg-green-400;
}


.btn-warning {
    @apply bg-yellow-500;
}

.btn-warning:hover {
    @apply bg-yellow-300;
}

.btn-danger {
    @apply bg-red-500;
}

.btn-danger:hover {
    @apply bg-red-300;
}

</style>