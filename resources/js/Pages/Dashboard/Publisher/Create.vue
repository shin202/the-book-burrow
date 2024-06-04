<script setup lang="ts">
import {Head, useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {useToast} from "primevue/usetoast";

import Breadcrumb from "primevue/breadcrumb";
import InputText from "primevue/inputtext";
import Editor from "primevue/editor";
import Button from "primevue/button";
import Toast from "primevue/toast";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";
import SlugHelper from "@/common/shared/slug.helper";

const toast = useToast();

const homeBreadcrumb = {label: 'Dashboard', route: 'dashboard.index', active: false};
const breadcrumbItems = ref([
    {label: 'Publishers List', route: 'dashboard.publishers.index', active: false},
    {label: 'Add new publisher', route: 'dashboard.publishers.create', active: true}
]);

const form = useForm({
    name: '',
    slug: '',
    email: '',
    website: '',
    phone: '',
    description: ''
});

const createPublisherHandler = () => {
    const url = route('dashboard.publishers.store');
    form.post(url, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Created',
                detail: 'Publisher created successfully',
                life: 3000
            })

            form.reset();
        }
    })
}

const onSubmit = () => {
    createPublisherHandler();
}

const onNameChange = () => {
    form.slug = SlugHelper.slugify(form.name);
}
</script>

<template>
    <Head title="Add new publisher"/>
    <Toast/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Add a new publisher</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink :class="{'text-surface-500 pointer-events-none': item.active}" v-if="item.route"
                                     :href="route(item.route)" v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <div class="form mt-4">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="form__group flex-1">
                        <label for="name" class="form__label form__label--required">Publisher Name</label>
                        <InputText v-model="form.name" type="text" id="name" placeholder="Enter publisher name"
                                   autofocus
                                   @change="onNameChange"/>
                        <Transition name="fade">
                            <small v-if="form.errors.name" class="text-red-500 line-clamp-1" v-text="form.errors.name"/>
                        </Transition>
                    </div>
                    <div class="form__group flex-1">
                        <label for="slug" class="form__label form__label--required">Slug</label>
                        <InputText v-model="form.slug" type="text" id="slug" placeholder="Enter publisher slug"/>
                        <Transition name="fade">
                            <small v-if="form.errors.slug" class="text-red-500 line-clamp-1" v-text="form.errors.slug"/>
                        </Transition>
                    </div>
                </div>
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="form__group flex-1">
                        <label for="email" class="form__label form__label--required">Email</label>
                        <InputText v-model="form.email" type="email" id="email" placeholder="Enter publisher email"/>
                        <Transition name="fade">
                            <small v-if="form.errors.email" class="text-red-500 line-clamp-1"
                                   v-text="form.errors.email"/>
                        </Transition>
                    </div>
                    <div class="form__group flex-1">
                        <label for="website" class="form__label form__label--optional">Website</label>
                        <InputText v-model="form.website" type="url" id="website"
                                   placeholder="Enter publisher website"/>
                        <Transition name="fade">
                            <small v-if="form.errors.website" class="text-red-500 line-clamp-1"
                                   v-text="form.errors.website"/>
                        </Transition>
                    </div>
                    <div class="form__group flex-1">
                        <label for="phone" class="form__label form__label--optional">Phone</label>
                        <InputText v-model="form.phone" type="tel" id="phone" placeholder="Enter publisher phone"/>
                        <Transition name="fade">
                            <small v-if="form.errors.phone" class="text-red-500 line-clamp-1"
                                   v-text="form.errors.phone"/>
                        </Transition>
                    </div>
                </div>
                <div class="form__group">
                    <label for="description" class="form__label form__label--required">Description</label>
                    <Editor v-model="form.description" editorStyle="height: 100px;"
                            placeholder="Enter description about publisher"/>
                    <Transition name="fade">
                        <small v-if="form.errors.description" class="text-red-500 line-clamp-1"
                               v-text="form.errors.description"/>
                    </Transition>
                </div>
                <div class="form__group">
                    <Button label="Save" icon="pi pi-save" iconPos="right" :disabled="form.processing"
                            :loading="form.processing" @click="onSubmit"/>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped lang="scss">

</style>
