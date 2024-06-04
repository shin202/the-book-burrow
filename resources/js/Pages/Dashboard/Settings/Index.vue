<script lang="ts">
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

export default {
    layout: DashboardLayout
};
</script>

<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";
import {ref} from "vue";
import {useDateFormat} from "@vueuse/core";

import Breadcrumb from "primevue/breadcrumb";
import InputText from "primevue/inputtext";
import Dialog from "primevue/dialog";
import Image from "primevue/image";
import Button from "primevue/button";
import InputGroup from "primevue/inputgroup";
import Toast from "primevue/toast";
import Dropdown from "primevue/dropdown";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import {Cropper, CircleStencil} from "vue-advanced-cropper";

import InertiaLink from "@/Components/InertiaLink.vue";

import {useSetting} from "@/composables";
import {useToast} from "primevue/usetoast";

const toast = useToast();

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = [
    {label: "Settings", route: "dashboard.settings.index", active: true}
];

const {settings, setting, setSetting} = useSetting();

const logoCropperRef = ref();
const logoInputRef = ref();
const previewLogo = ref("");
const isShowLogoEditDialog = ref(false);

const modelOptions = [
    {label: "Item-based Collaborative Filtering", value: "item-based-cf"},
    {label: "Content-based Filtering", value: "content-based"},
];
const selectedModel = ref("item-based-cf");

const datasets = ref();
const isLoadingDataset = ref(false);
const isLoadingMoreDataset = ref(false);
const selectedDatasets = ref<any>();
const isTraining = ref(false);

const onCloseLogoEditDialog = () => {
    isShowLogoEditDialog.value = false;
};

const onShowLogoEditDialog = () => {
    isShowLogoEditDialog.value = true;
};

const onSelectLogo = () => {
    logoInputRef.value.click();
};

const onLogoChange = (event: Event) => {
    const file = (<HTMLInputElement>event.target).files?.[0];
    if (!file) return;
    previewLogo.value = URL.createObjectURL(file);
    onShowLogoEditDialog();
};

const onSaveLogo = () => {
    const {canvas} = logoCropperRef.value.getResult();
    if (!canvas) return;
    canvas.toBlob((blob: any) => {
        setSetting("site.logo", blob);
    });
};

const setLoadingDataset = (value: boolean) => (isLoadingDataset.value = value);

const setLoadingMoreDataset = (value: boolean) => (isLoadingMoreDataset.value = value);

const setTraining = (value: boolean) => (isTraining.value = value);

const onChangeModel = () => {
    datasets.value = null;
};

const loadDatasets = async () => {
    setLoadingDataset(true);

    const url = route("dashboard.recommendation.train.datasets");

    const response = await window.axios.get(url, {
        params: {
            model: selectedModel.value
        }
    });

    datasets.value = response.data;
    setLoadingDataset(false);
};

const loadMore = async () => {
    setLoadingMoreDataset(true);

    const url = datasets.value.next_page_url;

    if (!url) return;

    const response = await window.axios.get(url, {
        params: {
            model: selectedModel.value
        }
    });

    datasets.value = {
        ...response.data,
        data: [...datasets.value.data, ...response.data.data]
    };

    setLoadingMoreDataset(false);
};

const onTrainItemBased = async () => {
    setTraining(true);

    const url = route("dashboard.recommendation.train.item-based");

    const response = await window.axios.post(url, {
        ids: selectedDatasets.value ? selectedDatasets.value?.map((item: any) => item.id) : null
    });

    toast.add({
        severity: "info",
        summary: "Training success",
        detail: response.data.message
    });
    setTraining(false);
};

const onTrainContentBased = async () => {
    setTraining(true);

    const url = route("dashboard.recommendation.train.item-content");

    const response = await window.axios.post(url, {
        ids: selectedDatasets.value ? selectedDatasets.value?.map((item: any) => item.id) : null
    });

    toast.add({
        severity: "info",
        summary: "Training success",
        detail: response.data.message
    });

    setTraining(false);
};

const onTrain = () => {
    selectedModel.value === "item-based-cf" && onTrainItemBased();
    selectedModel.value === "content-based" && onTrainContentBased();
};
</script>

<template>
    <Toast/>
    <Head title="Dashboard - Settings"/>
    <div class="container mx-auto px-3 lg:px-6 mt-4">
        <div class="flex flex-wrap gap-2 justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-800">Settings</h1>
            <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                <template #item="{item}">
                    <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                 :href="route(item.route)" v-text="item.label"/>
                </template>
            </Breadcrumb>
        </div>
        <div class="mt-4 bg-surface-0 shadow-md rounded-lg">
            <div class="flex flex-col gap-4 px-4 py-2">
                <h2 class="font-bold text-lg">General settings</h2>
                <div class="flex flex-col gap-6">
                    <div class="flex flex-col gap-2 self-center relative">
                        <span class="line-clamp-1 font-medium text-sm lg:text-base">Site Logo</span>
                        <div class="w-60 h-60 rounded-full overflow-hidden">
                            <Image
                                preview
                                width="250"
                            >
                                <template #image>
                                    <img
                                        :src="setting('site.logo')"
                                        alt="Site logo"
                                        class="w-60 h-60 object-cover"/>
                                </template>
                                <template #preview="slotProps">
                                    <img
                                        :src="setting('site.logo')"
                                        :style="slotProps.style"
                                        alt="preview"
                                        @click="slotProps.previewCallback"/>
                                </template>
                            </Image>
                        </div>
                        <Button class="!absolute bottom-0 left-0" icon="pi pi-pencil" outlined rounded
                                severity="secondary" @click="onSelectLogo"/>
                        <input ref="logoInputRef" accept="image/*" hidden type="file" @change="onLogoChange"/>
                        <Dialog v-model:visible="isShowLogoEditDialog" class="max-w-96" header="Preview logo" modal>
                            <Cropper ref="logoCropperRef" :canvas="{ width: 250, height: 250}"
                                     :src="previewLogo"
                                     :stencilComponent="CircleStencil"
                                     :stencilProps="{
                                aspectRatio: 1,
                                movable: true,
                                resizable: true,
                             }"
                                     class="w-full h-full"
                            />
                            <template #footer>
                                <div class="flex justify-between items-center gap-2">
                                    <Button class="px-6 py-2" label="Cancel" severity="secondary"
                                            @click="onCloseLogoEditDialog"/>
                                    <Button class="px-6 py-2" label="Save" severity="success" @click="onSaveLogo"/>
                                </div>
                            </template>
                        </Dialog>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                        <div v-for="setting in settings" :key="setting.key" class="flex flex-col gap-2">
                            <label :for="setting.key" class="line-clamp-1 font-medium text-sm lg:text-base"
                                   v-text="setting.label"/>
                            <InputGroup>
                                <InputText :id="setting.key" v-model="setting.value" class="w-full"/>
                                <Button :disabled="setting.isLoading" :loading="setting.isLoading" label="Save"
                                        severity="success" @click="setSetting(setting.key, setting.value)"/>
                            </InputGroup>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-10 bg-surface-0 shadow-md rounded-lg">
            <div class="flex flex-col gap-4 px-4 py-2">
                <h2 class="font-bold text-lg">Recommendation Training</h2>
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 flex flex-col gap-2">
                        <label class="line-clamp-1 font-medium text-sm lg:text-base" for="algorithm">Model</label>
                        <Dropdown v-model="selectedModel" :options="modelOptions" class="w-full" inputId="algorithm"
                                  optionLabel="label" optionValue="value"
                                  @update:modelValue="onChangeModel"/>
                    </div>
                    <Button :disabled="isLoadingDataset" :loading="isLoadingDataset" class="mt-auto"
                            label="Load dataset" @click="loadDatasets"/>
                    <Button :disabled="!datasets || isTraining" :loading="isTraining" class="mt-auto" label="Train"
                            severity="success" @click="onTrain"/>
                    <Button class="mt-auto" label="Evaluate" severity="warning"/>
                </div>
            </div>
            <div class="px-4 py-6">
                <span class="font-bold">
                    Please select dataset to train the model
                    <span class="text-surface-500 font-normal">(Leave empty to use the entire datasets)</span>
                </span>
                <DataTable v-if="selectedModel === 'item-based-cf' && datasets" v-model:selection="selectedDatasets"
                           :value="datasets.data"
                           class="mt-4"
                           dataKey="id"
                           scrollHeight="400px"
                           scrollable stripedRows>
                    <Column selectionMode="multiple"/>
                    <Column field="username" header="Username"/>
                    <Column field="title" header="Title"/>
                    <Column field="rating" header="Rating"/>
                    <Column field="Created at" header="Created at">
                        <template #body="{data}">
                            <span>{{ useDateFormat(data.created_at, "MMM-DD-YYYY", {locales: "en-US"}).value }}</span>
                        </template>
                    </Column>
                    <template #footer>
                        <Button :disabled="isLoadingMoreDataset" :loading="isLoadingMoreDataset" class="mt-4 w-full"
                                label="Load more" @click="loadMore"/>
                    </template>
                </DataTable>
                <DataTable v-if="selectedModel === 'content-based' && datasets" v-model:selection="selectedDatasets"
                           :value="datasets.data"
                           class="mt-4" dataKey="id" scrollHeight="400px" scrollable stripedRows>
                    <Column selectionMode="multiple"/>
                    <Column field="title" header="Title"/>
                    <Column field="ratings_count" header="Ratings count"/>
                    <Column field="average_rating" header="Average rating"/>
                    <Column field="orders_count" header="Orders count"/>
                    <Column field="reviews_count" header="Reviews count"/>
                    <template #footer>
                        <Button :disabled="isLoadingMoreDataset" :loading="isLoadingMoreDataset" class="mt-4 w-full"
                                label="Load more" @click="loadMore"/>
                    </template>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>
