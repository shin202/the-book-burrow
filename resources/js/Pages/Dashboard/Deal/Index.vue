<script lang="ts" setup>
import {Head, router} from "@inertiajs/vue3";
import {computed, onBeforeMount, ref} from "vue";

import Breadcrumb from "primevue/breadcrumb";
import DataTable, {DataTableFilterMeta} from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";
import Button from "primevue/button";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import ProgressBar from "primevue/progressbar";
import Paginator from "primevue/paginator";
import InputNumber from "primevue/inputnumber";
import Dropdown from "primevue/dropdown";
import Calendar from "primevue/calendar";
import MultiSelect from "primevue/multiselect";
import Toast from "primevue/toast";
import {useToast} from "primevue/usetoast";
import {FilterMatchMode, FilterOperator} from "primevue/api";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";

import {Book, Deal, Pagination} from "@/types";
import {usePaginator, useSearch, useSeverity} from "@/composables";
import {useFilter} from "@/composables/use-filter";
import {DiscountTypeList} from "@/common/enums";
import {useConfirm} from "primevue/useconfirm";
import ConfirmDeleteDialog from "@/Components/ConfirmDeleteDialog.vue";

const props = defineProps<{
    deals: Pagination<Deal[]>,
    books: Pagination<Book[]>
}>();

const toast = useToast();
const confirm = useConfirm();

const {getDiscountSeverity} = useSeverity();
const {first, paginatorTemplate, paginatorRowsPerPageOptions, onPageChange} = usePaginator(props.deals);
const {search, onSearch} = useSearch();
const {onClear, onFilter} = useFilter();

const formattedDeals = computed(() => {
    return props.deals.data.map(deal => {
        return {
            ...deal,
            start_date: window.dayjs(deal.start_date).format("MMM-DD-YYYY hh:mm:ss a"),
            end_date: window.dayjs(deal.end_date).format("MMM-DD-YYYY hh:mm:ss a")
        };
    });
});

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = ref([
    {label: "All Book's Deals", active: true}
]);

const filters = ref<DataTableFilterMeta>();

const selectedDeals = ref<Deal[]>([]);
const isDeleting = ref<boolean>(false);

const initFilters = () => {
    filters.value = {
        id: {operator: FilterOperator.AND, constraints: [{value: undefined, matchMode: FilterMatchMode.EQUALS}]},
        discount_type: {
            operator: FilterOperator.AND,
            constraints: [{value: undefined, matchMode: FilterMatchMode.EQUALS}]
        },
        discount_value: {
            operator: FilterOperator.AND,
            constraints: [{value: undefined, matchMode: FilterMatchMode.GREATER_THAN_OR_EQUAL_TO}]
        },
        start_date: {
            operator: FilterOperator.AND,
            constraints: [{value: undefined, matchMode: FilterMatchMode.DATE_IS}]
        },
        end_date: {operator: FilterOperator.AND, constraints: [{value: undefined, matchMode: FilterMatchMode.DATE_IS}]},
        book_id: {value: undefined, matchMode: FilterMatchMode.IN},
    };
};

const onClearFilters = () => {
    onClear();
    initFilters();
};

const setDeleting = (value: boolean) => (isDeleting.value = value);

const deleteDealHandler = (id: number | number[]) => {
    setDeleting(true);
    const url = route("dashboard.deals.destroy", {ids: id});

    router.delete(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Deleted",
                detail: "Deal(s) has been deleted successfully",
                life: 3000
            });

            setDeleting(false);
        }
    });
};


const onDeleteDeal = (id: number) => {
    confirm.require({
        group: "confirmDeleteDialog",
        message: "Are you sure you want to delete this deal?",
        accept: () => deleteDealHandler(id)
    });
};

const onDeleteSelectedDeals = () => {
    const ids = selectedDeals.value.map(deal => deal.id);
    confirm.require({
        group: "confirmDeleteDialog",
        message: "Are you sure you want to delete selected deals?",
        accept: () => deleteDealHandler(ids)
    });
};

onBeforeMount(() => {
    initFilters();
});
</script>

<template>
    <Head title="All book's deals"/>
    <Toast/>
    <ConfirmDeleteDialog/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">All book's deals</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else :class="{'text-surface-500 pointer-events-none': item.active}"
                              v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <DataTable v-model:filters="filters" v-model:selection="selectedDeals" :value="formattedDeals"
                       class="max-w-[calc(100vw-3em)]"
                       dataKey="id"
                       filterDisplay="menu"
                       lazy
                       scrollHeight="450px"
                       scrollable
                       stripedRows
                       @filter="onFilter"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-center lg:justify-between">
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <Button
                                icon="pi pi-filter-slash"
                                outlined
                                severity="success"
                                @click="onClearFilters"
                            />
                            <Button
                                :disabled="selectedDeals.length === 0 || isDeleting"
                                :loading="isDeleting" icon="pi pi-trash" label="Bulk Delete" severity="danger"
                                @click="onDeleteSelectedDeals"/>
                            <IconField iconPosition="right">
                                <InputIcon class="pi pi-search cursor-pointer hover:!text-primary-500"/>
                                <InputText v-model="search" placeholder="Search here..." type="text" @change="onSearch"
                                />
                            </IconField>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <InertiaLink :href="route('dashboard.deals.create')">
                                <Button icon="pi pi-plus" label="Add new"/>
                            </InertiaLink>
                        </div>
                        <ProgressBar class="w-full"/>
                    </div>
                </template>
                <Column selectionMode="multiple"/>
                <Column dataType="numeric" field="id" header="#">
                    <template #filter="{filterModel}">
                        <InputNumber v-model="filterModel.value" placeholder="Search by id"/>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" :showFilterOperator="false" filterField="book_id" header="Book">
                    <template #body="{data}">
                        <InertiaLink :href="route('dashboard.books.edit', {slug: data.book.slug})"
                                     class="flex items-center gap-2">
                            <div class="w-10 h-14 bg-surface-200 px-1 py-1 rounded">
                                <img :alt="data.book.title" :src="data.book.coverImage"
                                     class="w-full h-full object-cover block"/>
                            </div>
                            <span v-text="data.book.title"/>
                        </InertiaLink>
                    </template>
                    <template #filter="{filterModel}">
                        <MultiSelect v-model="filterModel.value" :maxSelectedLabels="2"
                                     :options="books.data"
                                     class="w-full"
                                     optionLabel="title" optionValue="id" placeholder="Select a book"/>
                    </template>
                </Column>
                <Column dataType="boolean" field="discount_type" header="Discount type">
                    <template #body="{data}">
                        <Tag :severity="getDiscountSeverity(data.discount_type)" :value="data.discount_type"/>
                    </template>
                    <template #filter="{filterModel}">
                        <Dropdown v-model="filterModel.value" :options="DiscountTypeList" class="w-full"
                                  optionLabel="key" optionValue="value" placeholder="Choose a discount type">
                            <template #option="{option}">
                                <Tag :severity="getDiscountSeverity(option.value)" :value="option.value"/>
                            </template>
                            <template #value="{value, placeholder}">
                                <Tag v-if="value" :severity="getDiscountSeverity(value)" :value="value"/>
                                <span v-else>{{ placeholder }}</span>
                            </template>
                        </Dropdown>
                    </template>
                </Column>
                <Column dataType="numeric" field="discount_value" header="Discount value">
                    <template #filter="{filterModel}">
                        <InputNumber v-model="filterModel.value" class="w-full" mode="decimal"
                                     placeholder="Search by discount value"/>
                    </template>
                </Column>
                <Column dataType="date" field="start_date" header="Start date">
                    <template #filter="{filterModel}">
                        <Calendar v-model="filterModel.value" class="w-full" dateFormat="mm-dd-yy"
                                  iconDisplay="input" placeholder="Search by start date" showIcon
                        />
                    </template>
                </Column>
                <Column dataType="date" field="end_date" header="End date">
                    <template #filter="{filterModel}">
                        <Calendar v-model="filterModel.value" class="w-full" dateFormat="mm-dd-yy"
                                  iconDisplay="input" placeholder="Search by end date" showIcon
                        />
                    </template>
                </Column>
                <Column header="Action(s)">
                    <template #body="{data}">
                        <div class="flex items-center gap-2">
                            <InertiaLink :href="route('dashboard.deals.edit', {id: data.id})">
                                <Button icon="pi pi-pencil" outlined rounded type="button"/>
                            </InertiaLink>
                            <Button :disabled="isDeleting" :loading="isDeleting" icon="pi pi-trash" outlined rounded
                                    severity="danger" type="button" @click="onDeleteDeal(data.id)"/>
                        </div>
                    </template>
                </Column>
                <template #footer>
                    <div class="flex justify-between items-center">
                    <span
                        class="text-xs text-surface-500 font-light">Showing {{
                            deals.pagination.count
                        }} entries</span>
                        <Paginator v-model:first="first" :rows="deals.pagination.perPage"
                                   :rowsPerPageOptions="paginatorRowsPerPageOptions"
                                   :template="paginatorTemplate"
                                   :totalRecords="deals.pagination.total"
                                   @page="onPageChange"
                        />
                    </div>
                </template>
                <template #empty>
                    <div class="flex flex-col justify-center items-center gap-2 text-surface-500">
                        <i class="pi pi-info-circle text-info text-4xl"/>
                        <p class="text-center text-lg">No deals found.</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
