<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import BtnComponent from '@/Components/Button.vue';
    import { PlusCircleIcon } from '@heroicons/vue/24/solid';
    import { usePage, Link, router } from '@inertiajs/vue3';
    import phoneNumberFormat from '@/Utilities/phoneNumberFormater';
    import MessageBanner from '@/Components/messageBanner.vue';

    const clients = usePage().props.clients;
    

    function statusBg(client) {

        let statusColor = null;

        if(client.status === "pending") statusColor = "bg-yellow-500";

        if(client.status === "active") statusColor = "bg-green-500";

        if(client.status === "cancelled") statusColor = "bg-red-500";

        return statusColor;


    }

    function editUser(id) {
        router.visit(route('clients.show', id));
    }


    function activePendingClients(client) {
        return client.status !== 'cancelled';
    }//


</script>


<template>

    <MainLayout>


        <div class="text-white py-2 bg-blue-600 mb-4 text-center px-4">
            <h1 class="text-2xl">Clients</h1>
        </div>

        <MessageBanner :show="$page.props.flash.success" type="warning">
            {{ $page.props.flash.success }}
        </MessageBanner>

        <section class="flex justify-between items-center my-6">
            
            <div>
                Filters
            </div>
            <div>
                <BtnComponent :link="route('clients.create')">
                    <div class="flex justify-between items-center space-x-2">
                        <span>Create New Client</span>
                        <PlusCircleIcon class="w-5 h-5" />
                    </div>
                </BtnComponent>
            </div>
        </section>

        <section v-if="clients.filter(activePendingClients).length !== 0">
            <table class="table-fixed border-collapse border w-full">
                <thead class="bg-green-300">
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                    <tr v-for="client in clients.filter(activePendingClients)" @click.prevent="editUser(client.id)" :key="client.id" class="text-center">
                        <td>{{ client.name }}</td>
                        <td>{{ phoneNumberFormat(client.phone) }}</td>
                        <td>{{ client.email }}</td>
                        <td><span :class="statusBg(client)" class="rounded-lg capitalize py-1 w-1/2 block mx-auto text-white">{{ client.status }}</span></td>
                    </tr>

                </tbody>
            </table>
        </section>

        <section v-else class="bg-yellow-500 text-white text-center py-4">
            No Clients Created
        </section>

    </MainLayout>


</template>


<style>


    th,
    td {
        @apply border border-gray-600 py-2 px-1;
    }

    tbody tr {
        @apply cursor-pointer;
    }

    tbody tr:hover {
        @apply bg-blue-200;
    }

    tbody tr:active {
        @apply bg-gray-200;
    }



</style>