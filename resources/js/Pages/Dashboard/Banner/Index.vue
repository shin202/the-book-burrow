<script lang="ts" setup>
import {Head, router} from "@inertiajs/vue3";
import {ref} from "vue";
import {useToast} from "primevue/usetoast";

import Breadcrumb from "primevue/breadcrumb";
import DataTable, {DataTableCellEditCompleteEvent} from "primevue/datatable";
import Column from "primevue/column";
import Image from "primevue/image";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import ProgressBar from "primevue/progressbar";
import InputIcon from "primevue/inputicon";
import IconField from "primevue/iconfield";
import Paginator from "primevue/paginator";
import Tag from "primevue/tag";
import Dropdown from "primevue/dropdown";
import Toast from "primevue/toast";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";

import {Banner, Pagination} from "@/types";
import {usePaginator, useSearch} from "@/composables";
import ConfirmDeleteDialog from "@/Components/ConfirmDeleteDialog.vue";
import {useConfirm} from "primevue/useconfirm";

const props = defineProps<{
    banners: Pagination<any>
}>();

const toast = useToast();
const confirm = useConfirm();

const {paginatorTemplate, paginatorRowsPerPageOptions, first, onPageChange} = usePaginator(props.banners);
const {search, onSearch} = useSearch();

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = ref([
    {label: "All banners", active: true}
]);

const bannerStatusList = [
    {label: "active", value: "active"},
    {label: "inactive", value: "inactive"}
];

const selectedBanners = ref<Banner[]>([]);
const isDeleting = ref<boolean>(false);

const getBannerStatusSeverity = (status: "active" | "inactive") => {
    return status === "active" ? "success" : "warning";
};

const setDeleting = (value: boolean) => (isDeleting.value = value);

const onCellEditComplete = ({field, newValue, data}: DataTableCellEditCompleteEvent) => {
    const url = route("dashboard.banners.update", {id: data.id});

    router.patch(url, {
        [field]: newValue
    });
};

const deleteBannerHandler = (id: number | number[]) => {
    setDeleting(true);
    const url = route("dashboard.banners.destroy", {ids: id});

    router.delete(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Deleted",
                detail: "Banner(s) deleted successfully.",
                life: 3000,
            });
            setDeleting(false);
        }
    });
};

const onDeleteBanner = (id: number) => {
    confirm.require({
        group: "confirmDeleteDialog",
        message: "Are you sure you want to delete this banner?",
        icon: "pi pi-exclamation-triangle",
        acceptLabel: "Yes, delete it",
        accept: () => deleteBannerHandler(id)
    });
};

const onDeleteSelectedBanners = () => {
    const ids = selectedBanners.value.map((banner) => banner.id);
    confirm.require({
        group: "confirmDeleteDialog",
        message: "Are you sure you want to delete the selected banner(s)?",
        acceptLabel: "Yes, delete them",
        accept: () => deleteBannerHandler(ids)
    });
};
</script>

<template>
    <Head title="All banners"/>
    <Toast/>
    <ConfirmDeleteDialog/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">All banners</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else :class="{'text-surface-500 pointer-events-none': item.active}"
                              v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <DataTable v-model:selection="selectedBanners" :value="banners.data" class="max-w-[calc(100vw-3em)]"
                       dataKey="id"
                       editMode="cell" scrollHeight="450px" scrollable
                       stripedRows
                       @cellEditComplete="onCellEditComplete"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-center lg:justify-between">
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <Button
                                :disabled="selectedBanners.length === 0 || isDeleting"
                                :loading="isDeleting"
                                icon="pi pi-trash"
                                severity="danger"
                                @click="onDeleteSelectedBanners"/>
                            <IconField iconPosition="right">
                                <InputIcon class="pi pi-search cursor-pointer hover:!text-primary-500"/>
                                <InputText v-model="search" placeholder="Search here..." type="text"
                                           @change="onSearch"
                                />
                            </IconField>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <InertiaLink :href="route('dashboard.banners.create')">
                                <Button icon="pi pi-plus" label="Add new"/>
                            </InertiaLink>
                        </div>
                        <ProgressBar class="w-full"/>
                    </div>
                </template>
                <Column selectionMode="multiple"/>
                <Column field="id" header="#" style="min-width: 2rem;"/>
                <Column header="Image" style="min-width: 8rem;">
                    <template #body="{data}">
                        <div class="flex items-center space-x-4">
                            <div class="bg-surface-100 w-20 h-20 flex justify-center items-center rounded px-2 py-4">
                                <Image :alt="`${data.title} cover image`" :src="data.image" preview width="50"/>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column field="title" header="Title" style="min-width: 8rem;"/>
                <Column field="description" header="Description" style="min-width: 8rem;"/>
                <Column field="status" header="Status">
                    <template #body="{data}">
                        <Tag :severity="getBannerStatusSeverity(data.status)" :value="data.status"/>
                    </template>
                    <template #editor="{data, field}">
                        <Dropdown v-model="data[field]" :options="bannerStatusList" optionLabel="label"
                                  optionValue="value">
                            <template #option="{option}">
                                <Tag :severity="getBannerStatusSeverity(option.value)" :value="option.label"/>
                            </template>
                            <template #value="{value}">
                                <Tag :severity="getBannerStatusSeverity(value)" :value="value"/>
                            </template>
                        </Dropdown>
                    </template>
                </Column>
                <Column header="Action(s)" style="min-width: 5rem;">
                    <template #body="{data}">
                        <div class="flex flex-wrap gap-2 items-center">
                            <InertiaLink :href="route('dashboard.banners.edit', {id: data.id})">
                                <Button icon="pi pi-pencil" outlined rounded type="button"/>
                            </InertiaLink>
                            <Button :disabled="isDeleting" :loading="isDeleting" icon="pi pi-trash" outlined rounded
                                    severity="danger" type="button" @click="onDeleteBanner(data.id)"/>
                        </div>
                    </template>
                </Column>
                <template #footer>
                    <div class="flex justify-between items-center">
                    <span
                        class="text-xs text-surface-500 font-light">Showing {{
                            banners.pagination.count
                        }} entries</span>
                        <Paginator v-model:first="first" :rows="banners.pagination.perPage"
                                   :rowsPerPageOptions="paginatorRowsPerPageOptions"
                                   :template="paginatorTemplate"
                                   :totalRecords="banners.pagination.total"
                                   @page="onPageChange"
                        />
                    </div>
                </template>
                <template #empty>
                    <div class="flex flex-col justify-center items-center gap-2 text-surface-500">
                        <i class="pi pi-info-circle text-info text-4xl"/>
                        <p class="text-center text-lg">No banners found.</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
