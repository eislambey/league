<script setup>
defineProps({
    kind: {
        type: String,
        default: 'btn-outline-primary',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    onClick: {
        type: Function,
        default: () => {},
    },
    loading: {
        type: Boolean,
        default: false,
    },
    tooltip: {
        type: String,
        default: '',
    },
});

defineEmits(['click']);
</script>

<template>
    <div v-tooltip data-bs-placement="top" class="d-inline-block me-3" :title="tooltip">
        <button class="btn" :class="kind" type="button" @click="$emit('click')" :disabled="disabled">
            <slot />
        </button>
    </div>
</template>
