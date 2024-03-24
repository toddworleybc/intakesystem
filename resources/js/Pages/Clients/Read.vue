<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import BtnComponent from '@/Components/Button.vue';
    import { PlusCircleIcon } from '@heroicons/vue/24/solid';

</script>


<template>

    <MainLayout>

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

        <section>
            <table class="table-fixed border-collapse border w-full">
                <thead class="bg-green-300">
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center">
                        <td>1</td>
                        <td>Todd</td>
                        <td>todd@gmail.com</td>
                        <td>Active</td>
                    </tr>
                </tbody>
            </table>
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