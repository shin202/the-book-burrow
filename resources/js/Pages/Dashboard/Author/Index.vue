<script lang="ts" setup>
import {ref} from "vue";
import {Head, router} from "@inertiajs/vue3";
import {useToast} from "primevue/usetoast";

import Breadcrumb from "primevue/breadcrumb";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import Avatar from "primevue/avatar";
import Paginator from "primevue/paginator";
import Toast from "primevue/toast";
import ProgressBar from "primevue/progressbar";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";
import FileImport from "@/Components/FileImport.vue";

import {Author, Pagination} from "@/types";
import {useExport, usePaginator, useSearch} from "@/composables";

const props = defineProps<{
    authors: Pagination<Author[]>
}>();

const toast = useToast();

const {onSearch, search} = useSearch();
const {paginatorTemplate, paginatorRowsPerPageOptions, onPageChange, first} = usePaginator(props.authors);
const {onExport, isExporting, exportProgress} = useExport("dashboard.authors.export", "authors");

const home = ref({
    label: "Dashboard", route: "dashboard.index", active: route().current() === "dashboard.index"
});
const breadcrumbItems = ref([
    {label: "Author Lists", route: "dashboard.authors.index", active: true}
]);

const selectedAuthors = ref<Author[]>([]);
const isDeleting = ref(false);

const setDeleting = (value: boolean) => (isDeleting.value = value);

const deleteAuthorHandler = (slug: string | string[]) => {
    setDeleting(true);
    const url = route("dashboard.authors.destroy", {slugs: slug});
    router.delete(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Author(s) Deleted",
                detail: "Author(s) deleted successfully.",
                life: 3000
            });
            setDeleting(false);
            selectedAuthors.value = [];
        }
    });
};

const onDelete = (slug: string) => {
    deleteAuthorHandler(slug);
};

const onDeleteSelected = () => {
    const slugs = selectedAuthors.value.map(author => author.slug);
    deleteAuthorHandler(slugs);
};
</script>

<template>
    <Head title="Dashboard - Authors List"/>
    <Toast/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Author Lists</h1>
                <Breadcrumb :home="home" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <DataTable v-model:selection="selectedAuthors" :value="authors.data" class="max-w-[calc(100vw-3em)]"
                       dataKey="id"
                       removableSort scrollHeight="450px"
                       scrollable stripedRows>
                <template #header>
                    <div class="flex flex-wrap gap-4 items-center justify-center lg:justify-between">
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <Button v-tooltip.bottom="`Deleted ${selectedAuthors.length} selected`"
                                    :disabled="selectedAuthors.length === 0"
                                    :loading="isDeleting" icon="pi pi-trash"
                                    severity="danger"
                                    @click="onDeleteSelected"
                            />
                            <IconField iconPosition="right">
                                <InputIcon class="pi pi-search cursor-pointer hover:!text-primary-500"/>
                                <InputText v-model="search" placeholder="Search" @change="onSearch"/>
                            </IconField>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <FileImport routeName="dashboard.authors.import"/>
                            <Button :disabled="isExporting" :loading="isExporting" icon="pi pi-download" label="Export"
                                    severity="help" @click="onExport"/>
                            <InertiaLink :href="route('dashboard.authors.create')">
                                <Button icon="pi pi-plus" label="Add New"/>
                            </InertiaLink>
                        </div>
                        <ProgressBar :value="exportProgress" class="w-full"/>
                    </div>
                </template>
                <Column selectionMode="multiple"/>
                <Column field="id" header="#"/>
                <Column header="Avatar">
                    <template #body="{data}">
                        <Avatar :image="data.avatar"
                                :label="!data.avatar ? data.full_name.charAt(0).toUpperCase() : undefined"
                                shape="circle"
                                size="large"/>
                    </template>
                </Column>
                <Column field="full_name" header="Name"/>
                <Column header="Biography">
                    <template #body="{data}">
                        <p v-html="data.biography"/>
                    </template>
                </Column>
                <Column field="books_count" header="Number of Books"/>
                <Column header="Action(s)">
                    <template #body="{data}">
                        <div class="flex flex-wrap items-center gap-2">
                            <InertiaLink :href="route('dashboard.authors.edit', {slug: data.slug})">
                                <Button icon="pi pi-pencil" outlined rounded/>
                            </InertiaLink>
                            <Button icon="pi pi-trash" outlined rounded severity="danger" @click="onDelete(data.slug)"/>
                        </div>
                    </template>
                </Column>
                <template #footer>
                    <div class="flex justify-between items-center">
                    <span
                        class="text-xs text-surface-500 font-light">Showing {{
                            authors.pagination.count
                        }} entries</span>
                        <Paginator v-model:first="first" :rows="authors.pagination.perPage"
                                   :rowsPerPageOptions="paginatorRowsPerPageOptions"
                                   :template="paginatorTemplate"
                                   :totalRecords="authors.pagination.total"
                                   @page="onPageChange"
                        />
                    </div>
                </template>
                <template #empty>
                    <div class="flex flex-col justify-center items-center gap-2 text-surface-500">
                        <i class="pi pi-info-circle text-info text-4xl"/>
                        <p class="text-center text-lg">
                            Looks like there are no authors available. Please
                            <InertiaLink :href="route('dashboard.authors.create')"
                                         class="text-primary-500 hover:underline">add some
                                authors.
                            </InertiaLink>
                        </p>
                    </div>
                </template>
            </DataTable>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
