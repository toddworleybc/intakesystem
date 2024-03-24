<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps(['link', 'type']);


const buttonStatus = computed( () => ({
    'bg-red-600': props.type === 'danger',
    'bg-yellow-600': props.type === 'warning',
    'bg-green-600': props.type === 'safe',
    'bg-blue-600 hover:bg-blue-400': !props.type
}) );



</script>


<template>

    <div>
        <Link :class="buttonStatus" class="py-2 inline-block px-4 text-white rounded-md shadow-lg active:shadow-sm transform active:scale-95 active:bg-gray-500 transition-all" :href="props.link">
            <slot />
        </Link>
    </div>

</template>