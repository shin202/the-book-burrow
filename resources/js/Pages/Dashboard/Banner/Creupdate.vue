<script lang="ts" setup>
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import {useToast} from "primevue/usetoast";

import Breadcrumb from "primevue/breadcrumb";
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import Button from "primevue/button";
import Toast from "primevue/toast";
import Image from "primevue/image";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";
import ImageUpload from "@/Components/ImageUpload.vue";
import {Banner} from "@/types";

interface BannerForm {
    title: string;
    description: string;
    link: string;
    image: File | null;
    status: "active" | "inactive";
}

const props = defineProps<{
    formType: "create" | "update",
    banner?: Banner
}>();

const page = usePage();
const toast = useToast();

const title = computed(() => props.formType === "create" ? "Create banner" : "Update banner");

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = ref([
    {label: "All banners", active: false, route: "dashboard.banners.index"},
    {label: props.formType === "create" ? "Create new banner" : "Edit banner", active: true}
]);

const form = useForm<BannerForm>({
    title: props.banner?.title ?? "",
    description: props.banner?.description ?? "",
    link: props.banner?.link ?? "",
    image: null,
    status: props.banner?.status ?? "active",
});

const isUpdating = ref(false);

const setUpdating = (value: boolean) => (isUpdating.value = value);

const onImageSelect = (file: File) => {
    form.image = file;
};

const createBannerHandler = () => {
    const url = route("dashboard.banners.store");

    form.post(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Created",
                detail: "Banner has been created successfully",
            });
            form.reset();
        }
    });
};

const updateBannerHandler = () => {
    setUpdating(true);
    const url = route("dashboard.banners.update", {id: props.banner?.id});

    router.post(url, {
        _method: "patch",
        ...form.data()
    }, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Updated",
                detail: "Banner has been updated successfully",
            });
            setUpdating(false);
        },
        onError: () => {
            setUpdating(false);
        }
    });
};

const onSubmit = () => {
    props.formType === "create" && createBannerHandler();
    props.formType === "update" && updateBannerHandler();
};
</script>

<template>
    <Head :title="title"/>
    <Toast/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Add a new banner</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else :class="{'text-surface-500 pointer-events-none': item.active}"
                              v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 mt-4 gap-4">
                <div class="form">
                    <div class="form__group">
                        <label class="form__label form__label--required" for="title">Banner title</label>
                        <InputText id="title" v-model="form.title" autofocus placeholder="Enter banner title"/>
                        <Transition name="fade">
                            <small v-if="form.errors.title ?? page.props.errors.title" class="form__feedback"
                                   v-text="form.errors.title ?? page.props.errors.title"/>
                        </Transition>
                    </div>
                    <div class="form__group">
                        <label class="form__label form__label--required" for="description">Banner description</label>
                        <Textarea id="description" v-model="form.description" placeholder="Enter banner description"
                                  rows="8"/>
                        <Transition name="fade">
                            <small v-if="form.errors.description ?? page.props.errors.description"
                                   class="form__feedback"
                                   v-text="form.errors.description ?? page.props.errors.description"/>
                        </Transition>
                    </div>
                    <div class="form__group">
                        <label class="form__label form__label--required" for="link">Banner link</label>
                        <InputText id="link" v-model="form.link" placeholder="Enter banner link"/>
                        <Transition name="fade">
                            <small v-if="form.errors.link ?? page.props.errors.link" class="form__feedback"
                                   v-text="form.errors.link ?? page.props.errors.link"/>
                        </Transition>
                    </div>
                </div>
                <div class="form">
                    <div class="flex flex-wrap gap-4">
                        <div v-if="formType === 'update'" class="form__group flex-1">
                            <span class="form__label">Old image</span>
                            <div class="flex justify-center items-center bg-surface-100 px-6 py-4 rounded h-full">
                                <Image :src="`../../${banner?.image}`" preview/>
                            </div>
                        </div>
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="image">Banner image</label>
                            <ImageUpload @fileSelect="onImageSelect"/>
                            <Transition name="fade">
                                <small v-if="form.errors.image ?? page.props.errors.image" class="form__feedback"
                                       v-text="form.errors.image ?? page.props.errors.image"/>
                            </Transition>
                        </div>
                    </div>
                    <div class="form__group">
                        <Button :disabled="form.processing || isUpdating" :loading="form.processing || isUpdating"
                                icon="pi pi-save" iconPos="right"
                                label="Save" @click="onSubmit"/>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
