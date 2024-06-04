<script lang="ts" setup>
import {Head, router} from "@inertiajs/vue3";
import {onBeforeMount, ref} from "vue";
import {useToast} from "primevue/usetoast";
import {useDateFormat, useUrlSearchParams} from "@vueuse/core";
import {useConfirm} from "primevue/useconfirm";

import Breadcrumb from "primevue/breadcrumb";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";
import InputText from "primevue/inputtext";
import InputIcon from "primevue/inputicon";
import Button from "primevue/button";
import ProgressBar from "primevue/progressbar";
import IconField from "primevue/iconfield";
import Paginator from "primevue/paginator";
import Toast from "primevue/toast";
import InputNumber from "primevue/inputnumber";
import Dropdown from "primevue/dropdown";
import Calendar from "primevue/calendar";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";
import FileImport from "@/Components/FileImport.vue";
import ConfirmDeleteDialog from "@/Components/ConfirmDeleteDialog.vue";

import {usePaginator, useSearch} from "@/composables";
import {useFilter} from "@/composables/use-filter";
import {useExport} from "@/composables/use-export";
import {Coupon, Pagination} from "@/types";
import {DiscountTypeEnum, DiscountTypeList} from "@/common/enums";

const props = defineProps<{
    coupons: Pagination<Coupon[]>
}>();

const toast = useToast();
const confirm = useConfirm();
const params = useUrlSearchParams("history");

const {search, onSearch} = useSearch();
const {paginatorTemplate, paginatorRowsPerPageOptions, first, onPageChange} = usePaginator(props.coupons);
const {onExport, isExporting, exportProgress} = useExport("dashboard.coupons.export", "coupons");
const {onFilter, onClear} = useFilter();

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = ref([
    {label: "All coupons", active: true}
]);

const selectedCoupon = ref<Coupon[]>([]);
const isDeleting = ref<boolean>(false);

const filters = ref();

const getCouponStatusSeverity = (status: "enabled" | "disabled") => {
    const severity: { enabled: string, disabled: string } = {
        enabled: "success",
        disabled: "danger"
    };

    return severity?.[status];
};

const getCouponTypeSeverity = (type: DiscountTypeEnum) => {
    const severity: { [key in DiscountTypeEnum]: string } = {
        [DiscountTypeEnum.FIXED]: "success",
        [DiscountTypeEnum.PERCENTAGE]: "info"
    };

    return severity?.[type];
};

const setDeleting = (value: boolean) => (isDeleting.value = value);

const initFilters = () => {
    filters.value = {
        id: {value: params["filter[id]"]},
        code: {value: params["filter[code]"]},
        type: {value: params["filter[type]"]},
        value: {value: params["filter[value]"]},
        valid_from: {value: params["filter[valid_from]"]},
        valid_to: {value: params["filter[valid_to]"]},
        status: {value: params["filter[status]"]}
    };
};

const onClearFilter = () => {
    initFilters();
    onClear();
};

const deleteCouponHandler = (code: string | string[]) => {
    setDeleting(true);
    const url = route("dashboard.coupons.destroy", {codes: code});

    router.delete(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "Coupon(s) deleted successfully.",
                life: 3000
            });

            setDeleting(false);
        }
    });
};

const onDeleteCoupon = (code: string) => {
    confirm.require({
        group: "confirmDeleteDialog",
        message: "Are you sure you want to delete this coupons?",
        icon: "pi pi-exclamation-triangle",
        accept: () => deleteCouponHandler(code)
    });
};

const onDeleteSelectedCoupon = () => {
    const codes = selectedCoupon.value.map(coupon => coupon.code);
    confirm.require({
        group: "confirmDeleteDialog",
        message: "Are you sure you want to delete selected coupons(s)?",
        icon: "pi pi-exclamation-triangle",
        accept: () => deleteCouponHandler(codes)
    });
};

onBeforeMount(() => {
    initFilters();
});
</script>

<template>
    <Head title="All coupons"/>
    <Toast/>
    <ConfirmDeleteDialog/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">All coupons</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else :class="{'text-surface-500 pointer-events-none': item.active}"
                              v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <DataTable v-model:filters="filters" v-model:selection="selectedCoupon" :value="coupons.data"
                       class="max-w-[calc(100vw-3em)]"
                       dataKey="id"
                       filterDisplay="menu" lazy
                       scrollHeight="450px"
                       scrollable
                       @filter="onFilter"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-center lg:justify-between">
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <Button
                                icon="pi pi-filter-slash"
                                outlined
                                severity="success"
                                @click="onClearFilter"
                            />
                            <Button :disabled="selectedCoupon.length === 0 || isDeleting"
                                    :loading="isDeleting"
                                    icon="pi pi-trash"
                                    severity="danger" @click="onDeleteSelectedCoupon"/>
                            <IconField iconPosition="right">
                                <InputIcon class="pi pi-search cursor-pointer hover:!text-primary-500"/>
                                <InputText v-model="search" placeholder="Search here..." type="text"
                                           @change="onSearch"
                                />
                            </IconField>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <FileImport importer="coupons" routeName="dashboard.coupons.import"/>
                            <Button :disabled="isExporting" :loading="isExporting"
                                    icon="pi pi-download" label="Export"
                                    severity="help" @click="onExport"/>
                            <InertiaLink :href="route('dashboard.coupons.create')">
                                <Button icon="pi pi-plus" label="Add new"/>
                            </InertiaLink>
                        </div>
                        <ProgressBar :value="exportProgress" class="w-full"/>
                    </div>
                </template>
                <Column selectionMode="multiple"/>
                <Column :showFilterMatchModes="false" dataType="numeric" field="id" header="#">
                    <template #filter="{filterModel}">
                        <InputText v-model="filterModel.value" placeholder="Search by id (eg: 1,2,3,...)"/>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" field="code" header="Code" style="min-width: 5rem;">
                    <template #filter="{filterModel}">
                        <InputText v-model="filterModel.value" placeholder="Search by code"/>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" filterField="type" header="Type" style="min-width: 5rem;">
                    <template #body="{data}">
                        <Tag :severity="getCouponTypeSeverity(data.type)" v-text="data.type"/>
                    </template>
                    <template #filter="{filterModel}">
                        <Dropdown v-model="filterModel.value" :options="DiscountTypeList" optionLabel="key"
                                  optionValue="value" placeholder="Choose coupon type">
                            <template #option="{option}">
                                <Tag :severity="getCouponTypeSeverity(option.value)" :value="option.value"
                                />
                            </template>
                            <template #value="{value, placeholder}">
                                <template v-if="value">
                                    <Tag :severity="getCouponTypeSeverity(value)" :value="value"/>
                                </template>
                                <template v-else>
                                    <span>{{ placeholder }}</span>
                                </template>
                            </template>
                        </Dropdown>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" dataType="numeric" field="value" header="Value"
                        style="min-width: 5rem;">
                    <template #filter="{filterModel}">
                        <InputNumber v-model="filterModel.value" placeholder="Search by value >="/>
                    </template>
                </Column>
                <Column dataType="numeric" field="minimum_order_amount" header="Minimum Order Amount"
                        style="min-width: 5rem;"/>
                <Column :showFilterMatchModes="false" dataType="numeric" field="usage_limit" header="Usage Limit"
                        style="min-width: 5rem;"/>
                <Column dataType="numeric" field="usage_per_user" header="Usage Per User" style="min-width: 5rem;"/>
                <Column :showFilterMatchModes="false" dataType="date" field="valid_from" header="Valid From"
                        style="min-width: 10rem;">
                    <template #body="{data}">
                        <span>{{
                                useDateFormat(data.valid_from, `MMM-DD-YYYY hh:mm:ss a`, {locales: "en-US"}).value
                            }}</span>
                    </template>
                    <template #filter="{filterModel}">
                        <Calendar v-model="filterModel.value" dateFormat="y-m-d" hourFormat="12"
                                  iconDisplay="input"
                                  placeholder="Search by valid from <="
                                  showIcon
                                  showTime/>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" dataType="date" field="valid_to" header="Valid To"
                        style="min-width: 10rem;">
                    <template #body="{data}">
                        <span>{{
                                useDateFormat(data.valid_to, `MMM-DD-YYYY hh:mm:ss a`, {locales: "en-US"}).value
                            }}</span>
                    </template>
                    <template #filter="{filterModel}">
                        <Calendar v-model="filterModel.value" dateFormat="y-m-d" hourFormat="12"
                                  iconDisplay="input"
                                  placeholder="Search by valid to >="
                                  showIcon
                                  showTime/>
                    </template>
                </Column>
                <Column :showFilterMatchModes="false" dataType="boolean" filterField="status" header="Status">
                    <template #body="{data}">
                        <Tag :severity="getCouponStatusSeverity(data.status)" v-text="data.status"/>
                    </template>
                    <template #filter="{filterModel}">
                        <Dropdown v-model="filterModel.value"
                                  :options="[{label: 'enabled', value: true}, {label: 'disabled', value: false}]"
                                  optionLabel="label" optionValue="value" placeholder="Choose status">
                            <template #option="{option}">
                                <Tag :severity="getCouponStatusSeverity(option.label)" :value="option.label"/>
                            </template>
                        </Dropdown>
                    </template>
                </Column>
                <Column header="Action(s)" style="min-width: 8rem;">
                    <template #body="{data}">
                        <div class="flex flex-wrap gap-2 items-center">
                            <InertiaLink :href="route('dashboard.coupons.edit', {code: data.code})">
                                <Button icon="pi pi-pencil" outlined rounded/>
                            </InertiaLink>
                            <Button :disabled="isDeleting" :loading="isDeleting" icon="pi pi-trash" outlined rounded
                                    severity="danger" @click="onDeleteCoupon(data.code)"/>
                        </div>
                    </template>
                </Column>
                <template #footer>
                    <div class="flex justify-between items-center">
                    <span
                        class="text-xs text-surface-500 font-light">Showing {{
                            coupons.pagination.count
                        }} entries</span>
                        <Paginator v-model:first="first" :rows="coupons.pagination.perPage"
                                   :rowsPerPageOptions="paginatorRowsPerPageOptions"
                                   :template="paginatorTemplate"
                                   :totalRecords="coupons.pagination.total"
                                   @page="onPageChange"
                        />
                    </div>
                </template>
                <template #empty>
                    <div class="flex flex-col justify-center items-center gap-2 text-surface-500">
                        <i class="pi pi-info-circle text-info text-4xl"/>
                        <p class="text-center text-lg">No coupons found.</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </DashboardLayout>
</template>
