<script setup>
    import { Head, Link } from '@inertiajs/vue3';


</script>


<template>
    <Head title="Client Intake" />

    <header class="bg-green-900  text-white text-center py-4">
        Header
    </header>
    <main class="flex justify-start">
        <aside class="w-1/6 border-r-2 border-gray-600 px-6 py-4">
            <nav>
                <ul>
                    <li class="mb-4"><Link :href="route('dashboard')">Dashboard</Link></li>
                    <li class="mb-4"><Link :href="route('clients.create')">Intake</Link></li>
                    <li class="mb-4"><Link :href="route('clients.index')">Clients</Link></li>
                    <li class="mb-4"><Link :href="route('emails')">Emails</Link></li>
                    <li class="mb-4"><Link href="#">Something?</Link></li>
                </ul>
            </nav>
        </aside>
        <section id="main-section" class="w-full px-6 py-4">
            <slot />
        </section>
    </main>
    <footer class="bg-green-900 text-white text-center py-4">
        <p>Copyright 2024</p>
    </footer>

</template>


<style>

.btn {
    @apply py-2 inline-block px-4 text-white rounded-md shadow-lg active:shadow-sm transform active:scale-95 active:bg-gray-500 transition-all;
}

.btn-safe {
    @apply bg-green-600;
}


</style>