<script lang="ts" setup>
import {Head, useForm} from "@inertiajs/vue3";
import {ref} from "vue";

import Breadcrumb from "primevue/breadcrumb";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";
import Avatar from "primevue/avatar";
import Dropdown from "primevue/dropdown";
import Button from "primevue/button";
import Toast from "primevue/toast";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";

import {Order} from "@/types";
import {useSeverity} from "@/composables";
import {useToast} from "primevue/usetoast";
import {useDateFormat} from "@vueuse/core";

const props = defineProps<{
    order: Order;
    can: {
        updateOrder: boolean
    }
}>();

const toast = useToast();
const {getOrderStatusSeverity} = useSeverity();

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = ref([
    {label: "Order", route: "dashboard.orders.index", active: false},
    {label: "Order Detail"},
    {label: `Order #${props.order.order_number}`, active: true}
]);

const form = useForm({
    status: props.order.status,
});

const orderStatusOptions = [
    {key: "Pending", value: "pending"},
    {key: "Processing", value: "processing"},
    {key: "Completed", value: "completed"},
    {key: "Shipped", value: "shipped"},
    {key: "Delivered", value: "delivered"},
];

const onSubmit = (event: Event) => {
    event.preventDefault();
    const url = route("dashboard.orders.update", {orderNumber: props.order.order_number});
    form.patch(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "Order updated successfully",
                life: 3000
            });
        }
    });
};
</script>

<template>
    <Head :title="`Order #${order.order_number}`"/>
    <Toast/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Order #{{ order.order_number }}</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else :class="{'text-surface-500 pointer-events-none': item.active}"
                              v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="">
                    <div class="px-4 py-6 bg-surface-0 shadow-md rounded-lg">
                        <h2 class="font-bold bg-surface-50 px-6 py-2">From user</h2>
                        <div class="flex items-center gap-4 px-6 py-2">
                            <Avatar :label="order.user.username.charAt(0).toUpperCase()" shape="circle" size="xlarge"/>
                            <div class="flex flex-col">
                                <span>Username</span>
                                <h3 class="font-bold" v-text="order.user.username"/>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-6 bg-surface-0 shadow-md rounded-lg mt-4">
                        <h2 class="font-bold bg-surface-50 px-6 py-2">All item</h2>
                        <DataTable :value="order.items" scrollHeight="300px" scrollable stripedRows>
                            <Column header="Book">
                                <template #body="{data}">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="flex justify-center items-center p-2 h-20 w-16 bg-surface-200 rounded">
                                            <img :alt="data.title" :src="data.cover_image"
                                                 class="w-full h-full object-cover"/>
                                        </div>
                                        <div class="flex flex-col">
                                            <span>Book title</span>
                                            <h3 class="font-bold" v-text="data.title"/>
                                        </div>
                                    </div>
                                </template>
                            </Column>
                            <Column field="quantity" header="Quantity"/>
                            <Column field="price" header="Price"/>
                        </DataTable>
                    </div>
                    <div class="px-4 py-6 bg-surface-0 shadow-md rounded-lg mt-4">
                        <table class="w-full table-auto">
                            <thead>
                            <tr class="text-left">
                                <th class="font-bold bg-surface-50 px-4 py-2">Cart totals</th>
                                <th class="font-bold bg-surface-50 px-4 py-2">Price</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y">
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Subtotal</td>
                                <td class="px-4 py-2 font-bold">{{ order.billing_subtotal }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Discount ({{
                                        order.billing_discount_code ?? "N/A"
                                    }})
                                </td>
                                <td class="px-4 py-2 font-bold text-red-500">-{{ order.billing_discount }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-bold">Total price</td>
                                <td class="px-4 py-2 font-bold">{{ order.billing_total }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4 py-6 bg-surface-0 shadow-md rounded-lg">
                        <h2 class="font-bold px-4 py-2">Status</h2>
                        <DataTable :value="order.status_history" scrollHeight="200px" scrollable stripedRows>
                            <Column field="created_at" header="Date">
                                <template #body="{data}">
                                    {{
                                        useDateFormat(data.created_at, "MMM-DD-YYYY", {locales: "en-US"}).value
                                    }}
                                </template>
                            </Column>
                            <Column field="created_at" header="Time">
                                <template #body="{data}">
                                    {{
                                        useDateFormat(data.created_at, "hh:mm:ss a", {locales: "en-US"}).value
                                    }}
                                </template>
                            </Column>
                            <Column field="status" header="Status">
                                <template #body="{data}">
                                    <Tag :severity="getOrderStatusSeverity(data.status)" :value="data.status"/>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
                <div class="">
                    <div class="px-4 py-6 bg-surface-0 shadow-md rounded-lg">
                        <h2 class="font-bold px-4 py-2">Summary</h2>
                        <table class="w-full table-auto">
                            <tbody class="divide-y">
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Order number</td>
                                <td class="px-4 py-2 font-bold">#{{ order.order_number }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Order date</td>
                                <td class="px-4 py-2 font-bold">
                                    {{
                                        useDateFormat(order.created_at, "MMM-DD-YYYY hh:mm:ss a", {locales: "en-US"}).value
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Payment method</td>
                                <td class="px-4 py-2 font-bold">{{ order.payment_method }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Current status</td>
                                <td class="px-4 py-2 font-bold">
                                    <Tag :severity="getOrderStatusSeverity(order.status)" :value="order.status"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-6 bg-surface-0 shadow-md rounded-lg mt-4">
                        <h2 class="font-bold px-4 py-2">Billing address</h2>
                        <table class="w-full table-auto">
                            <tbody class="divide-y">
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Name</td>
                                <td class="px-4 py-2 font-bold">{{ order.billing_name }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Email</td>
                                <td class="px-4 py-2 font-bold">{{ order.billing_email }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Phone</td>
                                <td class="px-4 py-2 font-bold">{{ order.billing_phone }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Address</td>
                                <td class="px-4 py-2 font-bold">{{ order.billing_address }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-surface-500">City</td>
                                <td class="px-4 py-2 font-bold">{{ order.billing_city }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-surface-500">State</td>
                                <td class="px-4 py-2 font-bold">{{ order.billing_state }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Zip code</td>
                                <td class="px-4 py-2 font-bold">{{ order.billing_zip }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-6 bg-surface-0 shadow-md rounded-lg mt-4">
                        <h2 class="font-bold px-4 py-2">Payment information</h2>
                        <table class="w-full table-auto">
                            <tbody class="divide-y">
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Transaction id</td>
                                <td class="px-4 py-2 font-bold">{{ order.payment?.transaction_id }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Method</td>
                                <td class="px-4 py-2 font-bold">{{ order.payment?.method }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-surface-500">Paid at</td>
                                <td class="px-4 py-2 font-bold">{{ order.payment?.paid_at }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div v-if="can.updateOrder" class="px-4 py-6 bg-surface-0 shadow-md rounded-lg">
                <h2 class="font-bold bg-surface-50 px-6 py-2">Update order</h2>
                <form class="form">
                    <div class="form__group">
                        <label class="form__label form__label--required" for="status">Status</label>
                        <Dropdown v-model="form.status" :options="orderStatusOptions" optionLabel="key"
                                  optionValue="value">
                            <template #option="{option}">
                                <Tag :severity="getOrderStatusSeverity(option.value)" :value="option.value"/>
                            </template>
                            <template #value="{value, placeholder}">
                                <Tag v-if="value" :severity="getOrderStatusSeverity(value)" :value="value"/>
                                <span v-else v-text="placeholder"/>
                            </template>
                        </Dropdown>
                    </div>
                    <div class="form__group">
                        <Button label="Update" type="submit" @click="onSubmit"/>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
