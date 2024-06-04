<script lang="ts" setup>
import InertiaLink from "@/Components/InertiaLink.vue";
import {useSetting} from "@/composables";

const {setting} = useSetting();
</script>

<template>
    <div class="main-logo">
        <img :src="setting('site.logo')" alt="The Book Burrow Logo" class="w-8 h-8"/>
        <InertiaLink :href="route('landing.index')" class="mt-auto pl-2">
            <span v-text="setting('site.title')"/>
        </InertiaLink>
    </div>
</template>

<style lang="scss" scoped>
.main-logo {
    @apply font-semibold text-3xl text-surface-900 dark:text-surface-400 tracking-tighter font-serif flex items-center justify-center;
}
</style>
