<script lang="ts" setup>
import Rating from "primevue/rating";
import {Book} from "@/types";
import {useCart} from "@/composables";
import InertiaLink from "@/Components/InertiaLink.vue";

defineProps<{
    book: Book
}>();

const {addToCart} = useCart();
</script>

<template>
    <div class="flex flex-col">
        <div
            class="h-56 px-4 py-3 rounded-xl bg-primary-300 relative cursor-pointer overflow-hidden group">
            <InertiaLink :href="route('books.show', {slug: book.slug})">
                <img :src="book.cover_image" alt="Book cover"
                     class="h-full w-40 object-contain rounded-xl mx-auto group-hover:scale-110 transition-transform duration-300 ease-out"
                     loading="lazy"/>
            </InertiaLink>
            <div class="absolute top-1/2 right-4 -translate-y-1/2 flex flex-col gap-1">
                <button
                    v-tooltip.top="'Add to wishlist'"
                    class="min-w-10 aspect-square bg-surface-0 text-surface-900 px-1.5 py-2 rounded-full flex justify-center items-center hover:bg-primary hover:text-surface-0 translate-x-[calc(100%+16px)] opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300 ease-in-out delay-75">
                    <i class="pi pi-heart"/>
                </button>
                <button
                    v-tooltip.top="'Quick view'"
                    class="min-w-10 aspect-square bg-surface-0 text-surface-900 px-1.5 py-2 rounded-full flex justify-center items-center hover:bg-primary hover:text-surface-0 translate-x-[calc(100%+16px)] opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300 ease-in-out delay-100">
                    <i class="pi pi-eye"/>
                </button>
                <button
                    v-tooltip.top="'Add to cart'"
                    class="min-w-10 aspect-square bg-surface-0 text-surface-900 px-1.5 py-2 rounded-full flex justify-center items-center hover:bg-primary hover:text-surface-0 translate-x-[calc(100%+16px)] opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300 ease-in-out delay-150"
                    @click="addToCart(book.id)">
                    <i class="pi pi-shopping-cart"/>
                </button>
            </div>
        </div>
        <div class="py-4 flex-1">
            <h3 class="font-bold mb-2 text-surface-900 cursor-pointer hover:text-primary-800 font-serif line-clamp-2">
                <InertiaLink :href="route('books.show', book.slug)" v-text="book.title"/>
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
                <span v-text="book.average_rating"/>
            </div>
            <p class="text-sm text-surface-500 mb-2 hover:text-primary-800">
                <InertiaLink :href="route('authors.show', {slug: book.authors?.[0].slug})"
                             v-text="book.authors?.[0].full_name"/>
            </p>
            <div>
                <div v-if="book.has_discount">
                    <span class="text-xl font-bold text-primary-900"
                          v-text="book.discount_price"/>
                    <span class="text-sm text-surface-500 line-through pl-2"
                          v-text="book.original_price"/>
                </div>
                <span v-else class="text-xl font-bold text-primary-900" v-text="book.original_price"/>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>
