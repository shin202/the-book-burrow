<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";

import Breadcrumb from "primevue/breadcrumb";
import Paginator from "primevue/paginator";

import LandingLayout from "@/Layouts/LandingLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";
import BookCard from "@/Components/BookCard.vue";

import {Author, Book, Pagination} from "@/types";
import {usePaginator} from "@/composables";

defineOptions({
    layout: LandingLayout,
});

const props = defineProps<{
    author: Author,
    books: Pagination<Book[]>,
}>();

const {paginatorTemplate, paginatorRowsPerPageOptions, onPageChange, first} = usePaginator(props.books);
</script>

<template>
    <Head :title="author.full_name"/>
    <div class="container mx-auto px-3">
        <div
            class="flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-3 px-6 py-8 bg-primary-300">
            <h1 class="text-4xl md:text-5xl lg:text-8xl font-bold">{{ author.full_name }}</h1>
            <Breadcrumb :home="{label: 'Home', route: 'landing.index', active: false}"
                        :model="[{label: 'Authors', route: 'authors.index', active: false}, {label: author.full_name, active: true}]"
                        class="!bg-primary-300">
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
            <div class="flex flex-col gap-8">
                <div>
                    <div
                        class="float-left m-6 w-60 h-60 rounded-full bg-primary-300 flex justify-center items-center overflow-hidden group"
                        style="shape-outside: circle()">
                        <img v-if="author.avatar" :src="author.avatar" alt="author.full_name"
                             class="w-full h-full object-cover rounded-full group-hover:scale-110 transition-transform duration-300 ease-out"
                             loading="lazy"/>
                        <span v-else class="text-4xl" v-text="author.full_name.charAt(0).toUpperCase()"/>
                    </div>
                    <h2 class="text-3xl font-bold" v-text="author.full_name"/>
                    <p class="text-pretty py-2" v-html="author.biography"/>
                </div>
                <div>
                    <h3 class="font-bold capitalize text-xl py-4">{{ author.full_name }}'s books</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 py-4">
                        <BookCard v-for="book in books.data" :key="book.slug" :book="book"/>
                    </div>
                </div>
                <div class="w-full flex justify-center">
                    <Paginator v-model:first="first" :rows="books.pagination.perPage"
                               :rowsPerPageOptions="paginatorRowsPerPageOptions"
                               :template="paginatorTemplate" :totalRecords="books.pagination.total"
                               class="w-full [&>*]:!bg-primary-200"
                               @page="onPageChange"/>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>
