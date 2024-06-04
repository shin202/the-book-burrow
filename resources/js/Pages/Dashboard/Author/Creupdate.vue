<script lang="ts" setup>
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import {computed, reactive, ref} from "vue";
import {useToast} from "primevue/usetoast";

import Breadcrumb from "primevue/breadcrumb";
import InputText from "primevue/inputtext";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import Toast from "primevue/toast";
import {CircleStencil, Cropper, CropperResult, Preview} from "vue-advanced-cropper";
import Editor from "ckeditor5-custom-build";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";

import SlugHelper from "@/common/shared/slug.helper";
import {Author} from "@/types";

const props = defineProps<{
    formType: "create" | "update"
    author?: Author
}>();

const toast = useToast();
const page = usePage();
const form = useForm({
    first_name: props.author?.first_name,
    last_name: props.author?.last_name,
    slug: props.author?.slug,
    biography: props.author?.biography,
    avatar: null,
});

const home = ref({
    label: "Dashboard", route: "dashboard.index", active: route().current() === "dashboard.index"
});
const breadcrumbItems = computed(() => [
    {label: "Author Lists", route: "dashboard.authors.index", active: false},
    {label: props.formType === "create" ? "Create" : "Edit", active: true}
]);
const formTitle = computed(() => props.formType === "create" ? "Create a new author" : `Edit ${props.author?.full_name}`);

const isShowAvatarCropper = ref(false);
const setShowAvatarCropper = (value: boolean) => {
    isShowAvatarCropper.value = value;
};

const avatarPreview = ref();
const onAvatarChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    avatarPreview.value = URL.createObjectURL(file);
    setShowAvatarCropper(true);
};

const avatarCropped = reactive<{ coordinates: any, image: any }>({
    coordinates: null,
    image: null,
});
const onCropperChange = ({coordinates, image}: CropperResult) => {
    avatarCropped.coordinates = coordinates;
    avatarCropped.image = image;
};

const resetAvatarCropper = () => {
    avatarCropped.coordinates = null;
    avatarCropped.image = null;
    avatarPreview.value = null;
};

const onCloseCropper = () => {
    resetAvatarCropper();
    setShowAvatarCropper(false);
};

const avatarCropperRef = ref();

const saveCroppedAvatar = (canvas: HTMLCanvasElement) => {
    canvas.toBlob((blob: any) => {
        form.avatar = blob;
    });
};

const onSaveCroppedAvatar = () => {
    const {canvas} = avatarCropperRef.value.getResult();
    if (!canvas) return;
    saveCroppedAvatar(canvas);
    setShowAvatarCropper(false);
};

const onInputName = () => {
    form.slug = SlugHelper.slugify(`${form.first_name} ${form.last_name}`);
};

const createAuthorHandler = () => {
    form.post(route("dashboard.authors.store"), {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Author created",
                detail: "Author has been created successfully",
                life: 3000
            });
            form.reset();
            resetAvatarCropper();
        }
    });
};

const updateAuthorHandler = () => {
    form.processing = true;
    const url = route("dashboard.authors.update", {slug: props.author?.slug});
    router.post(url, {
        _method: "patch",
        ...form.data(),
    }, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "Author has been updated successfully",
                life: 3000,
            });
            form.processing = false;
        },
        onError: (errors) => {
            form.processing = false;
        }
    });
};

const onSubmit = () => {
    props.formType === "create" && createAuthorHandler();
    props.formType === "update" && updateAuthorHandler();
};
</script>

<template>
    <Head title="Edit author"/>
    <Toast/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800" v-text="formTitle"/>
                <Breadcrumb :home="home" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route"
                                     :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"
                        />
                        <span v-else
                              :class="{'text-surface-500 pointer-events-none': item.active}"
                              v-text="item.label"
                        />
                    </template>
                </Breadcrumb>
            </div>
            <div class="form mt-4">
                <div class="flex flex-wrap gap-4">
                    <div class="form__group mr-10">
                        <label class="form__label form__label--optional" for="avatar">Avatar</label>
                        <div class="">
                            <input id="avatar" accept="image/*" class="hidden" type="file" @input="onAvatarChange"/>
                            <label v-tooltip.bottom="'Change avatar'"
                                   class="cursor-pointer flex justify-center items-center text-sm w-32 h-32 bg-surface-100 rounded-full border border-primary-300 text-surface-500"
                                   for="avatar">
                                <span v-if="!avatarCropped.image"
                                      :class="{'hidden': author?.avatar}"
                                      class="flex flex-col gap-1 justify-center items-center">
                                    <i class="pi pi-camera text-xl"/>
                                    <span>Choose avatar</span>
                                </span>
                                <Preview v-else :coordinates="avatarCropped.coordinates"
                                         :image="avatarCropped.image"
                                         class="w-full h-full rounded-full"/>
                                <img v-if="author?.avatar && !avatarCropped.image" :alt="author?.firstName"
                                     :src="author?.avatar"
                                     class="w-full h-full rounded-full"/>
                            </label>
                            <Transition name="fade">
                                <small v-if="page.props.errors.avatar" class="text-red-500 line-clamp-1"
                                       v-text="page.props.errors.avatar"/>
                            </Transition>
                            <Dialog v-model:visible="isShowAvatarCropper" :closable="true" class="max-w-96"
                                    header="Preview avatar" modal>
                                <Cropper ref="avatarCropperRef" :canvas="{
                                            width: 250,
                                            height: 250,
                                         }" :src="avatarPreview"
                                         :stencil-component="CircleStencil"
                                         :stencil-props="{
                                            aspectRatio: 1 ,
                                            movable: true,
                                            resizable: true,
                                         }"
                                         @change="onCropperChange"
                                />
                                <template #footer>
                                    <div class="flex justify-between items-center gap-2">
                                        <Button class="px-6 py-2" label="Cancel" severity="secondary"
                                                @click="onCloseCropper"/>
                                        <Button class="px-6 py-2" label="Save" severity="success"
                                                @click="onSaveCroppedAvatar"/>
                                    </div>
                                </template>
                            </Dialog>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 items-center flex-1">
                        <div class="form__group w-full">
                            <label class="form__label form__label--required" for="first-name">First name</label>
                            <InputText id="first-name" v-model="form.first_name"
                                       placeholder="Enter author's first name"
                                       @input="onInputName"
                            />
                            <Transition name="fade">
                                <small v-if="page.props.errors.first_name" class="text-red-500 line-clamp-1"
                                       v-text="page.props.errors.first_name"/>
                            </Transition>
                        </div>
                        <div class="form__group w-full">
                            <label class="form__label form__label--required" for="last-name">Last name</label>
                            <InputText id="last-name" v-model="form.last_name" placeholder="Enter author's last name"
                                       @input="onInputName"
                            />
                            <Transition name="fade">
                                <small v-if="page.props.errors.last_name" class="text-red-500 line-clamp-1"
                                       v-text="page.props.errors.last_name"/>
                            </Transition>
                        </div>
                        <div class="form__group w-full">
                            <label class="form__label form__label--required" for="slug">Slug</label>
                            <InputText id="slug" v-model="form.slug" placeholder="Enter author's slug"/>
                            <Transition name="fade">
                                <small v-if="page.props.errors.slug" class="text-red-500 line-clamp-1"
                                       v-text="page.props.errors.slug"/>
                            </Transition>
                        </div>
                    </div>
                </div>
                <div class="form__group">
                    <label class="form__label form__label--optional" for="biography">Biography</label>
                    <ckeditor id="biography" v-model="form.biography" :editor="Editor"/>
                </div>
                <div class="form__group">
                    <Button :disabled="form.processing" :loading="form.processing" icon="pi pi-save" iconPos="right"
                            label="Save"
                            type="button"
                            @click="onSubmit"
                    />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
