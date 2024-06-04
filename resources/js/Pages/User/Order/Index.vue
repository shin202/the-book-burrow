<script lang="ts">
import LandingLayout from "@/Layouts/LandingLayout.vue";

export default {
    layout: [LandingLayout]
};
</script>
<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";

import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";
import Button from "primevue/button";
import Paginator from "primevue/paginator";

import {Order, Pagination} from "@/types";
import {useDateFormat} from "@vueuse/core";
import {usePaginator, useSeverity} from "@/composables";

import InertiaLink from "@/Components/InertiaLink.vue";
import UserLayout from "@/Layouts/UserLayout.vue";

const props = defineProps<{
    orders: Pagination<Order[]>
}>();

const {getOrderStatusSeverity} = useSeverity();
const {paginatorTemplate, paginatorRowsPerPageOptions, onPageChange, first} = usePaginator(props.orders);
</script>

<template>
    <Head title="My orders"/>
    <UserLayout>
        <div class="mt-4 px-6 flex flex-col gap-4 w-full">
            <h1 class="font-bold text-xl">My orders</h1>
            <DataTable :value="orders.data" class="!w-full" dataKey="order_number"
                       stripedRows
            >
                <Column field="created_at" header="Date">
                    <template #body="{data}">
                    <span>
                        {{ useDateFormat(data.created_at, "MMM-DD-YYYY hh:mm:ss a", {locales: "en-US"}).value }}
                    </span>
                    </template>
                </Column>
                <Column field="order_number" header="Order number"/>
                <Column field="billing_total" header="Total"/>
                <Column field="status" header="Status">
                    <template #body="{data}">
                        <Tag :severity="getOrderStatusSeverity(data.status)" :value="data.status"/>
                    </template>
                </Column>
                <Column header="">
                    <template #body="{data}">
                        <div class="flex items-center gap-4">
                            <InertiaLink :href="route('my-profile.orders.show', data.order_number)">
                                <Button icon="pi pi-eye" outlined rounded severity="success"/>
                            </InertiaLink>
                            <a :href="route('my-profile.orders.invoice', data.order_number)"
                               target="_blank">
                                <Button icon="pi pi-file-pdf" outlined rounded severity="info"/>
                            </a>
                        </div>
                    </template>
                </Column>
                <template #empty>
                    <div class="flex items-center justify-center h-60">
                        <p>
                            Looks like you haven't placed any orders yet.
                            <InertiaLink :href="route('shop.index')"
                                         class="text-primary-800 hover:text-primary-800/80 hover:underline">
                                Shopping now
                                <i class="pi pi-shopping-cart"/>
                            </InertiaLink>
                        </p>
                    </div>
                </template>
                <template #footer>
                    <Paginator :first="first"
                               :rows="orders.pagination.perPage"
                               :rowsPerPageOptions="paginatorRowsPerPageOptions"
                               :template="paginatorTemplate"
                               :totalRecords="orders.pagination.total"
                               @page="onPageChange"
                    />
                </template>
            </DataTable>
        </div>
    </UserLayout>
</template>

<style lang="scss" scoped>

</style>
