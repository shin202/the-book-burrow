<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";

import Breadcrumb from "primevue/breadcrumb";
import DataView from "primevue/dataview";
import Dropdown from "primevue/dropdown";

import LandingLayout from "@/Layouts/LandingLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";

import {Author, Pagination} from "@/types";
import Paginator from "primevue/paginator";
import {usePaginator} from "@/composables";

defineOptions({
    layout: LandingLayout,
});

const props = defineProps<{
    authors: Pagination<Author[]>
}>();

const {paginatorRowsPerPageOptions, paginatorTemplate, onPageChange, first} = usePaginator(props.authors);
</script>

<template>
    <Head title="Authors"/>
    <div class="container mx-auto px-3">
        <div
            class="flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-3 px-6 py-8 bg-primary-300">
            <div class="text-4xl md:text-5xl lg:text-8xl font-bold">Authors</div>
            <Breadcrumb :home="{label: 'Home', route: 'landing.index', active: false}"
                        :model="[{label: 'Authors', route: 'authors.index', active: true}]" class="!bg-primary-300">
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
            <DataView :value="authors.data" dataKey="slug" layout="grid">
                <template #header>
                    <div class="flex flex-wrap items-center justify-end gap-4">
                        <Dropdown class="text-sm" optionLabel="label" optionValue="value"
                                  placeholder="Default sorting"/>
                    </div>
                </template>
                <template #grid="{items}">
                    <div class="grid grid-cols-2 lg:grid-cols-5 divide-y px-4 bg-primary-200">
                        <div v-for="author in items" :key="author.slug"
                             class="flex flex-col justify-center items-center gap-4 py-6">
                            <InertiaLink :href="route('authors.show', author.slug)">
                                <div
                                    class="w-40 h-40 rounded-full bg-primary-300 flex justify-center items-center overflow-hidden group">
                                    <img v-if="author.avatar" :src="author.avatar" alt="author.full_name"
                                         class="w-full h-full object-cover rounded-full group-hover:scale-110 transition-transform duration-300 ease-out"
                                         loading="lazy"/>
                                    <span v-else class="text-4xl" v-text="author.full_name.charAt(0).toUpperCase()"/>
                                </div>
                                <span v-text="author.full_name"/>
                            </InertiaLink>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <div class="flex justify-between items-center w-full">
                        <Paginator v-model:first="first" :rows="authors.pagination.perPage"
                                   :rowsPerPageOptions="paginatorRowsPerPageOptions"
                                   :template="paginatorTemplate"
                                   :totalRecords="authors.pagination.total"
                                   class="w-full [&>*]:!bg-primary-200"
                                   @page="onPageChange"
                        />
                    </div>
                </template>
            </DataView>
        </div>
    </div>
</template>
