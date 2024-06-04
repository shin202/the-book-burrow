<script lang="ts" setup>
import {Head, router} from "@inertiajs/vue3";
import {ref} from "vue";
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";

import Breadcrumb from "primevue/breadcrumb";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import ProgressBar from "primevue/progressbar";
import InputText from "primevue/inputtext";
import Paginator from "primevue/paginator";
import Toast from "primevue/toast";
import ConfirmPopup from "primevue/confirmpopup";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";

import {Genre, Pagination} from "@/types";
import {usePaginator, useSearch} from "@/composables";
import {useExport} from "@/composables/use-export";
import FileImport from "@/Components/FileImport.vue";

const props = defineProps<{
    genres: Pagination<Genre[]>
}>();

const toast = useToast();
const confirm = useConfirm();

const {search, onSearch} = useSearch();
const {paginatorRowsPerPageOptions, paginatorTemplate, onPageChange, first} = usePaginator(props.genres);
const {onExport, isExporting, exportProgress} = useExport("dashboard.genres.export", "genres");

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = ref([
    {label: "Genres List", route: "dashboard.genres.index", active: true}
]);

const selectedGenres = ref<Genre[]>([]);

const isDeleting = ref(false);

const setDeleting = (value: boolean) => (isDeleting.value = value);

const deleteGenreHandler = (slug: string | string[]) => {
    setDeleting(true);
    const url = route("dashboard.genres.destroy", {slugs: slug});
    router.delete(url, {
        preserveState: true,
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Deleted",
                detail: "Genre(s) deleted successfully.",
                life: 3000
            });
            setDeleting(false);
        }
    });
};

const onDeleteGenre = (event: Event, slug: string) => {
    confirm.require({
        target: <HTMLElement>event.currentTarget,
        group: "confirmDeletePopup",
        message: "Are you sure you want to delete this genre?",
        icon: "pi pi-exclamation-triangle",
        rejectLabel: "Cancel",
        acceptLabel: "Yes, delete it",
        accept: () => deleteGenreHandler(slug)
    });
};

const onDeleteSelectedGenres = (event: Event) => {
    const slugs = selectedGenres.value.map(genre => genre.slug);
    confirm.require({
        target: <HTMLElement>event.currentTarget,
        group: "confirmDeletePopup",
        message: `Are you sure you want to delete (${selectedGenres.value.length}) selected genres?`,
        icon: "pi pi-exclamation-triangle",
        rejectLabel: "Cancel",
        acceptLabel: "Yes, delete them",
        accept: () => deleteGenreHandler(slugs)
    });
};
</script>

<template>
    <Head title="Genres list"/>
    <Toast/>
    <ConfirmPopup group="confirmDeletePopup">
        <template #container="{message, acceptCallback, rejectCallback}">
            <div class="flex flex-col items-center p-4 rounded">
                <div class="text-red-500 flex justify-center items-center gap-2 border-b py-1.5 w-full">
                    <i class="pi pi-exclamation-triangle text-warning text-4xl"/>
                    <span class="text-md lg:text-lg">{{ message.header ?? "Danger Zone" }}</span>
                </div>
                <p class="mt-2 px-4 py-6" v-text="message.message"/>
                <div class="flex items-center gap-4 w-full">
                    <Button :label="message.rejectLabel ?? 'Cancel'" class="flex-1" severity="secondary"
                            @click="rejectCallback"/>
                    <Button :label="message.acceptLabel ?? 'Yes'" class="flex-1" severity="danger"
                            @click="acceptCallback"/>
                </div>
            </div>
        </template>
    </ConfirmPopup>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Genres List</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <DataTable v-model:selection="selectedGenres" :value="genres.data" class="max-w-[calc(100vw-3em)]"
                       dataKey="id"
                       removableSort
                       scrollHeight="450px" scrollable
                       stripedRows
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-center lg:justify-between">
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <Button v-tooltip.bottom="`Delete ${selectedGenres.length} items`"
                                    :disabled="selectedGenres.length === 0 || isDeleting"
                                    :loading="isDeleting"
                                    icon="pi pi-trash"
                                    severity="danger"
                                    @click="onDeleteSelectedGenres"
                            />
                            <IconField iconPosition="right">
                                <InputIcon class="pi pi-search cursor-pointer hover:!text-primary-500"/>
                                <InputText v-model="search" placeholder="Search here..." type="text"
                                           @change="onSearch"/>
                            </IconField>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <FileImport importer="genres" routeName="dashboard.genres.import"/>
                            <Button :disabled="isExporting" :loading="isExporting" icon="pi pi-download" label="Export"
                                    severity="help" @click="onExport"/>
                            <InertiaLink :href="route('dashboard.genres.create')">
                                <Button icon="pi pi-plus" label="Add new"/>
                            </InertiaLink>
                        </div>
                        <ProgressBar :value="exportProgress" class="w-full"/>
                    </div>
                </template>
                <Column selectionMode="multiple"/>
                <Column field="id" header="#"/>
                <Column field="name" header="Name"/>
                <Column header="Description">
                    <template #body="{data}">
                        <p v-html="data.description"/>
                    </template>
                </Column>
                <Column header="Action(s)">
                    <template #body="{data}">
                        <div class="flex flex-wrap gap-2 items-center">
                            <InertiaLink :href="route('dashboard.genres.edit', {slug: data.slug})">
                                <Button icon="pi pi-pencil" outlined rounded/>
                            </InertiaLink>
                            <Button :disabled="isDeleting" :loading="isDeleting" icon="pi pi-trash" outlined
                                    rounded
                                    severity="danger"
                                    @click="onDeleteGenre($event, data.slug)"
                            />
                        </div>
                    </template>
                </Column>
                <template #footer>
                    <div class="flex justify-between items-center">
                    <span
                        class="text-xs text-surface-500 font-light">Showing {{
                            genres.pagination.count
                        }} entries</span>
                        <Paginator v-model:first="first" :rows="genres.pagination.perPage"
                                   :rowsPerPageOptions="paginatorRowsPerPageOptions"
                                   :template="paginatorTemplate"
                                   :totalRecords="genres.pagination.total"
                                   @page="onPageChange"
                        />
                    </div>
                </template>
                <template #empty>
                    <div class="flex flex-col justify-center items-center gap-2 text-surface-500">
                        <i class="pi pi-info-circle text-info text-4xl"/>
                        <p class="text-center text-lg">No genres found.</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
