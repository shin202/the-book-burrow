<script lang="ts" setup>
import {Head, router} from "@inertiajs/vue3";
import {computed, reactive, ref} from "vue";

import Card from "primevue/card";
import Chart from "primevue/chart";
import Dropdown from "primevue/dropdown";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Image from "primevue/image";
import Tag from "primevue/tag";
import Button from "primevue/button";
import Dialog from "primevue/dialog";


import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import {ChartData} from "chart.js";
import {Book} from "@/types";
import {useSeverity} from "@/composables";
import {camelCase, upperCase} from "lodash";
import {useUrlSearchParams} from "@vueuse/core";
import {useNumberFormat} from "@/composables/use-number-format";

interface ReportData {
    total: {
        abbr: string,
        full: string
    },
    current: string,
    last: string,
    rate: number,
    status: string,
    chart: ChartData
}

const props = defineProps<{
    sales: ReportData,
    revenue: ReportData,
    orders: ReportData,
    customers: ReportData,
    recentOrders: {
        chart: ChartData
    },
    bestSellers: Book[],
    salesByGenre: {
        chart: ChartData
    },
    earnings: {
        total_revenue: {
            abbr: string,
            full: string
        },
        total_profit: {
            abbr: string,
            full: string
        },
        chart: ChartData
    }
}>();

const {getStockStatusSeverity} = useSeverity();
const params = useUrlSearchParams("history");

const chartOptions = ref({
    responsive: true,
    aspectRatio: 1.75,
});

const overviewCards = computed(() => [
    {
        id: "total_sales",
        icon: "pi pi-shop",
        title: "Total Sales",
        value: props.sales.total,
        percentage: props.sales.rate,
        status: props.sales.status,
        bgClass: "bg-green-500",
        data: {
            labels: props.sales.chart.labels,
            datasets: [
                {
                    ...props.sales.chart.datasets[0],
                    fill: true,
                    tension: 0.4,
                    borderColor: "#4CAF50",
                    backgroundColor: "rgba(137,255,143,0.57)"
                }
            ]
        },
    },
    {
        id: "revenue",
        icon: "pi pi-dollar",
        title: "Revenue",
        value: props.revenue.total,
        percentage: props.revenue.rate,
        status: props.revenue.status,
        bgClass: "bg-orange-500",
        data: {
            labels: props.revenue.chart.labels,
            datasets: [
                {
                    ...props.revenue.chart.datasets[0],
                    fill: true,
                    tension: 0.4,
                    borderColor: "#FFC107",
                    backgroundColor: "rgba(255,193,7,0.49)"
                }
            ]
        },
    },
    {
        id: "orders",
        icon: "pi pi-box",
        title: "Orders Placed",
        value: props.orders.total,
        percentage: props.orders.rate,
        status: props.orders.status,
        bgClass: "bg-blue-500",
        data: {
            labels: props.orders.chart.labels,
            datasets: [
                {
                    ...props.orders.chart.datasets[0],
                    fill: true,
                    tension: 0.4,
                    borderColor: "#1976D2",
                    backgroundColor: "rgba(25,118,210,0.49)"
                }
            ]
        },
    },
    {
        id: "customers",
        icon: "pi pi-users",
        title: "Total Customers",
        value: props.customers.total,
        percentage: props.customers.rate,
        status: props.customers.status,
        bgClass: "bg-purple-500",
        data: {
            labels: props.customers.chart.labels,
            datasets: [
                {
                    ...props.customers.chart.datasets[0],
                    fill: true,
                    tension: 0.4,
                    borderColor: "#9C27B0",
                    backgroundColor: "rgba(156,39,176,0.49)"
                }
            ]
        },
    }
]);

const recentOrderChart = computed(() => {
    return {
        labels: props.recentOrders.chart.labels,
        datasets: [
            {
                ...props.recentOrders.chart.datasets[0],
                fill: true,
                tension: 0.4,
                borderColor: "#1976D2",
                backgroundColor: "rgba(25,118,210,0.49)"
            }
        ]
    };
});

const salesByGenreChart = computed(() => {
    return {
        labels: props.salesByGenre.chart.labels,
        datasets: [
            {
                ...props.salesByGenre.chart.datasets[0],
                backgroundColor: [
                    "#FF6384",
                    "#36A2EB",
                    "#FFCE56",
                    "#4CAF50",
                    "#FFC107",
                ],
                hoverBackgroundColor: [
                    "#fc89a1",
                    "#67bef8",
                    "#fcd984",
                    "#7aee7f",
                    "#f6ce54",
                ]
            }
        ]
    };
});

const earningsChart = computed(() => {
    return {
        labels: props.earnings.chart.labels,
        datasets: props.earnings.chart.datasets.map((dataset: any, i: number) => {
            return {
                ...dataset,
                borderColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4CAF50", "#FFC107"][i],
                backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4CAF50", "#FFC107"][i],
            };
        })
    };
});

const bestSellerOptions = [
    {label: "All time", value: null},
    {label: "This week", value: "week"},
    {label: "This month", value: "month"},
    {label: "This year", value: "year"},
];
const bestSellerBy = ref(params.bestSellerBy || null);

const isShowDialog = reactive<any>({
    total_sales: false,
    revenue: false,
    orders: false,
    customers: false,
});

const onBestSellerOptionChange = () => {
    const url = route(route().current()!);
    router.get(url, {
        ...route().params,
        bestSellerBy: bestSellerBy.value
    }, {
        replace: true,
        preserveState: true,
    });
};

const reportUnitsOptions = [
    {label: "Days", value: "days"},
    {label: "Weeks", value: "weeks"},
    {label: "Months", value: "months"},
    {label: "Years", value: "years"},
];
const reportUnit = ref(params.reportUnit || "months");

const reportLimitOptions = [3, 5, 7, 12];
const reportLimit = ref(params.reportLimit ? Number(params.reportLimit) : 3);

const onReportLimitChange = () => {
    router.reload({
        data: {
            ...route().params,
            reportLimit: reportLimit.value
        },
        only: ["sales", "revenue", "orders", "customers"]
    });
};

const onReportUnitChange = () => {
    router.reload({
        data: {
            ...route().params,
            reportUnit: reportUnit.value
        },
        only: ["sales", "revenue", "orders", "customers"]
    });
};
</script>

<template>
    <Head title="Dashboard"/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="mt-4 grid lg:grid-cols-2 xl:grid-cols-4 gap-4">
                <Card v-for="card in overviewCards" :key="card.id" class="card">
                    <template #title>
                        <div class="flex justify-between">
                            <div class="relative flex space-x-4">
                                <div
                                    :class="card.bgClass"
                                    class="w-12 h-12 flex items-center justify-center text-surface-0 text-xl rounded"
                                    style="clip-path: polygon(50% 0%, 90% 20%, 100% 60%, 75% 100%, 25% 100%, 0% 60%, 10% 20%);">
                                    <i :class="card.icon"/>
                                </div>
                                <div>
                                    <h2 class="text-sm text-surface-600 font-light" v-text="card.title"/>
                                    <span v-tooltip.right="card.value.full" class="text-2xl font-bold"
                                          v-text="card.value.abbr"/>
                                </div>
                            </div>
                            <div class="text-sm flex justify-center items-center space-x-1.5">
                                <i :class="{'pi pi-arrow-up text-green-500': card.status === 'up', 'pi pi-arrow-down text-red-500': card.status === 'down'}"/>
                                <span v-text="`${card.percentage}%`"/>
                            </div>
                            <Button icon="pi pi-ellipsis-v" plain severity="secondary" size="small" text
                                    @click="() => isShowDialog[card.id] = !isShowDialog[card.id]"/>
                        </div>
                    </template>
                    <template #content>
                        <div class="flex items-center w-full">
                            <Chart :data="card.data" :options="chartOptions" class="w-full" type="line"/>
                        </div>
                        <Dialog v-model:visible="isShowDialog[card.id]" :closable="true">
                            <template #header>
                                <h2 class="font-bold text-xl" v-text="card.title"/>
                            </template>
                            <div>
                                <div class="flex flex-col justify-center items-center gap-4">
                                    <div class="flex self-end items-center gap-2">
                                        <Dropdown v-model="reportLimit" :options="reportLimitOptions" class="w-fit"
                                                  placeholder="5" @update:modelValue="onReportLimitChange"/>
                                        <Dropdown v-model="reportUnit" :options="reportUnitsOptions" class="w-fit"
                                                  optionLabel="label"
                                                  optionValue="value" placeholder="Months"
                                                  @update:modelValue="onReportUnitChange"/>
                                    </div>
                                    <Chart :data="card.data" class="w-full flex items-center justify-center"
                                           type="line"/>
                                    <table class="w-full table-auto mt-4">
                                        <thead>
                                        <tr>
                                            <th class="py-2 px-4 bg-surface-100 text-center">Date</th>
                                            <th class="py-2 px-4 bg-surface-100 text-center">{{ card.title }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(label, index) in card.data.labels" :key="<string>label"
                                            class="odd:bg-surface-0 even:bg-surface-50">
                                            <td class="text-center py-2 px-4" v-text="label"/>
                                            <td
                                                class="text-center py-2 px-4"
                                            >{{ useNumberFormat(card.data.datasets[0].data[index]) }}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </Dialog>
                    </template>
                </Card>
            </div>
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <Card class="card">
                    <template #title>
                        <div class="flex justify-between items-center">
                            <h2 class="font-bold text-xl">Recent Order</h2>
                            <i class="pi pi-ellipsis-h"/>
                        </div>
                    </template>
                    <template #content>
                        <div class="flex justify-center items-center">
                            <Chart :data="recentOrderChart" :options="chartOptions" class="w-full" type="line"/>
                        </div>
                    </template>
                </Card>
                <Card class="card">
                    <template #title>
                        <div class="flex justify-between items-center">
                            <h2 class="font-bold text-xl">Top Selling Books</h2>
                            <Dropdown v-model="bestSellerBy" :options="bestSellerOptions" inputClass="text-sm"
                                      optionLabel="label"
                                      optionValue="value" placeholder="All time"
                                      @update:modelValue="onBestSellerOptionChange"/>
                        </div>
                    </template>
                    <template #content>
                        <DataTable :scrollable="true" :value="bestSellers" scrollHeight="600px">
                            <Column header="Book" style="min-width: 4rem;">
                                <template #body="{data}">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-surface-200 w-10 h-10 flex justify-center items-center rounded">
                                            <Image :alt="`${data.title} cover image`" :src="data.cover_image"
                                                   width="20"/>
                                        </div>
                                        <h3 class="font-bold" v-text="data.title"/>
                                    </div>
                                </template>
                            </Column>
                            <Column field="total_sold" header="Total sold" style="min-width: 6rem;"/>
                            <Column field="total_revenue" header="Revenue" style="min-width: 6rem;">
                                <template #body="{data}">
                                    <span v-tooltip.right="data.total_revenue.full"
                                          v-text="`$${data.total_revenue.abbr}`"/>
                                </template>
                            </Column>
                            <Column header="Status" style="min-width: 7rem;">
                                <template #body="{data}">
                                    <Tag :severity="getStockStatusSeverity(data.stock_status)"
                                         :value="upperCase(camelCase(data.stock_status))"/>
                                </template>
                            </Column>
                        </DataTable>
                    </template>
                </Card>
            </div>
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <Card class="card">
                    <template #title>
                        <div class="flex justify-between items-center">
                            <h2 class="font-bold text-xl">Sales by genre</h2>
                            <i class="pi pi-ellipsis-h"/>
                        </div>
                    </template>
                    <template #content>
                        <div class="flex justify-center items-center">
                            <Chart :data="salesByGenreChart" :options="chartOptions" class="w-full" type="doughnut"/>
                        </div>
                    </template>
                </Card>
                <Card class="card">
                    <template #title>
                        <div class="flex justify-between items-center">
                            <h2 class="font-bold text-xl">Earnings</h2>
                            <i class="pi pi-ellipsis-h"/>
                        </div>
                    </template>
                    <template #content>
                        <div class="flex flex-col justify-center items-center gap-6">
                            <div class="flex items-center self-start gap-4">
                                <div>
                                    <h3 class="text-surface-500 text-sm">Total Revenue</h3>
                                    <span v-tooltip.right="props.earnings.total_revenue.full"
                                          class="text-xl font-bold"
                                    >${{ props.earnings.total_revenue.abbr }}</span>
                                </div>
                                <div>
                                    <h3 class="text-surface-500 text-sm">Total Profit</h3>
                                    <span v-tooltip.right="props.earnings.total_profit.full"
                                          class="text-xl font-bold"
                                    >${{ props.earnings.total_profit.abbr }}</span>
                                </div>
                            </div>
                            <Chart :data="earningsChart" class="w-full" type="bar"/>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>
.card {
    @apply relative;

    &::before, &::after {
        content: '';
        @apply absolute border border-primary-500 -top-0.5 -left-0.5 -right-0.5 -bottom-0.5 rounded-lg opacity-0 hover:opacity-100 transition-all duration-500;
    }

    &::before {
        animation: clippath 3s linear infinite;
    }

    &::after {
        animation: clippath 3s linear -1.5s infinite;
    }
}
</style>
