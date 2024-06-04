<script lang="ts" setup>
import CallToActionCard from "@/Components/CallToActionCard.vue";
import {Banner} from "@/types";

defineProps<{
    banners: Banner[]
}>();
</script>

<template>
    <swiper-container :autoplay="{delay: 2000}" :slidesPerView="1" :spaceBetween="20">
        <swiper-slide v-for="banner in banners" :key="banner.id">
            <CallToActionCard :banner="banner" class="!h-96"/>
        </swiper-slide>
    </swiper-container>
</template>

<style lang="scss" scoped>

</style>
