<script lang="ts">
import LandingLayout from "@/Layouts/LandingLayout.vue";

export default {
    layout: LandingLayout,
};
</script>

<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";
import {computed, onBeforeMount, ref} from "vue";
import {register} from "swiper/element/bundle";

import Rating from "primevue/rating";
import Button from "primevue/button";

import BookCard from "@/Components/BookCard.vue";
import CallToActionCard from "@/Components/CallToActionCard.vue";

import {Banner, Book, Pagination as Paginate} from "@/types";
import Countdown from "@/Components/Countdown.vue";
import BannerCarousel from "@/Components/BannerCarousel.vue";
import InertiaLink from "@/Components/InertiaLink.vue";


defineProps<{
    banners: Paginate<Banner[]>;
    popularBooks: Paginate<Book[]>,
    topRatedBooks: Paginate<Book[]>,
    top10ThisWeek: Book[],
    bestSellerBooks: Paginate<Book[]>,
    recentlyAddedBooks: Paginate<Book[]>,
    discountBooks: Paginate<Book[]>,
    dealsOfWeek: Paginate<Book[]>,
    recommendedBooks: Paginate<Book[]>,
}>();

const currentTab = ref("popular");

const changeTab = (tab: string) => {
    currentTab.value = tab;
};

const colors = [
    "bg-green-300",
    "bg-blue-300",
    "bg-yellow-300",
    "bg-red-300",
    "bg-purple-300",
    "bg-pink-300",
    "bg-indigo-300",
    "bg-cyan-300",
    "bg-teal-300",
    "bg-lime-300",
];

onBeforeMount(() => {
    register();
});
</script>

<template>
    <Head title="Home"/>
    <div class="container mx-auto mt-4 px-6">
        <BannerCarousel :banners="banners.data"/>
        <div class="mt-12">
            <!-- Popular section (Mobile) -->
            <div class="flex flex-col lg:hidden">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-xl font-bold capitalize">Popular books</h2>
                    <Button label="View all"/>
                </div>
                <div
                    class="grid grid-cols-2 md:grid-cols-3 gap-4 place-items-center mt-8"
                >
                    <BookCard v-for="book in popularBooks.data" :key="book.id" :book="book"/>
                </div>
            </div>
            <!-- Popular section (Mobile) -->
            <div class="flex">
                <!-- Top 10 This Week section (Desktop - 1280px) -->
                <div class="hidden xl:flex flex-col gap-6 items-center">
                    <div
                        class="p-8 flex-col divide-y bg-primary-300 rounded-2xl flex w-full h-fit"
                    >
                        <h2 class="capitalize font-bold text-xl py-4">
                            Top 10 This Week
                        </h2>
                        <div v-for="(book, i) in top10ThisWeek" :key="book.slug" class="flex flex-col py-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="h-28 w-20 rounded-3xl bg-primary-400 relative"
                                >
                                    <div
                                        :class="{ [colors[i]]: true }"
                                        class="w-8 h-8 rounded-full font-bold flex justify-center items-center border-2 border-surface-0 text-sm absolute -top-2 -left-2"
                                    >
                                        <span>{{ i + 1 }}</span>
                                    </div>
                                    <div class="w-20 h-full">
                                        <img :alt="book.title" :src="book.cover_image"
                                             class="w-full h-full object-cover rounded-3xl"/>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm font-serif hover:text-primary-800">
                                        <InertiaLink :href="route('books.show', book.slug)">{{
                                                book.title
                                            }}
                                        </InertiaLink>
                                    </h3>
                                    <span class="text-xs text-surface-500">
                                    <a href="#">{{ book.author }}</a>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <CallToActionCard :banner="banners.data[0]" class="!h-full"/>
                </div>
                <!-- Top 10 This Week section (Desktop - 1280px) -->
                <div class="flex-1">
                    <div class="tab-container flex-1 xl:pl-8">
                        <div class="tab-header">
                            <div class="flex space-x-6">
                                <div @click="changeTab('popular')">
                                    <h2
                                        :class="{
                                            active: currentTab === 'popular',
                                        }"
                                        class="tab-header__item"
                                    >
                                        Popular Books
                                    </h2>
                                </div>
                                <div @click="changeTab('onSale')">
                                    <h2
                                        :class="{
                                            active: currentTab === 'onSale',
                                        }"
                                        class="tab-header__item"
                                    >
                                        On Sale
                                    </h2>
                                </div>
                                <div @click="changeTab('topRated')">
                                    <h2
                                        :class="{
                                            active: currentTab === 'topRated',
                                        }"
                                        class="tab-header__item"
                                    >
                                        Top Rated
                                    </h2>
                                </div>
                            </div>
                            <div
                                class="h-0.5 bg-surface-200 flex-1 mx-24"
                            ></div>
                            <div class="">
                                <InertiaLink :href="route('shop.index')">
                                    <Button class="px-6 py-3 !rounded-full w-full" icon="pi pi-chevron-right"
                                            iconPos="right"
                                            label="View all"/>
                                </InertiaLink>
                            </div>
                        </div>
                        <div class="tab-content">
                            <TransitionGroup name="fade" tag="div">
                                <div
                                    v-if="currentTab === 'popular'"
                                    class="grid grid-cols-2 lg:grid-cols-5 gap-8"
                                >
                                    <BookCard v-for="book in popularBooks.data" :key="book.id" :book="book"/>
                                </div>
                                <div
                                    v-if="currentTab === 'onSale'"
                                    class="grid grid-cols-2 lg:grid-cols-5 gap-8"
                                >
                                    <BookCard v-for="book in discountBooks.data" :key="book.id" :book="book"/>
                                </div>
                                <div
                                    v-if="currentTab === 'topRated'"
                                    class="grid grid-cols-2 lg:grid-cols-5 gap-8"
                                >
                                    <BookCard v-for="book in topRatedBooks.data" :key="book.id" :book="book"/>
                                </div>
                            </TransitionGroup>
                        </div>
                    </div>
                    <!-- Call to action section -->
                    <div
                        class="mt-10 flex flex-wrap flex-col lg:flex-row gap-6 xl:pl-8"
                    >
                        <CallToActionCard v-for="i in 2" :key="i" :banner="banners.data[i]"
                                          class="flex-1 odd:-skew-y-3 even:skew-y-2"/>
                    </div>

                    <!-- Deal section -->
                    <div class="flex flex-col mt-10 xl:pl-8">
                        <h2 class="text-3xl font-bold capitalize">
                            Deals of the week
                        </h2>
                        <div
                            class="mt-8 w-[calc(100vw-3em)] sm:w-[592px] md:w-[720px] xl:w-[calc(100vw-24em)] lg:w-[976px]">
                            <swiper-container :autoplay="{delay: 2000}"
                                              :breakpoints="{768: {slidesPerView: 2}, 1024: {slidesPerView: 3}}"
                                              :grabCursor="true"
                                              :loop="true"
                                              :slidesPerView="1"
                                              :spaceBetween="20"
                                              pagination="true">
                                <swiper-slide v-for="book in dealsOfWeek.data" :key="book.slug"
                                              class="flex flex-col gap-4 mb-8">
                                    <div
                                        class="w-full h-[32rem] bg-primary-300 rounded-xl overflow-hidden group">
                                        <InertiaLink :href="route('books.show', book.slug)">
                                            <img :alt="book.title" :src="book.cover_image"
                                                 class="w-full h-full object-cover rounded-xl transition-transform duration-300 ease-out group-hover:scale-110"
                                                 loading="lazy"/>
                                        </InertiaLink>
                                    </div>
                                    <div>
                                        <div class="">
                                            <h3 class="font-bold mb-2 text-surface-900 cursor-pointer hover:text-primary-800 font-serif">
                                                <InertiaLink :href="route('books.show', book.slug)">{{
                                                        book.title
                                                    }}
                                                </InertiaLink>
                                            </h3>
                                            <div class="flex items-center gap-2 text-sm mb-2.5">
                                                <Rating :cancel="false" :modelValue="book.average_rating">
                                                    <template #officon>
                                                        <i class="pi pi-star text-surface-400"/>
                                                    </template>
                                                    <template #onicon>
                                                        <i class="pi pi-star-fill text-orange-400"/>
                                                    </template>
                                                </Rating>
                                                <span>{{ book.average_rating }}</span>
                                            </div>
                                            <p class="text-sm text-surface-500 mb-2 hover:text-primary-800">
                                                <a href="#">{{ book.authors?.[0].full_name }}</a>
                                            </p>
                                            <div>
                                                <div v-if="book.has_discount">
                                                    <span class="text-xl font-bold text-primary-900"
                                                          v-text="book.discount_price"/>
                                                    <span class="text-sm text-surface-500 line-through pl-2"
                                                          v-text="book.original_price"/>
                                                </div>
                                                <span v-else class="text-xl font-bold text-primary-900"
                                                      v-text="book.original_price"/>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <span class="text-sm mb-2 block">Hurry up! Sale ends in:</span>
                                            <Countdown :from="book.sale_from" :to="book.sale_to"/>
                                        </div>
                                    </div>
                                </swiper-slide>
                            </swiper-container>
                        </div>
                    </div>
                    <!-- Deal section -->

                    <!-- Call to action section -->

                    <!-- Top 10 This Week section (Mobile) -->
                    <div
                        class="flex xl:hidden flex-col divide-y rounded-2xl w-full h-fit mt-10"
                    >
                        <h2 class="capitalize font-bold text-xl py-4">
                            Top 10 This Week
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                            <div v-for="i in 10" :key="i"
                                 class="flex flex-col py-4 border border-primary-300 rounded-lg mt-4 px-2">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="h-28 w-20 rounded-3xl bg-primary-400 relative"
                                    >
                                        <div
                                            :class="{ [colors[i - 1]]: true }"
                                            class="w-8 h-8 rounded-full font-bold flex justify-center items-center border-2 border-surface-0 text-sm absolute -top-2 -left-2"
                                        >
                                            <span>{{ i }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-sm font-serif line-clamp-3">
                                            <a href="#">Sample title</a>
                                        </h3>
                                        <span class="text-xs text-surface-500">
                                    <a href="#">Author name</a>
                                </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Top 10 This Week section (Mobile) -->
                </div>
            </div>

            <!-- Recommended section -->
            <div v-if="recommendedBooks.data.length > 0" class="flex flex-col mt-10">
                <div class="flex justify-between items-center gap-4">
                    <h2 class="text-xl font-bold capitalize py-4">Recommended for you</h2>
                    <div
                        class="h-0.5 bg-surface-200 flex-1 mx-24"
                    ></div>
                    <div class="">
                        <Button class="px-6 py-3 !rounded-full w-full" icon="pi pi-chevron-right"
                                iconPos="right"
                                label="View all"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 md:gap-x-8">
                    <BookCard v-for="book in recommendedBooks.data" :key="book.id" :book="book"
                              class="!flex-row lg:!flex-col py-6 gap-8"/>
                </div>
            </div>
            <!-- Recommended section -->

            <div class="mt-10 flex flex-col md:flex-row items-center md:gap-16">
                <div class="flex flex-col divide-y flex-1">
                    <h2 class="text-xl font-bold capitalize py-4">Top Selling</h2>
                    <div class="flex flex-col py-4 divide-y">
                        <BookCard v-for="book in bestSellerBooks.data" :key="book.id" :book="book"
                                  class="!flex-row py-6 gap-8"/>
                    </div>
                </div>

                <div class="flex flex-col divide-y flex-1">
                    <h2 class="text-xl font-bold capitalize py-4">Recently Added</h2>
                    <div class="flex flex-col py-4 divide-y">
                        <BookCard v-for="book in recentlyAddedBooks.data" :key="book.id" :book="book"
                                  class="!flex-row py-6 gap-8"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style lang="scss">
.tab > div {
    @apply pr-0;
}

.tab {
    &-container {
        @apply hidden lg:flex lg:flex-col lg:space-y-8;
    }

    &-header {
        @apply flex justify-between items-center w-full;

        &__item {
            @apply capitalize text-xl py-2 px-6 w-full font-bold text-surface-400 relative before:absolute before:w-full before:h-1 before:bg-primary-800 before:bottom-0 before:left-0 before:scale-x-0 before:hover:scale-x-100 before:transition-transform before:duration-200 before:ease-out cursor-pointer hover:text-surface-900 transition-colors duration-200 ease-out;

            &.active {
                @apply text-surface-900 before:scale-x-100;
            }
        }
    }
}
</style>
