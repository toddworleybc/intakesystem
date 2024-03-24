<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import { CircleCheckIcon } from '@heroicons/vue/24/solid';


</script>


<template>

    <MainLayout>

        <h1 class="text-2xl text-center py-2 text-white bg-blue-400 mb-8">Enter Clients Information</h1>


        <div class="border border-gray-200 px-4 py-10 rounded-sm shadow-lg">

            <p>Client Successfully Created!</p>
            <div>
                <CircleCheckIcon class="w-5 h-5"/>
            </div>


        </div>
        <!-- ./form-container  -->


    </MainLayout>

</template>