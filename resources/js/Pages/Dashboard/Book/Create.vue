<script lang="ts" setup>
import {Head, useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {useToast} from "primevue/usetoast";
import {useDebounceFn} from "@vueuse/core";

import Breadcrumb from "primevue/breadcrumb";
import InputText from "primevue/inputtext";
import Dropdown from "primevue/dropdown";
import MultiSelect from "primevue/multiselect";
import FileUpload, {FileUploadSelectEvent} from "primevue/fileupload";
import Button from "primevue/button";
import Image from "primevue/image";
import Calendar from "primevue/calendar";
import InputNumber from "primevue/inputnumber";
import Toast from "primevue/toast";
import Editor from "primevue/editor";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";

import {Author, Genre, Pagination, Publisher} from "@/types";
import SlugHelper from "@/common/shared/slug.helper";

defineProps<{
    authors: Pagination<Author[]>,
    genres: Pagination<Genre[]>,
    publishers: Pagination<Publisher[]>,
}>();

const toast = useToast();

const form = useForm({
    title: "",
    authors: [],
    genres: [],
    publisher_id: null,
    publication_date: "",
    number_of_pages: null,
    slug: "",
    description: "",
    cover_image: null,
    isbn: null,
    cost: null,
    price: null,
    quantity_in_stock: null,
});

const home = ref(
    {label: "Dashboard", route: "dashboard.index", active: false}
);
const breadcrumbItems = ref([
    {label: "Book Lists", route: "dashboard.books.index", active: false},
    {label: "Add new book", active: true}
]);

const fileUpload = ref();
const onFileSelect = (event: FileUploadSelectEvent) => {
    form.cover_image = event.files[0];
};

const onTitleInput = useDebounceFn(() => {
    form.slug = SlugHelper.slugify(form.title);
}, 500, {maxWait: 1000});

const onSubmit = () => {
    form.publication_date = window.dayjs(form.publication_date).format("YYYY-MM-DD");
    form.post(route("dashboard.books.store"), {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "Book has been created successfully.",
                life: 3000
            });
            form.reset();
            fileUpload.value.clear();
            fileUpload.value.uploadedFileCount = 0;
        }
    });
};
</script>

<template>
    <Head title="Dashboard - Add new book"/>
    <Toast/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Add a new book</h1>
                <Breadcrumb :home="home" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else :class="{'text-surface-500 pointer-events-none': item.active}"
                              v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                <div class="form">
                    <div class="form__group">
                        <label class="form__label form__label--required" for="title">Book title</label>
                        <InputText id="title" v-model="form.title" aria-labelledby="title-help"
                                   autofocus class="!bg-surface-0" placeholder="Enter book title"
                                   @input="onTitleInput"
                        />
                        <Transition>
                            <small v-if="form.errors.title" id="title-help"
                                   :title="form.errors.title"
                                   class="text-red-500 text-xs line-clamp-1"
                                   v-text="form.errors.title"/>
                        </Transition>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="authors">Author(s)</label>
                            <MultiSelect v-model="form.authors" :maxSelectedLabels="2"
                                         :options="authors.data"
                                         :virtualScrollerOptions="{itemSize: 40}"
                                         filter
                                         inputId="authors"
                                         optionLabel="full_name"
                                         optionValue="id"
                                         placeholder="Choose author(s)"
                            >
                                <template #footer>
                                    <div class="p-4 flex justify-between items-center">
                                        <InertiaLink :href="route('dashboard.authors.create')" class="w-full">
                                            <Button class="w-full" icon="pi pi-plus" iconPos="right"
                                                    label="Add new author"/>
                                        </InertiaLink>
                                    </div>
                                </template>
                            </MultiSelect>
                            <Transition>
                                <small v-if="form.errors.authors" :title="form.errors.authors"
                                       class="text-red-500 text-xs line-clamp-1" v-text="form.errors.authors"/>
                            </Transition>
                        </div>
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="genres">Genre(s)</label>
                            <MultiSelect v-model="form.genres"
                                         :maxSelectedLabels="2"
                                         :options="genres.data"
                                         :virtualScrollerOptions="{itemSize: 40}"
                                         filter
                                         inputId="genres"
                                         optionLabel="name"
                                         optionValue="id"
                                         placeholder="Choose genre(s)"
                            >
                                <template #footer>
                                    <div class="p-4 flex justify-between items-center">
                                        <InertiaLink :href="route('dashboard.genres.create')" class="w-full">
                                            <Button class="w-full" icon="pi pi-plus" iconPos="right"
                                                    label="Add new genre"/>
                                        </InertiaLink>
                                    </div>
                                </template>
                            </MultiSelect>
                            <Transition>
                                <small v-if="form.errors.genres" :title="form.errors.genres"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="form.errors.genres"/>
                            </Transition>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="publisher">Publisher</label>
                            <Dropdown v-model="form.publisher_id" :options="publishers.data"
                                      :virtualScrollerOptions="{itemSize: 40}"
                                      inputId="publisher" optionLabel="name"
                                      optionValue="id"
                                      placeholder="Choose publisher"
                            >
                                <template #footer>
                                    <div class="p-4 flex justify-between items-center">
                                        <InertiaLink :href="route('dashboard.publishers.create')" class="w-full">
                                            <Button class="w-full" icon="pi pi-plus" iconPos="right"
                                                    label="Add new publisher"/>
                                        </InertiaLink>
                                    </div>
                                </template>
                            </Dropdown>
                            <Transition>
                                <small v-if="form.errors.publisher_id" :title="form.errors.publisher_id"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="form.errors.publisher_id"/>
                            </Transition>
                        </div>
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="publication-date">Publication
                                date</label>
                            <Calendar v-model="form.publication_date" dateFormat="mm-dd-yy"
                                      inputClass="!bg-surface-0" inputId="publication-date"
                                      placeholder="Enter publication date" showIcon/>
                            <Transition>
                                <small v-if="form.errors.publication_date"
                                       :title="form.errors.publication_date"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="form.errors.publication_date"/>
                            </Transition>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="number-of-pages">Number of
                                pages</label>
                            <InputText id="number-of-pages" v-model="form.number_of_pages"
                                       class="!bg-surface-0" placeholder="Enter number of pages"/>
                            <small class="text-red-500 text-xs line-clamp-1" v-text="form.errors.number_of_pages"/>
                        </div>
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="slug">Slug</label>
                            <InputText id="slug" v-model="form.slug" class="!bg-surface-0"
                                       placeholder="Enter number of pages"/>
                            <Transition>
                                <small v-if="form.errors.slug"
                                       :title="form.errors.slug"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="form.errors.slug"/>
                            </Transition>
                        </div>
                    </div>
                    <div class="form__group">
                        <span class="form__label form__label--optional">Description</span>
                        <Editor v-model="form.description" editorStyle="height: 150px;"
                                placeholder="Enter description"/>
                    </div>
                </div>
                <div class="form">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="isbn">ISBN</label>
                            <InputText id="isbn" v-model="form.isbn" placeholder="Enter isbn"/>
                            <Transition>
                                <small v-if="form.errors.isbn"
                                       :title="form.errors.isbn"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="form.errors.isbn"/>
                            </Transition>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="cost">Cost</label>
                            <InputNumber v-model="form.cost" currency="USD" inputId="cost" locale="en-US"
                                         mode="currency"
                                         placeholder="Enter cost"/>
                            <Transition>
                                <small v-if="form.errors.cost"
                                       :title="form.errors.cost"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="form.errors.cost"/>
                            </Transition>
                        </div>
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="price">Price</label>
                            <InputNumber v-model="form.price" currency="USD" inputId="price" locale="en-US"
                                         mode="currency"
                                         placeholder="Enter price"/>
                            <Transition>
                                <small v-if="form.errors.price"
                                       :title="form.errors.price"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="form.errors.price"/>
                            </Transition>
                        </div>
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="cost">Quantity</label>
                            <InputNumber v-model="form.quantity_in_stock" inputId="cost"
                                         placeholder="Enter quantity"/>
                            <Transition>
                                <small v-if="form.errors.quantity_in_stock"
                                       :title="form.errors.quantity_in_stock"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="form.errors.quantity_in_stock"/>
                            </Transition>
                        </div>
                    </div>
                    <div class="form__group">
                        <span class="form__label form__label--required">Book cover</span>
                        <FileUpload ref="fileUpload" :fileLimit="1" :multiple="false" accept="image/*"
                                    @select="onFileSelect">
                            <template #header="{chooseCallback, clearCallback, files}">
                                <div class="flex flex-wrap justify-between items-center flex-1 gap-2">
                                    <div class="flex gap-2">
                                        <Button icon="pi pi-images" outlined rounded @click="chooseCallback()"/>
                                        <Button :disabled="!files || files.length === 0" icon="pi pi-times" outlined
                                                rounded
                                                severity="danger"
                                                @click="clearCallback()"/>
                                    </div>
                                </div>
                            </template>
                            <template #content="{files, removeFileCallback}">
                                <div v-if="files.length > 0" class="flex space-x-4">
                                    <div v-for="(file, index) of files" :key="file.name"
                                         class="border border-dotted border-primary-500">
                                        <div
                                            class="w-40 h-40 flex flex-col justify-center items-center space-y-4 relative group">
                                            <Image :alt="file.name" :src="file.objectURL" role="presentation"
                                                   width="100"/>
                                            <Button
                                                class="!absolute -bottom-5 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 ease-in-out"
                                                icon="pi pi-times" rounded
                                                severity="danger"
                                                @click="removeFileCallback(index)"/>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template #empty>
                                <div class="flex flex-col space-y-4">
                                    <div class="flex items-center justify-center flex-col text-surface-500">
                                        <i class="pi pi-cloud-upload border-2 p-5 text-8xl rounded-full text-primary-300"/>
                                        <p class="mt-4 mb-0">Drag and drop files to here to upload.</p>
                                    </div>
                                </div>
                            </template>
                        </FileUpload>
                        <Transition>
                            <small v-if="form.errors.cover_image"
                                   :title="form.errors.cover_image"
                                   class="text-red-500 text-xs line-clamp-1"
                                   v-text="form.errors.cover_image"/>
                        </Transition>
                    </div>
                    <div class="form__group">
                        <Button :disabled="form.processing" :loading="form.processing"
                                class="w-full" icon="pi pi-save" iconPos="right" label="Save"
                                @click="onSubmit"/>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
