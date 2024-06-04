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
import InputText from "primevue/inputtext";
import ProgressBar from "primevue/progressbar";
import FileUpload, {FileUploadUploaderEvent} from "primevue/fileupload";
import Badge from "primevue/badge";
import OverlayPanel from "primevue/overlaypanel";
import Toast from "primevue/toast";
import ConfirmPopup from "primevue/confirmpopup";
import Paginator, {PageState} from "primevue/paginator";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";

import {Pagination, Publisher} from "@/types";
import {humanFileSize} from "@/common/shared";
import {ModelStatusEnum} from "@/common/enums";
import {useDebounceFn} from "@vueuse/core";

const props = defineProps<{
    publishers: Pagination<Publisher[]>
}>();

const toast = useToast();
const confirm = useConfirm();

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = ref([
    {label: "Publishers List", route: "dashboard.publishers.index", active: true}
]);

const importOverlayRef = ref();

const selectedPublishers = ref<Publisher[]>([]);

const search = ref("");

const isDeleting = ref(false);
const isImporting = ref(false);
const isExporting = ref(false);

const paginatorTemplate = ref("RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink");
const paginatorRowsPerPageOptions = ref([5, 10, 20, 50]);
const first = ref((props.publishers.pagination.currentPage - 1) * props.publishers.pagination.perPage);


const onToggleImportOverlay = (event: any) => {
    importOverlayRef.value.toggle(event);
};

const setDeleting = (value: boolean) => (isDeleting.value = value);

const setImporting = (value: boolean) => (isImporting.value = value);

const setExporting = (value: boolean) => (isExporting.value = value);

const deletePublisherHandler = (slug: string | string[]) => {
    setDeleting(true);
    const url = route("dashboard.publishers.destroy", {slugs: slug});
    router.delete(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Deleted",
                detail: "Publisher(s) deleted successfully.",
                life: 3000,
            });
            setDeleting(false);
        }
    });
};

const onDeletePublisher = (event: Event, slug: string) => {
    confirm.require({
        target: <HTMLElement>event.currentTarget,
        group: "confirmDeletePopup",
        message: "Are you sure you want to delete this publisher?",
        icon: "pi pi-exclamation-triangle",
        rejectLabel: "Cancel",
        acceptLabel: "Yes, delete it",
        accept: () => deletePublisherHandler(slug)
    });
};

const onDeleteSelectedPublishers = () => {
    const slugs = selectedPublishers.value.map(publisher => publisher.slug);
    deletePublisherHandler(slugs);
};

const onImportPublishers = (event: FileUploadUploaderEvent) => {
    setImporting(true);
    importOverlayRef.value.hide();

    const url = route("dashboard.publishers.import");
    const file = (<File[]>event.files)[0];

    router.post(url, {file}, {
        onSuccess: () => {
            toast.add({
                severity: "info",
                summary: "Importing...",
                detail: "Importing publishers data, please wait a moment. We will notify you once it is done.",
                life: 5000,
            });
            setImporting(false);
        }
    });
};

const onExportPublishers = () => {
    setExporting(true);
    const url = route("dashboard.publishers.export");
    router.get(url, {}, {
        onSuccess: () => {
            toast.add({
                severity: "info",
                summary: "Exporting...",
                detail: "Exporting publishers data, please wait a moment. We will notify you once it is done.",
                life: 5000,
            });
            setExporting(false);
        }
    });
};

const onSearchPublisher = useDebounceFn(() => {
    const url = route(route().current()!);
    const params = {
        page: props.publishers.pagination.currentPage,
        perPage: props.publishers.pagination.perPage,
        search: search.value.toLowerCase(),
    };

    router.get(url, params, {
        replace: true,
        preserveState: true,
    });
}, 1000, {maxWait: 5000});

const onPageChange = ({page, rows}: PageState) => {
    const url = route(route().current()!);
    const params = {
        page: page + 1,
        perPage: rows,
        search: search.value.toLowerCase(),
    };

    router.get(url, params, {
        replace: true,
        preserveState: true,
    });
};
</script>

<template>
    <Head title="Publishers List"/>
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
                <h1 class="text-2xl font-semibold text-gray-800">Publishers List</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <DataTable v-model:selection="selectedPublishers" :value="publishers.data" class="max-w-[calc(100vw-3em)]"
                       dataKey="id"
                       removableSort scrollHeight="450px"
                       scrollable
                       stripedRows
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-center lg:justify-between">
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <Button :disabled="selectedPublishers.length === 0 || isDeleting" :loading="isDeleting"
                                    icon="pi pi-trash" severity="danger"
                                    @click="onDeleteSelectedPublishers"/>
                            <IconField iconPosition="right">
                                <InputIcon class="pi pi-search cursor-pointer hover:!text-primary-500"/>
                                <InputText v-model="search" placeholder="Search here..." type="text"
                                           @change="onSearchPublisher"/>
                            </IconField>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <div>
                                <Button :disabled="isImporting" :loading="isImporting" icon="pi pi-upload"
                                        label="Import"
                                        severity="info"
                                        @click="onToggleImportOverlay"/>
                                <OverlayPanel ref="importOverlayRef">
                                    <div class="">
                                        <FileUpload
                                            ref="fileImportRef"
                                            :customUpload="true"
                                            :fileLimit="1"
                                            :multiple="false"
                                            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                            @uploader="onImportPublishers"
                                        >
                                            <template #header="{chooseCallback, uploadCallback, clearCallback, files}">
                                                <div class="flex flex-col items-center gap-2">
                                                    <div class="flex items-center gap-2">
                                                        <Button icon="pi pi-file"
                                                                label="Choose file" @click="chooseCallback"/>
                                                        <Button
                                                            :disabled="!files || files.length === 0"
                                                            icon="pi pi-upload"
                                                            label="Import" severity="info"
                                                            @click="uploadCallback"/>
                                                        <Button
                                                            :disabled="!files || files.length === 0"
                                                            icon="pi pi-times"
                                                            label="Clear" severity="danger"
                                                            @click="clearCallback"/>
                                                    </div>
                                                </div>
                                            </template>
                                            <template
                                                #content="{files, removeFileCallback}">
                                                <div v-if="files.length > 0">
                                                    <div v-for="(file, index) in files" :key="file.name"
                                                         class="flex gap-2 items-center px-4 py-2 bg-surface-100 rounded">
                                                        <span class="font-semibold" v-text="file.name"/>
                                                        <span v-text="`${humanFileSize(file.size)}`"/>
                                                        <Badge severity="warning" value="Pending"/>
                                                        <Button icon="pi pi-times" severity="danger"
                                                                text @click="removeFileCallback(index)"/>
                                                    </div>
                                                </div>
                                            </template>
                                            <template #empty>
                                                <span>Drag and drop file here to import.</span>
                                            </template>
                                        </FileUpload>
                                    </div>
                                </OverlayPanel>
                            </div>
                            <Button :disabled="isExporting" :loading="isExporting" icon="pi pi-download" label="Export"
                                    severity="help" @click="onExportPublishers"/>
                            <InertiaLink :href="route('dashboard.publishers.create')">
                                <Button icon="pi pi-plus" label="Add new"/>
                            </InertiaLink>
                        </div>
                        <ProgressBar class="w-full"/>
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
                <Column field="contact_information" header="Contact information">
                    <template #body="{data}">
                        <div class="flex flex-col gap-2">
                            <span>
                                <span class="font-semibold">Email</span>:
                                <a :href="`mailto:${data?.email}`" class="text-primary-500">
                                    {{ data.email ?? "N/A" }}
                                </a>
                            </span>
                            <span>
                                <span class="font-semibold">Phone</span>:
                                <a :href="`tel:${data?.phone}`" class="text-primary-500">
                                    {{ data.phone ?? "N/A" }}
                                </a>
                            </span>
                            <span>
                                <span class="font-semibold">Website</span>:
                                <a :href="data?.website" class="text-primary-500">
                                    {{ data.website ?? "N/A" }}
                                </a>
                            </span>
                        </div>
                    </template>
                </Column>
                <Column header="Action(s)" style="min-width: 7rem;">
                    <template #body="{data}">
                        <div v-if="data.status !== ModelStatusEnum.DELETED" class="flex flex-wrap gap-2 items-center">
                            <InertiaLink :href="route('dashboard.publishers.edit', {slug: data.slug})">
                                <Button icon="pi pi-pencil" outlined rounded/>
                            </InertiaLink>
                            <Button :disabled="isDeleting" :loading="isDeleting" icon="pi pi-trash" outlined
                                    rounded
                                    severity="danger"
                                    @click="onDeletePublisher($event, data.slug)"/>
                        </div>
                        <Button v-else icon="pi pi-undo" outlined rounded severity="success"/>
                    </template>
                </Column>
                <template #footer>
                    <div class="flex justify-between items-center">
                    <span
                        class="text-xs text-surface-500 font-light">Showing {{
                            publishers.pagination.count
                        }} entries</span>
                        <Paginator v-model:first="first" :rows="publishers.pagination.perPage"
                                   :rowsPerPageOptions="paginatorRowsPerPageOptions"
                                   :template="paginatorTemplate"
                                   :totalRecords="publishers.pagination.total"
                                   @page="onPageChange"
                        />
                    </div>
                </template>
                <template #empty>
                    <div class="flex flex-col justify-center items-center gap-2 text-surface-500">
                        <i class="pi pi-info-circle text-info text-4xl"/>
                        <p class="text-center text-lg">No publishers found.</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
