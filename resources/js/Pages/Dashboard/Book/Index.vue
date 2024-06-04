<script lang="ts" setup>
import {Head, router} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import {useDateFormat, useUrlSearchParams} from "@vueuse/core";
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";

import Breadcrumb from "primevue/breadcrumb";
import DataTable, {DataTableFilterMeta} from "primevue/datatable";
import Column from "primevue/column";
import Image from "primevue/image";
import Button from "primevue/button";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import Tag from "primevue/tag";
import Paginator from "primevue/paginator";
import Toast from "primevue/toast";
import ProgressBar from "primevue/progressbar";
import Calendar from "primevue/calendar";
import Dropdown from "primevue/dropdown";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";

import {Book, Pagination} from "@/types";
import ConfirmDeleteDialog from "@/Components/ConfirmDeleteDialog.vue";
import {usePaginator, useSearch, useSeverity, useSort} from "@/composables";
import {camelCase, upperCase} from "lodash";
import {useFilter} from "@/composables/use-filter";
import {FilterMatchMode} from "primevue/api";
import {StockStatusList} from "@/common/enums";
import {useExport} from "@/composables/use-export";
import FileImport from "@/Components/FileImport.vue";

const props = defineProps<{
    books: Pagination<Book[]>;
}>();

const toast = useToast();
const confirm = useConfirm();
const params = useUrlSearchParams("history");

const {getStockStatusSeverity} = useSeverity();
const {onFilter, onClear} = useFilter();
const {onSort} = useSort();
const {search, onSearch} = useSearch();
const {paginatorTemplate, paginatorRowsPerPageOptions, onPageChange, first} = usePaginator(props.books);
const {exportProgress, onExport, isExporting} = useExport("dashboard.books.export", "books");

const home = ref({
    label: "Dashboard", route: "dashboard.index", active: false,
});
const breadcrumbItems = ref([
    {label: "Book Lists", route: "dashboard.books.index", active: true}
]);

const selectedBook = ref<Book[]>([]);

const isDeleting = ref(false);

const filters = ref<DataTableFilterMeta>();
const isDisabledClearFilter = computed(() => {
    return Object.values(filters.value!).every(filter => !(<any>filter).value);
});

const setDeleting = (value: boolean) => {
    isDeleting.value = value;
};

const initFilters = () => {
    filters.value = {
        id: {value: params["filter[id]"], matchMode: FilterMatchMode.EQUALS},
        title: {value: params["filter[title]"], matchMode: FilterMatchMode.CONTAINS},
        author: {value: params["filter[author]"], matchMode: FilterMatchMode.CONTAINS},
        genre: {value: params["filter[genre]"], matchMode: FilterMatchMode.CONTAINS},
        publisher: {value: params["filter[publisher]"], matchMode: FilterMatchMode.CONTAINS},
        publication_date: {value: params["filter[publication_date]"], matchMode: FilterMatchMode.EQUALS},
        status: {value: params["filter[status]"], matchMode: FilterMatchMode.EQUALS}
    };
};

initFilters();

const clearFilters = () => {
    filters.value = {};
    initFilters();
    onClear();
};

const deleteBookHandler = (slug: string | string[]) => {
    setDeleting(true);

    const url = route("dashboard.books.destroy", {slugs: slug});
    router.delete(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Deleted",
                detail: "Books has been deleted successfully.",
                life: 3000
            });
            setDeleting(false);
        }
    });
};

const onDeleteBook = (slug: string) => {
    confirm.require({
        group: "confirmDeleteDialog",
        message: "Are you sure you want to delete this book?",
        icon: "pi pi-exclamation-triangle",
        acceptLabel: "Yes, delete it",
        accept: () => deleteBookHandler(slug)
    });
};

const onDeleteSelectedBook = () => {
    const slugs = selectedBook.value.map(book => book.slug);
    confirm.require({
        group: "confirmDeleteDialog",
        message: `Are you sure you want to delete ${selectedBook.value.length} item(s)?`,
        icon: "pi pi-exclamation-triangle",
        acceptLabel: "Yes, delete them",
        accept: () => deleteBookHandler(slugs)
    });
};
</script>

<template>
    <Head title="Dashboard - Book Lists"/>
    <Toast/>
    <ConfirmDeleteDialog/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Book List</h1>
                <Breadcrumb :home="home" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <DataTable v-model:filters="filters" v-model:selection="selectedBook"
                       :lazy="true"
                       :value="books.data" class="max-w-[calc(100vw-3em)]"
                       dataKey="id" filterDisplay="menu" removableSort
                       scrollHeight="450px"
                       scrollable
                       stripedRows
                       @filter="onFilter"
                       @sort="onSort"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-center lg:justify-between">
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <Button :disabled="isDisabledClearFilter" icon="pi pi-filter-slash" outlined
                                    severity="success" @click="clearFilters"/>
                            <Button v-tooltip.bottom="`Delete ${selectedBook.length} item(s)`"
                                    :disabled="selectedBook.length === 0 || isDeleting"
                                    :loading="isDeleting"
                                    icon="pi pi-trash" severity="danger"
                                    @click="onDeleteSelectedBook"
                            />
                            <IconField iconPosition="right">
                                <InputIcon class="pi pi-search cursor-pointer hover:!text-primary-500"/>
                                <InputText v-model="search" class="!bg-surface-0" placeholder="Search here..."
                                           type="text"
                                           @change="onSearch"
                                />
                            </IconField>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <FileImport importer="books" routeName="dashboard.books.import"
                            />
                            <Button :disabled="isExporting" :loading="isExporting" icon="pi pi-download" label="Export"
                                    severity="help" @click="onExport"/>
                            <InertiaLink :href="route('dashboard.books.create')">
                                <Button class="self-end" icon="pi pi-plus" label="Add new"/>
                            </InertiaLink>
                        </div>
                        <ProgressBar :showValue="true" :value="exportProgress" class="w-full"/>
                    </div>
                </template>
                <Column selectionMode="multiple"/>
                <Column :showFilterMatchModes="false" :sortable="true" dataType="numeric" field="id" header="ID">
                    <template #body="{data}">
                        <span>#{{ data?.id }}</span>
                    </template>
                    <template #filter="{filterModel}">
                        <InputText v-model="filterModel.value"
                                   placeholder="Search by id (eg: 1,2,3,...)"/>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" :sortable="true" field="title" header="Title"
                        style="min-width: 10rem;">
                    <template #body="{data}">
                        <h3 class="font-bold line-clamp-2 max-w-60" v-text="data.title"/>
                    </template>
                    <template #filter="{filterModel}">
                        <InputText v-model="filterModel.value"
                                   placeholder="Search by title"/>
                    </template>
                </Column>
                <Column header="Cover" style="min-width: 7rem;">
                    <template #body="{data}">
                        <div class="flex items-center space-x-4">
                            <div class="bg-surface-100 w-20 h-20 flex justify-center items-center rounded px-2 py-4">
                                <Image :alt="`${data.title} cover image`" :src="data.cover_image" preview width="50"/>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" filterField="author" header="Author(s)" style="min-width: 10rem;">
                    <template #body="{data}">
                        <span v-text="`${data.authors}`"/>
                    </template>
                    <template #filter="{filterModel}">
                        <InputText v-model="filterModel.value"
                                   placeholder="Search by author"/>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" filterField="genre" header="Genre(s)" style="min-width: 7rem;">
                    <template #body="{data}">
                        <span v-text="`${data.genres}`"/>
                    </template>
                    <template #filter="{filterModel}">
                        <InputText v-model="filterModel.value"
                                   placeholder="Search by genre"/>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" :sortable="true" field="publisher" header="Publisher"
                        style="min-width: 10rem;">
                    <template #filter="{filterModel}">
                        <InputText v-model="filterModel.value"
                                   placeholder="Search by publisher"/>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" :sortable="true" dataType="date" field="publication_date"
                        header="Publication Date"
                        style="min-width: 10rem;">
                    <template #body="{data}">
                        <span>{{ useDateFormat(data.publication_date, "MMM-DD-YYYY", {locales: "en-US"}).value }}</span>
                    </template>
                    <template #filter="{filterModel}">
                        <Calendar v-model="filterModel.value"
                                  dateFormat="yy-mm-dd"
                                  iconDisplay="input" placeholder="Search by publication date" showIcon/>
                    </template>
                </Column>
                <Column field="original_price" header="Original price" style="min-width: 7rem;"/>
                <Column field="discount_price" header="Discount price" style="min-width: 7rem;"/>
                <Column :showFilterMatchModes="false" filterField="status" header="Status" style="min-width: 7rem;">
                    <template #body="{data}">
                        <Tag :severity="getStockStatusSeverity(data.stock_status)"
                             :value="upperCase(camelCase(data.stock_status))"/>
                    </template>
                    <template #filter="{filterModel}">
                        <Dropdown v-model="filterModel.value" :options="StockStatusList" optionLabel="key"
                                  optionValue="value"
                                  placeholder="Select a status">
                            <template #option="{option}">
                                <Tag :severity="getStockStatusSeverity(option.value)"
                                     :value="upperCase(camelCase(option.value))"/>
                            </template>
                            <template #value="{value, placeholder}">
                                <Tag v-if="value" :severity="getStockStatusSeverity(value)"
                                     :value="upperCase(camelCase(value))"/>
                                <span v-else v-text="placeholder"/>
                            </template>
                        </Dropdown>
                    </template>
                </Column>
                <Column header="Action(s)" style="min-width: 10rem;">
                    <template #body="{data}">
                        <div class="flex flex-wrap gap-2 items-center">
                            <Button icon="pi pi-eye" outlined rounded severity="success"/>
                            <InertiaLink :href="route('dashboard.books.edit', {slug: data.slug})">
                                <Button icon="pi pi-pencil" outlined rounded/>
                            </InertiaLink>
                            <Button :disabled="isDeleting" :loading="isDeleting" icon="pi pi-trash" outlined
                                    rounded
                                    severity="danger"
                                    @click="onDeleteBook(data.slug)"
                            />
                        </div>
                    </template>
                </Column>
                <template #footer>
                    <div class="flex justify-between items-center">
                    <span
                        class="text-xs text-surface-500 font-light">Showing {{
                            books.pagination.count
                        }} entries</span>
                        <Paginator v-model:first="first" :rows="books.pagination.perPage"
                                   :rowsPerPageOptions="paginatorRowsPerPageOptions"
                                   :template="paginatorTemplate" :totalRecords="books.pagination.total"
                                   @page="onPageChange"
                        />
                    </div>
                </template>
                <template #empty>
                    <div class="flex flex-col justify-center items-center gap-2 text-surface-500">
                        <i class="pi pi-info-circle text-info text-4xl"/>
                        <p class="text-center text-lg">No books found.</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
