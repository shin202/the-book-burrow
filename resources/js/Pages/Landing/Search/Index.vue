<script lang="ts" setup>
import {ref} from "vue";
import {Head, router} from "@inertiajs/vue3";
import {useUrlSearchParams} from "@vueuse/core";

import Breadcrumb from "primevue/breadcrumb";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import InputGroup from "primevue/inputgroup";
import Dropdown from "primevue/dropdown";
import Paginator from "primevue/paginator";
import DataView from "primevue/dataview";

import LandingLayout from "@/Layouts/LandingLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";
import BookCard from "@/Components/BookCard.vue";

import {Book, Pagination} from "@/types";
import {usePaginator} from "@/composables";

defineOptions({
    layout: LandingLayout,
});

const props = defineProps<{
    books: Pagination<Book[]>,
    execution_time: number,
}>();

const params = useUrlSearchParams("history");
const {paginatorRowsPerPageOptions, paginatorTemplate, onPageChange, first} = usePaginator(props.books);

const searchOptions = [
    {label: "All", value: "all"},
    {label: "Title", value: "title"},
    {label: "Author", value: "author"},
];
const searchField = ref(params.searchField || "all");

const query = ref(<string>params.query);

const onSearch = () => {
    const url = route(route().current()!);

    router.get(url, {
        ...route().params,
        searchField: searchField.value,
        query: query.value,
    }, {
        preserveState: true
    });
};
</script>

<template>
    <Head title="Search"/>
    <div class="container mx-auto px-3">
        <div
            class="flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-3 px-6 py-8 bg-primary-300">
            <div class="text-4xl md:text-5xl lg:text-8xl font-bold">Search</div>
            <Breadcrumb :home="{label: 'Home', route: 'landing.index', active: false}"
                        :model="[{label: 'Search', route: 'authors.index', active: true}]" class="!bg-primary-300">
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
            <div class="flex flex-col gap-4">
                <InputGroup>
                    <Dropdown v-model="searchField" :options="searchOptions" optionLabel="label" optionValue="value"
                              placeholder="Search by"/>
                    <InputText v-model="query" inputmode="text"
                               placeholder="Search" @change="onSearch"/>
                    <Button icon="pi pi-search" @click="onSearch"/>
                </InputGroup>
                <p v-if="books.data.length > 0" class="font-medium">
                    Found {{ books.pagination.total }} results in {{ execution_time }} seconds
                </p>
            </div>
            <DataView v-if="books.data.length > 0" :value="books.data" class="mt-10" dataKey="slug" layout="grid">
                <template #grid="{items}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 md:gap-x-8 px-4 bg-primary-200">
                        <BookCard v-for="book in items" :key="book.slug" :book="book"
                                  class="!flex-row lg:!flex-col py-6 gap-8"/>
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
            <div v-else class="flex flex-col items-center justify-center py-6 gap-2 bg-primary-200">
                <i class="pi pi-info-circle text-5xl text-surface-500"/>
                <p class="text-2xl">
                    Looks like we don't have any items matching your search
                </p>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>
