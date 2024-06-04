<script lang="ts" setup>
import {Banner} from "@/types";
import InertiaLink from "@/Components/InertiaLink.vue";

defineProps<{
    banner: Banner
}>();
</script>

<template>
    <div class="cta__item group">
        <div class="cta__bg group-hover:scale-110">
            <img :alt="banner.title" :src="banner.image"/>
        </div>
        <div class="cta__content drop-shadow-lg backdrop-blur-sm px-2 py-4 rounded">
            <div
                class="line-clamp-1 text-md font-bold capitalize mb-2 relative before:absolute before:h-0.5 before:bottom-0 before:left-0 before:right-0 before:bg-surface-900 before:scale-x-0 group-hover:before:scale-x-100 before:transition-transform before:origin-left before:duration-300 before:ease-out">
                <span v-text="banner.title"/>
            </div>
            <h3 class="text-3xl font-bold capitalize mb-8 line-clamp-3" v-text="banner.description"/>
            <div>
                <button
                    class="rounded-full uppercase font-bold flex justify-center items-center gap-4 hover:text-primary">
                    <InertiaLink :href="banner.link">Shop now</InertiaLink>
                    <i class="pi pi-chevron-right text-xs"/>
                </button>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.cta {
    &__item {
        @apply h-fit max-h-96 bg-primary-300 rounded-2xl bg-cover bg-center bg-no-repeat p-8 relative overflow-hidden;
    }

    &__bg {
        @apply absolute inset-0 bg-primary-300 rounded-2xl bg-cover bg-center bg-no-repeat transition-transform duration-300 ease-out;

        > img {
            @apply w-full h-full object-contain;
        }
    }

    &__content {
        @apply relative max-w-60;
    }
}
</style>
