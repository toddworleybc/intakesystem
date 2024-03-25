<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import BtnComponent from '@/Components/Button.vue';  
    import { CheckCircleIcon } from '@heroicons/vue/24/outline';
    import { useAttrs } from 'vue';
    import { usePage } from '@inertiajs/vue3';

    const client = useAttrs();

</script>


<template>

    <MainLayout>

        <h1 class="text-2xl text-center py-2 text-white bg-blue-400 mb-4">Client {{ client.name }}</h1>


        <div v-if="client.created" class="flex justify-center items-center space-x-4 mb-4">
               
            <div id="message-banner" class="flex item-center space-x-2 bg-green-500 text-white justify-center py-2 w-1/2 rounded-lg">

                <div>
                    <p class="mb-0">Client Successfully Created!</p>
                </div>
                
                
                <CheckCircleIcon class="w-5 h-5 mt-0.5"/>
                
            </div>
                 <!-- #/message-banner  -->

            

        </div>
        <!-- @/client_created  -->

        <div class="border border-gray-200 px-4 py-10 rounded-sm shadow-lg relative">

            
            <div class="border-b-2 border-gray-500 pb-2 mb-10 flex justify-between items-end">

                <div>
                    <h2 class="mb-2">Client Information</h2>
                    <div class="flex items-center space-x-4">
                        <span class="bg-green-700 text-sm text-white py-1 px-2 rounded">Id: {{ client.id }}</span>
                        <span class="bg-yellow-500 text-sm text-white py-1 px-2 rounded">
                            Status: Pending
                        </span>
                    </div>
                </div>
                
                
                <div>
                    <button v-if="client.created" class="btn btn-warning">
                        Send Welcome Email
                    </button>
                    <BtnComponent link="#" v-else>
                        Edit Client
                    </BtnComponent>
                </div>
                
            </div>
            

            <div id="client-info-container" class="flex">

                <div id="client-info" class="w-1/2">
                    <div class="border-b-2 border-gray-300 w-8/12">
                        <p><span>Name:</span> {{ client.name }}</p>
                        <p><span>Email:</span> {{ client.email }}</p>
                        <p><span>Phone:</span> {{ client.phone }}</p>
                        <p><span>Location:</span> {{ client.location }}</p>
                    </div>
                    <div class="mt-2">
                        <p>Created: {{  client.createdAt }}</p>
                        <p>Updated: {{  client.updatedAt  }}</p>
                    </div>
                    
                    
                </div>
                <!-- #/client-info  -->
                
                <div id="client-details" class="w-1/2">

                    <div class="flex justify-between items-center border-b-2 border-gray-400 mb-4">
                        <h3>Details:</h3>
                        <p class="font-semibold">Initial Quote: ${{ client.quote }}</p>
                    </div>
                    
                    <div class="border p-4 min-h-52">
                        <p>{{ client.details }}</p>
                    </div>
                
                </div>

            </div>
            <!-- #/client-info-container  -->
            

        </div>
        <!-- ./form-container  -->


    </MainLayout>

</template>