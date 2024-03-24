<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import { CheckCircleIcon } from '@heroicons/vue/24/outline';
    import { useAttrs } from 'vue';

    const client = useAttrs();


</script>


<template>

    <MainLayout>

        <h1 class="text-2xl text-center py-2 text-white bg-blue-400 mb-8">Enter Clients Information</h1>


        <div class="border border-gray-200 px-4 py-10 rounded-sm shadow-lg">

            
            <div>
                <p>Client Successfully Created!</p>
                <CheckCircleIcon class="w-5 h-5"/>
            </div>

            <p>{{ client.name }}</p>


        </div>
        <!-- ./form-container  -->


    </MainLayout>

</template>