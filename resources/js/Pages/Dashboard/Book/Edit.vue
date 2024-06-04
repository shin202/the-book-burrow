<script lang="ts" setup>
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import {useToast} from "primevue/usetoast";

import Dropdown from "primevue/dropdown";
import Breadcrumb from "primevue/breadcrumb";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Toast from "primevue/toast";
import MultiSelect from "primevue/multiselect";
import Calendar from "primevue/calendar";
import Editor from "primevue/editor";
import Image from "primevue/image";
import FileUpload, {FileUploadSelectEvent} from "primevue/fileupload";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import {Author, Genre, Pagination, Publisher} from "@/types";
import InertiaLink from "@/Components/InertiaLink.vue";
import InputNumber from "primevue/inputnumber";
import {UpdateBookDto} from "@/common/dto";


const props = defineProps<{
    book: UpdateBookDto;
    authors: Pagination<Author[]>,
    genres: Pagination<Genre[]>,
    publishers: Pagination<Publisher[]>,
}>();

const toast = useToast();
const page = usePage();

const home = ref({
    label: "Dashboard", route: "dashboard.index", active: route().current() === "dashboard.index"
});
const breadcrumbItems = ref([
    {label: props.book.title, route: "dashboard.books.index", active: false},
    {label: "Edit", active: true},
]);


const form = useForm<UpdateBookDto>({
    title: props.book.title,
    authors: props.book.authors,
    genres: props.book.genres,
    publisher_id: props.book.publisher_id,
    publication_date: window.dayjs(props.book.publication_date).format("MM-DD-YYYY"),
    number_of_pages: props.book.number_of_pages,
    slug: props.book.slug,
    description: props.book.description,
    cover_image: undefined,
    isbn: props.book.isbn,
    cost: props.book.cost,
    price: props.book.price,
    quantity_in_stock: props.book.quantity_in_stock,
});

const onFileSelect = (event: FileUploadSelectEvent) => {
    form.cover_image = event.files[0];
};

const onSubmit = () => {
    form.publication_date = window.dayjs(form.publication_date).toDate();
    router.post(route("dashboard.books.update", {slug: props.book.slug}), {
        _method: "patch",
        ...form.data(),
    }, {
        onSuccess: () => {
            toast.add({severity: "success", summary: "Success", detail: "Book updated successfully.", life: 3000});
        },
    });
};
</script>

<template>
    <Head :title="`Edit ${book.title}`"/>
    <Toast/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Edit {{ book.title }}</h1>
                <Breadcrumb :home="home" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else class="text-surface-500 pointer-events-none" v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                <div class="form">
                    <div class="form__group">
                        <label class="form__label form__label--required" for="title">Title</label>
                        <InputText id="title" v-model="form.title"/>
                        <Transition name="fade">
                            <small v-if="page.props.errors.title" class="text-red-500 text-xs line-clamp-1"
                                   v-text="page.props.errors.title"/>
                        </Transition>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="authors">Author(s)</label>
                            <MultiSelect v-model="form.authors"
                                         :maxSelectedLabels="2"
                                         :options="authors.data"
                                         filter
                                         inputId="authors"
                                         optionLabel="full_name"
                                         optionValue="id"
                                         placeholder="Choose author(s)"
                            >
                                <template #footer>
                                    <div class="p-4 flex justify-between items-center">
                                        <Button class="w-full" icon="pi pi-plus" iconPos="right"
                                                label="Add new author"/>
                                    </div>
                                </template>
                            </MultiSelect>
                            <Transition>
                                <small v-if="page.props.errors.authors" :title="page.props.errors.authors"
                                       class="text-red-500 text-xs line-clamp-1" v-text="page.props.errors.authors"/>
                            </Transition>
                        </div>
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="genres">Genre(s)</label>
                            <MultiSelect v-model="form.genres"
                                         :maxSelectedLabels="2"
                                         :options="genres.data"
                                         inputId="genres"
                                         optionLabel="name"
                                         optionValue="id"
                                         placeholder="Choose genre(s)">
                                <template #footer>
                                    <div class="p-4 flex justify-between items-center">
                                        <Button class="w-full" icon="pi pi-plus" iconPos="right" label="Add new genre"/>
                                    </div>
                                </template>
                            </MultiSelect>
                            <Transition>
                                <small v-if="page.props.errors.genres" :title="page.props.errors.genres"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="page.props.errors.genres"/>
                            </Transition>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="publisher">Publisher</label>
                            <Dropdown v-model="form.publisher_id"
                                      :options="publishers.data"
                                      inputId="publisher"
                                      optionLabel="name" optionValue="id"
                                      placeholder="Choose publisher">
                                <template #footer>
                                    <div class="p-4 flex justify-between items-center">
                                        <Button class="w-full" icon="pi pi-plus" iconPos="right"
                                                label="Add new publisher"/>
                                    </div>
                                </template>
                            </Dropdown>
                            <Transition>
                                <small v-if="page.props.errors.publisher_id" :title="page.props.errors.publisher_id"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="page.props.errors.publisher_id"/>
                            </Transition>
                        </div>
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="publication-date">Publication
                                date</label>
                            <Calendar v-model="form.publication_date" dateFormat="mm-dd-yy"
                                      inputClass="!bg-surface-0" inputId="publication-date"
                                      placeholder="Enter publication date" showIcon/>
                            <Transition>
                                <small v-if="page.props.errors.publication_date"
                                       :title="page.props.errors.publication_date"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="page.props.errors.publication_date"/>
                            </Transition>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="number-of-pages">Number of
                                pages</label>
                            <InputNumber id="number-of-pages" v-model="form.number_of_pages"
                                         class="!bg-surface-0" placeholder="Enter number of pages"/>
                            <Transition>
                                <small v-if="page.props.errors.number_of_pages"
                                       :title="page.props.errors.number_of_pages"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="page.props.errors.number_of_pages"/>
                            </Transition>
                        </div>
                        <div class="form__group flex-1">
                            <label class="form__label form__label--required" for="slug">Slug</label>
                            <InputText id="slug" v-model="form.slug" class="!bg-surface-0"
                                       placeholder="Enter number of pages"/>
                            <Transition>
                                <small v-if="page.props.errors.slug"
                                       :title="page.props.errors.slug"
                                       class="text-red-500 text-xs line-clamp-1"
                                       v-text="page.props.errors.slug"/>
                            </Transition>
                        </div>
                    </div>
                    <div class="form__group">
                        <span class="form__label form__label--optional">Description</span>
                        <Editor v-model="form.description" editorStyle="height: 200px;"/>
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
                    <div class="flex flex-wrap gap-4">
                        <div class="form__group flex-1">
                            <span class="form__label">Old book cover</span>
                            <div class="flex justify-center items-center bg-surface-100 px-6 py-4 rounded h-full">
                                <Image :src="book.cover_image" preview width="150"/>
                            </div>
                        </div>
                        <div class="form__group flex-1">
                            <span class="form__label form__label--optional">New book cover</span>
                            <FileUpload ref="fileUpload" :fileLimit="1" :multiple="false" accept="image/*"
                                        @select="onFileSelect"
                            >
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
                                                <Image :alt="file.name" :src="file.objectURL" role="presentation"/>
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
                        </div>
                    </div>
                    <Transition name="fade">
                        <small v-if="page.props.errors.cover_image"
                               :title="page.props.errors.cover_image"
                               class="text-red-500 text-xs line-clamp-1"
                               v-text="page.props.errors.cover_image"/>
                    </Transition>
                    <small class="text-surface-500">Note: Old book cover will be delete and replace by new book
                        cover.</small>
                    <div class="form__group">
                        <Button icon="pi pi-save" iconPos="right" label="Save" @click="onSubmit"/>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
