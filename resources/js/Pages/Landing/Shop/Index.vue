<script lang="ts">
import LandingLayout from "@/Layouts/LandingLayout.vue";

export default {
    layout: LandingLayout
};
</script>
<script lang="ts" setup>
import {reactive, ref} from "vue";
import {Head, router} from "@inertiajs/vue3";

import Breadcrumb from "primevue/breadcrumb";
import DataView from "primevue/dataview";
import DataViewLayoutOptions from "primevue/dataviewlayoutoptions";
import Button from "primevue/button";
import Dropdown from "primevue/dropdown";
import Paginator from "primevue/paginator";
import Sidebar from "primevue/sidebar";
import Checkbox from "primevue/checkbox";
import Slider from "primevue/slider";

import InertiaLink from "@/Components/InertiaLink.vue";
import BookCard from "@/Components/BookCard.vue";

import {Author, Book, Genre, Pagination} from "@/types";
import {useCart, usePaginator} from "@/composables";
import {useUrlSearchParams} from "@vueuse/core";

const props = defineProps<{
    books: Pagination<Book[]>,
    genres: Pagination<Genre[]>,
    authors: Pagination<Author[]>,
}>();

const params = useUrlSearchParams("history");
const {paginatorRowsPerPageOptions, paginatorTemplate, onPageChange, first} = usePaginator(props.books);
const {addToCart} = useCart();

const layout = ref<any>("grid");

const isShowFilterMenu = ref(false);
const filters = reactive({
    genres: params["filter[genre]"] ? (<string>params["filter[genre]"])?.split(",") : [],
    authors: params["filter[author]"] ? (<string>params["filter[author]"])?.split(",") : [],
    price: params["filter[price]"] ? (<string>params["filter[price]"])?.split(",").map(Number) : [10, 1000],
    ratings: params["filter[rating]"] ? (<string>params["filter[rating]"])?.split(",").map(Number) : [],
});

const sortOptions = [
    {label: "Newest", value: "-publication_date"},
    {label: "Oldest", value: "publication_date"},
    {label: "Price: Low to High", value: "price"},
    {label: "Price: High to Low", value: "-price"},
    {label: "Rating: Low to High", value: "rating"},
    {label: "Rating: High to Low", value: "-rating"},
];
const sortBy = ref(params.sort || "");

const onOpenFilterMenu = () => {
    isShowFilterMenu.value = true;
};

const onFilter = () => {
    const url = route(route().current()!);

    const params = {
        ...route().params,
        filter: {
            genre: filters.genres.join(","),
            author: filters.authors.join(","),
            price: filters.price.join(","),
            rating: filters.ratings.join(","),
        }
    };

    router.get(url, params);
};

const onSort = () => {
    const url = route(route().current()!);

    const params = {
        ...route().params,
        sort: sortBy.value
    };

    router.get(url, params, {
        preserveState: true,
    });
};
</script>

<template>
    <Head title="Shop"/>
    <div class="container px-3 lg:px-6 mx-auto mt-4">
        <div
            class="flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-3 px-6 py-8 bg-primary-300">
            <div class="text-4xl md:text-5xl lg:text-8xl font-bold">Shop</div>
            <Breadcrumb :home="{label: 'Home', route: 'landing.index', active: false}"
                        :model="[{label: 'Shop', route: 'shop.index', active: true}]"
                        class="!bg-primary-300"
            >
                <template #item="{item}">
                    <InertiaLink v-if="item.route" :class="{'text-primary-800 pointer-events-none': item.active}"
                                 :href="route(item.route)"
                                 class="uppercase" v-text="item.label"/>
                    <span v-else :class="{'text-primary-800 pointer-events-none': item.active}" class="uppercase"
                          v-text="item.label"/>
                </template>
            </Breadcrumb>
        </div>
        <div class="mt-14">
            <DataView :layout="layout" :value="books.data" dataKey="slug">
                <template #header>
                    <div class="flex flex-wrap items-center justify-end gap-4">
                        <div>
                            <Button class="ml-auto" icon="pi pi-filter" label="Filter"
                                    severity="contrast"
                                    text
                                    @click="onOpenFilterMenu"/>
                            <Sidebar v-model:visible="isShowFilterMenu">
                                <template #container="{closeCallback}">
                                    <div class="px-4 py-2 flex flex-col divide-y overflow-y-auto">
                                        <div
                                            class="sticky top-0 z-[10] bg-primary-100 w-full text-sm flex items-center justify-between gap-2 text-medium py-2">
                                            <Button class="w-fit" icon="pi pi-filter" label="Filter" severity="contrast"
                                                    text @click="onFilter"/>
                                            <div class="flex items-center gap-2 cursor-pointer" @click="closeCallback">
                                                <span class="uppercase">Hide filter</span>
                                                <i class="pi pi-times"/>
                                            </div>
                                        </div>
                                        <div class="py-2 flex flex-col divide-y gap-4">
                                            <div class="flex flex-col divide-y">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold py-4">Genre</span>
                                                    <span>View all</span>
                                                </div>
                                                <div class="flex flex-col gap-2 py-2">
                                                    <div v-for="genre in genres.data" :key="genre.slug"
                                                         class="flex items-center gap-2 py-1 w-fit hover:text-primary-800">
                                                        <Checkbox v-model="filters.genres" :inputId="genre.slug"
                                                                  :value="genre.slug"/>
                                                        <label :for="genre.slug" class="cursor-pointer"
                                                               v-text="genre.name"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col divide-y">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold py-4">Author</span>
                                                    <span>View all</span>
                                                </div>
                                                <div class="flex flex-col gap-2 py-2">
                                                    <div v-for="author in authors.data" :key="author.slug"
                                                         class="flex items-center gap-2 py-1 w-fit hover:text-primary-800">
                                                        <Checkbox v-model="filters.authors" :inputId="author.slug"
                                                                  :value="author.slug"/>
                                                        <label :for="author.slug" class="cursor-pointer"
                                                               v-text="author.full_name"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col divide-y">
                                                <span class="font-bold py-4">Filter by price</span>
                                                <div class="flex flex-col gap-2 py-8">
                                                    <Slider v-model="filters.price" :max="1000" :min="10" :range="true"
                                                            :step="10"
                                                            :style="{width: '100%'}"/>
                                                    <span class="text-sm"><span class="text-surface-500">Price</span>: ${{
                                                            filters.price[0]
                                                        }} - ${{ filters.price[1] }}</span>
                                                </div>
                                            </div>
                                            <div class="flex flex-col divide-y">
                                                <span class="font-bold py-2">Rating</span>
                                                <div class="flex flex-col gap-2 py-2">
                                                    <div v-for="i in 5" :key="i" class="flex items-center gap-2 py-1">
                                                        <Checkbox v-model="filters.ratings" :inputId="`rating-${i}`"
                                                                  :value="i"/>
                                                        <label :for="`rating-${i}`" class="cursor-pointer">
                                                            {{ i }} <i v-for="j in i" :key="`${i}-${j}`"
                                                                       class="pi pi-star-fill text-orange-400"/>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Sidebar>
                        </div>
                        <Dropdown v-model="sortBy" :options="sortOptions" class="text-sm" optionLabel="label"
                                  optionValue="value"
                                  placeholder="Default sorting"
                                  @update:modelValue="onSort"/>
                        <DataViewLayoutOptions v-model="layout" class="hidden md:block"/>
                    </div>
                </template>
                <template #grid="{items}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 md:gap-x-8 divide-y px-4 bg-primary-200">
                        <BookCard v-for="book in items" :key="book.slug" :book="book"
                                  class="!flex-row lg:!flex-col py-6 gap-8"/>
                    </div>
                </template>
                <template #list="{items}">
                    <div class="grid grid-cols-1 md:gap-x-8 divide-y px-4 bg-primary-200">
                        <div v-for="book in items" :key="book.slug" class="flex flex-wrap justify-between items-center">
                            <BookCard :book="book"
                                      class="!flex-row py-6 gap-8"/>
                            <div class="hidden md:flex flex-wrap items-center gap-4">
                                <Button class="!rounded-full px-8 py-4 !bg-primary-800" icon="pi pi-shopping-cart"
                                        label="Add to cart" @click="addToCart(book.id)"/>
                                <Button icon="pi pi-heart" label="Add to wishlist" severity="secondary" text/>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <div class="flex justify-between items-center w-full">
                        <Paginator v-model:first="first" :rows="books.pagination.perPage"
                                   :rowsPerPageOptions="paginatorRowsPerPageOptions"
                                   :template="paginatorTemplate"
                                   :totalRecords="books.pagination.total"
                                   class="w-full [&>*]:!bg-primary-200"
                                   @page="onPageChange"
                        />
                    </div>
                </template>
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-6 gap-2 bg-primary-200">
                        <i class="pi pi-info-circle text-5xl text-surface-500"/>
                        <p class="text-2xl">
                            Looks like we don't have any items matching your search
                        </p>
                    </div>
                </template>
            </DataView>
        </div>
    </div>
</template>
