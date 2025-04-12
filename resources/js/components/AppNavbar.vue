<script setup>
import logoSrc from '@/../images/logo.png';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
</script>

<template>
    <nav class="navbar bg-body-transparent">
        <div class="container-fluid">
            <Link class="navbar-brand" :href="route('home')">
                <img :src="logoSrc" alt="Logo" height="24" class="d-inline-block align-text-top" />
                League Simulator
            </Link>
        </div>
    </nav>
</template>
