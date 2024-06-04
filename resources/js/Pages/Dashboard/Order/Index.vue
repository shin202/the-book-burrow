<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";
import {computed, onBeforeMount, ref} from "vue";

import Breadcrumb from "primevue/breadcrumb";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Avatar from "primevue/avatar";
import Tag from "primevue/tag";
import Button from "primevue/button";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import ProgressBar from "primevue/progressbar";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import Paginator from "primevue/paginator";
import MultiSelect from "primevue/multiselect";
import Dropdown from "primevue/dropdown";
import Calendar from "primevue/calendar";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";
import {Order, Pagination, User} from "@/types";
import {usePaginator, useSearch, useSeverity} from "@/composables";
import {useFilter} from "@/composables/use-filter";
import {OrderStatusList} from "@/common/enums";
import {useUrlSearchParams} from "@vueuse/core";

const props = defineProps<{
    orders: Pagination<Order[]>,
    users: Pagination<User[]>
}>();

const params = useUrlSearchParams("history");

const {getOrderStatusSeverity} = useSeverity();
const {paginatorRowsPerPageOptions, paginatorTemplate, onPageChange, first} = usePaginator(props.orders);
const {search, onSearch} = useSearch();
const {onClear, onFilter} = useFilter();

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = ref([
    {label: "Order"},
    {label: "Order List", active: true}
]);

const formattedOrders = computed(() => {
    return props.orders.data.map((order: Order) => {
        return {
            ...order,
            created_at: window.dayjs(order.created_at).format("MMM-DD-YYYY hh:mm:ss a"),
        };
    });
});

const filters = ref();

const initFilters = () => {
    filters.value = {
        id: {value: params["filter[id]"]},
        order_number: {value: params["filter[order_number]"]},
        user_id: {value: params["filter[user_id]"] ? (<string>params["filter[user_id]"])?.split(",").map(Number) : undefined},
        total: {value: params["filter[total]"]},
        status: {value: params["filter[status]"]},
        created_at: {value: params["filter[created_at]"]},
    };
};

const onClearFilter = () => {
    onClear();
    initFilters();
};

onBeforeMount(() => {
    initFilters();
});
</script>

<template>
    <Head title="All orders"/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Order List</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else :class="{'text-surface-500 pointer-events-none': item.active}"
                              v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <DataTable v-model:filters="filters" :value="formattedOrders" class="min-w-[calc(100vw-3em)]"
                       filterDisplay="menu" lazy scrollHeight="450px"
                       scrollable
                       stripedRows @filter="onFilter">
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-center lg:justify-between">
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <Button
                                icon="pi pi-filter-slash"
                                outlined
                                severity="success"
                                @click="onClearFilter"/>
                            <Button
                                icon="pi pi-trash"
                                severity="danger"/>
                            <IconField iconPosition="right">
                                <InputIcon class="pi pi-search cursor-pointer hover:!text-primary-500"/>
                                <InputText v-model="search" placeholder="Search here..." type="text" @change="onSearch"
                                />
                            </IconField>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <Button
                                icon="pi pi-download" label="Export"
                                severity="help"/>
                        </div>
                        <ProgressBar class="w-full"/>
                    </div>
                </template>
                <Column :showFilterMatchModes="false" dataType="numeric" field="id" header="#" style="min-width: 5rem;">
                    <template #filter="{filterModel}">
                        <InputText v-model="filterModel.value" :useGrouping="false" mode="decimal"
                                   placeholder="Search by id (eg: 1,2,3...)"/>
                    </template>
                </Column>
                <Column :showFilerMatchModes="false" field="order_number" header="Order Number">
                    <template #filter="{filterModel}">
                        <InputText v-model="filterModel.value" placeholder="Search by order number"/>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" field="user.username" filterField="user_id" header="User">
                    <template #body="{data}">
                        <div class="flex items-center gap-2">
                            <Avatar :label="data.user.username.charAt(0).toUpperCase()" shape="circle"/>
                            <span v-text="data.user.username"/>
                        </div>
                    </template>
                    <template #filter="{filterModel}">
                        <MultiSelect v-model="filterModel.value" :maxSelectedLabels="2"
                                     :options="users.data"
                                     :virtualScrollerOptions="{itemSize: 40}"
                                     filter
                                     optionLabel="username" optionValue="id" placeholder="Select a user"/>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" dataType="numeric" field="billing_total" filterField="total"
                        header="Total">
                    <template #filter="{filterModel}">
                        <InputNumber v-model="filterModel.value" currency="USD" locale="en-US" mode="currency"
                                     placeholder="Search by total >="/>
                    </template>
                </Column>
                <Column field="total_profit" header="Profit"/>
                <Column :showFilterMatchModes="false" field="status" header="Status">
                    <template #body="{data}">
                        <Tag :severity="getOrderStatusSeverity(data.status)" :value="data.status"/>
                    </template>
                    <template #filter="{filterModel}">
                        <Dropdown v-model="filterModel.value" :options="OrderStatusList" optionLabel="key"
                                  optionValue="value"
                                  placeholder="Select a status">
                            <template #option="{option}">
                                <Tag :severity="getOrderStatusSeverity(option.value)" :value="option.value"/>
                            </template>
                            <template #value="{value, placeholder}">
                                <Tag v-if="value" :severity="getOrderStatusSeverity(value)" :value="value"/>
                                <span v-else v-text="placeholder"/>
                            </template>
                        </Dropdown>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" dataType="date" field="created_at" header="Created At">
                    <template #filter="{filterModel}">
                        <Calendar v-model="filterModel.value" dateFormat="mm-dd-yy" iconDisplay="input"
                                  placeholder="Search by date"
                                  showIcon/>
                    </template>
                </Column>
                <Column header="Action(s)">
                    <template #body="{data}">
                        <div class="flex items-center gap-2">
                            <InertiaLink :href="route('dashboard.orders.show', {orderNumber: data.order_number})">
                                <Button icon="pi pi-eye" outlined rounded severity="success"/>
                            </InertiaLink>
                            <Button icon="pi pi-trash" outlined rounded severity="danger"/>
                        </div>
                    </template>
                </Column>
                <template #footer>
                    <div class="flex justify-between items-center">
                    <span
                        class="text-xs text-surface-500 font-light">Showing {{
                            orders.pagination.count
                        }} entries</span>
                        <Paginator v-model:first="first" :rows="orders.pagination.perPage"
                                   :rowsPerPageOptions="paginatorRowsPerPageOptions"
                                   :template="paginatorTemplate"
                                   :totalRecords="orders.pagination.total"
                                   @page="onPageChange"
                        />
                    </div>
                </template>
                <template #empty>
                    <div class="flex flex-col justify-center items-center gap-2 text-surface-500">
                        <i class="pi pi-info-circle text-info text-4xl"/>
                        <p class="text-center text-lg">No orders found.</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
