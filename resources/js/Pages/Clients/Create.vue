<script setup>
    import MainLayout from '@/Layouts/MainLayout.vue';
    import { useForm } from '@inertiajs/vue3';





    const form = useForm({
        name: null,
        email: null,
        phone: null,
        location: null,
        quote: null,
        details: null
    });

    
// submits the create client form

    function submitForm() {
       const $url = route('clients.store');

        form.post($url, {
            onError: function(errors) {
                console.log(errors);
            }
        });

    } 




</script>


<template>

    <MainLayout>

        <h1 class="text-2xl text-center py-2 text-white bg-blue-400 mb-8">Enter Clients Information</h1>


        <div class="border border-gray-200 px-4 py-10 rounded-sm shadow-lg">

            

            <form @submit.prevent="submitForm" id="client-create" class="flex justify-between items-start">

                <div id="left-side" class="w-1/2">

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="first-name">Full Name</label>
                        <div v-if="form.errors.name" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p>{{ form.errors.name }} Hello</p></div>
                        <input v-model="form.name" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text" name="name" id="name">
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="email">Email</label>
                        <div v-if="form.errors.email" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p>{{ form.errors.email }} Hello</p></div>
                        <input v-model="form.email" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="email" name="email" id="email">
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="phone">Phone</label>
                        <div v-if="form.errors.phone" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p>{{ form.errors.phone }} Hello</p></div>
                        <input v-model="form.phone" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="tel" name="phone" id="phone">
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="location">Location</label>
                        <div v-if="form.errors.location" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p>{{ form.errors.location }} Hello</p></div>
                        <input v-model="form.location" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text" name="location" id="location">
                    </div>

                </div>
                <!-- #/left side  -->

                <div id="right-side" class="w-1/2">


                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="Quote">Quote</label>
                        <div v-if="form.errors.quote" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p>{{ form.errors.quote }} Hello</p></div>
                        <input v-model="form.quote" class="block rounded-lg w-3/4 border-gray-400 shadow-gray-200 shadow-md py-2" type="text" name="quote" id="quote">
                    </div>

                    <div class="mb-8">
                        <label class="mb-1 text-xl block" for="website-details">Website Details</label>
                        <div v-if="form.errors.details" class="bg-red-500 text-white w-3/4 py-1 px-4 rounded-sm mb-2"><p>{{ form.errors.details }} Hello</p></div>
                        <textarea v-model="form.details" class="block rounded-lg w-full border-gray-400 shadow-gray-200 shadow-md py-2" rows="10" name="details" id="details"></textarea>
                    </div>

                    <div>
                        <button type="submit" :disabled="form.processing" class="btn btn-safe disabled:bg-green-100">
                            Create Client
                        </button>
                    </div>
                    
                </div>
                <!-- #/right-side  -->
                
                
            </form>





        </div>
        <!-- ./form-container  -->


    </MainLayout>

</template>