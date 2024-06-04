<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";
import {useDateFormat} from "@vueuse/core";

import Tag from "primevue/tag";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Timeline from "primevue/timeline";
import Button from "primevue/button";


import LandingLayout from "@/Layouts/LandingLayout.vue";
import UserLayout from "@/Layouts/UserLayout.vue";

import {Order} from "@/types";
import {useSeverity} from "@/composables";
import InertiaLink from "@/Components/InertiaLink.vue";
import {OrderStatusEnum} from "@/common/enums";
import {computed} from "vue";

defineOptions({
    layout: LandingLayout
});

const props = defineProps<{
    order: Order,
}>();

const {getOrderStatusSeverity} = useSeverity();

const formattedStatuses = computed(() => {
    const markerClasses = {
        [OrderStatusEnum.PENDING]: "bg-gray-500",
        [OrderStatusEnum.PROCESSING]: "bg-orange-500",
        [OrderStatusEnum.COMPLETED]: "bg-blue-500",
        [OrderStatusEnum.SHIPPED]: "bg-violet-500",
        [OrderStatusEnum.DELIVERED]: "bg-green-500",
        [OrderStatusEnum.CANCELLED]: "bg-red-500",
        [OrderStatusEnum.FAILED]: "bg-red-500",
        [OrderStatusEnum.REFUNDED]: "bg-red-500",
    };

    const icons = {
        [OrderStatusEnum.PENDING]: "pi pi-shopping-cart",
        [OrderStatusEnum.PROCESSING]: "pi pi-spinner-dotted",
        [OrderStatusEnum.COMPLETED]: "pi pi-check",
        [OrderStatusEnum.SHIPPED]: "pi pi-truck",
        [OrderStatusEnum.DELIVERED]: "pi pi-home",
        [OrderStatusEnum.CANCELLED]: "pi pi-times",
        [OrderStatusEnum.FAILED]: "pi pi-times",
        [OrderStatusEnum.REFUNDED]: "pi pi-money-bill",
    };

    return props.order.status_history.map(status => {
        return {
            ...status,
            markerClass: markerClasses[status.status],
            icon: icons[status.status]
        };
    });
});

window.Echo.private(`orders.${props.order.order_number}`)
    .listen(".OrderStatusUpdated", (data: any) => {
        props.order.status_history.push({
            status: data.status,
            created_at: data.timestamp,
        });
    });
</script>

<template>
    <Head :title="`Order #${order.order_number}`"/>
    <UserLayout>
        <div class="mt-4 px-6 flex-col gap-4 w-full">
            <h1 class="font-bold text-2xl">Order #{{ order.order_number }}</h1>
            <div class="mt-4">
                <div class="flex flex-wrap justify-between gap-16">
                    <div class="flex-1 bg-primary-300/50 px-6 py-4 rounded-lg divide-y">
                        <h2 class="font-bold text-lg text-primary-800">Order Summary</h2>
                        <div class="flex flex-col gap-4 mt-4 py-4">
                            <div class="flex justify-between">
                                <span class="font-medium">Order number:</span>
                                <span>{{ order.order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Order date:</span>
                                <span>{{
                                        useDateFormat(order.created_at, "MMM-DD-YYYY hh:mm:ss a", {locales: "en-US"}).value
                                    }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Sub total:</span>
                                <span>{{ order.billing_subtotal }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Shipping:</span>
                                <span>Free</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Discount ({{ order.billing_discount_code }}):</span>
                                <span>{{ order.billing_discount }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Total:</span>
                                <span>{{ order.billing_total }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Status:</span>
                                <Tag :severity="getOrderStatusSeverity(order.status)" :value="order.status"/>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 bg-primary-300/50 px-6 py-4 rounded-lg divide-y">
                        <h2 class="font-bold text-lg text-primary-800">Billing address</h2>
                        <div class="flex flex-col gap-4 mt-4 py-4">
                            <div class="flex justify-between gap-6">
                                <span class="font-medium">Name:</span>
                                <span>{{ order.billing_name }}</span>
                            </div>
                            <div class="flex justify-between gap-6">
                                <span class="font-medium">Address:</span>
                                <p>
                                    {{ order.billing_address }}<br>
                                    {{ order.billing_city }}, {{ order.billing_state }}<br>
                                    {{ order.billing_zip }}<br>
                                    {{ order.billing_country }}
                                </p>
                            </div>
                            <div class="flex justify-between gap-6">
                                <span class="font-medium">Phone:</span>
                                <span>{{ order.billing_phone }}</span>
                            </div>
                            <div class="flex justify-between gap-6">
                                <span class="font-medium">Email:</span>
                                <span>{{ order.billing_email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <h2 class="font-bold text-lg text-primary-800">Order Items</h2>
                    <div class="mt-4">
                        <DataTable :value="order.items" class="!w-full" dataKey="id"
                                   stripedRows
                        >
                            <Column header="Product">
                                <template #body="{data}">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="flex justify-center items-center p-2 h-20 w-16 bg-surface-200 rounded">
                                            <img :alt="data.title" :src="data.cover_image"
                                                 class="w-full h-full object-cover"/>
                                        </div>
                                        <div class="flex flex-col">
                                            <span>Product title</span>
                                            <h3 class="font-bold hover:text-primary-900">
                                                <InertiaLink :href="route('books.show', data.slug)">
                                                    {{ data.title }}
                                                </InertiaLink>
                                            </h3>
                                        </div>
                                    </div>
                                </template>
                            </Column>
                            <Column field="quantity" header="Quantity"/>
                            <Column field="price" header="Price"/>
                            <Column field="total" header="Total"/>
                        </DataTable>
                    </div>
                </div>
                <div class="mt-8">
                    <h2 class="font-bold text-lg text-primary-800">Order Tracking</h2>
                    <div class="mt-4">
                        <Timeline :value="formattedStatuses" align="top" class="duration-300 transition-all ease-out">
                            <template #marker="{item}">
                                <span
                                    :class="item.markerClass"
                                    class="w-8 h-8 px-4 flex items-center justify-center text-white shadow rounded-full"
                                >
                                    <i :class="item.icon"/>
                                </span>
                            </template>
                            <template #content="{item}">
                                <Tag :severity="getOrderStatusSeverity(item.status)" :value="item.status"/>
                            </template>
                            <template #opposite="{item}">
                                <span class="px-4 py-2 bg-surface-0 rounded-lg">{{
                                        useDateFormat(item.created_at, "MM-DD-YYYY hh:mm:ss a", {locales: "en-US"}).value
                                    }}</span>
                            </template>
                        </Timeline>
                    </div>
                </div>
                <div class="mt-8">
                    <Button icon="pi pi-times" label="Cancel Order" severity="danger"/>
                    <Button class="ml-4" icon="pi pi-money-bill" label="Cancel & Refund" severity="danger"/>
                    <Button class="ml-4" icon="pi pi-undo" label="Request a return" severity="secondary"/>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<style lang="scss" scoped>

</style>
