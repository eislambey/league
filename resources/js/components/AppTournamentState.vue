<script setup>
import { computed } from 'vue';

const { state } = defineProps({
    state: {
        type: String,
        required: true,
    },
});

const className = computed(() => {
    switch (state) {
        case 'created':
            return 'bg-secondary';
        case 'started':
            return 'bg-danger';
        case 'finished':
            return 'bg-success';
        default:
            return '';
    }
});
</script>

<template>
    <span class="badge" :class="className">
        {{ state.toUpperCase() }}
    </span>
</template>
