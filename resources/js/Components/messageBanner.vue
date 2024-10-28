<script setup>
    import { computed, onMounted } from 'vue';
    import { CheckCircleIcon } from '@heroicons/vue/24/outline';
    import messageBannerControl from '@/Utilities/messageBannerControl';
    import { usePage } from '@inertiajs/vue3';
    


    const openBanner =  computed( () => {
        return messageBannerControl.status !== null ? true : false;
    } );
    

    const bannerType = computed( () => ({
        'bg-green-500' : messageBannerControl.type === 'safe',
        'bg-red-500' : messageBannerControl.type === 'danger',
        'bg-blue-500' : messageBannerControl.type === 'info',
        'bg-yellow-500' : messageBannerControl.type === 'warning'
    }) );


    function closeBanner() {

        messageBannerControl.status = null;
        messageBannerControl.type = null;
        messageBannerControl.message = null;

    }

    onMounted( () => {
        if(usePage().props.flash.message === null) closeBanner(); 
    } );

   
</script>
<template>

    <div v-if="openBanner" class="flex justify-center items-center space-x-4 mb-4">
               
        <div id="message-banner" :class="bannerType" class="flex item-center space-x-2 text-white justify-center py-2 w-1/2 rounded-lg">

            <div>
                <p class="mb-0">{{ messageBannerControl.message }}</p>
            </div>
            
            
            <CheckCircleIcon v-if="messageBannerControl.type !== 'danger'" class="w-5 h-5 mt-0.5"/>
            
        </div>
            <!-- #/message-banner  -->
        <div @click.prevent="closeBanner" class="bg-red-500 text-white rounded-full px-2 cursor-pointer hover:bg-red-800 active:bg-yellow-400 shadow-sm">
            X
        </div>
    </div>
    <!-- @/client_created  -->

</template>
<style>
</style>