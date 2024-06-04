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
    {label: 'Genres List', route: 'dashboard.genres.index', active: false},
    {label: 'Create a new genre', active: true}
]);

const form = useForm({
    name: '',
    description: '',
    slug: ''
});

const onNameChange = () => {
    form.slug = SlugHelper.slugify(form.name);
}

const createGenreHandler = () => {
    const url = route('dashboard.genres.store');
    form.post(url, {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Created',
                detail: 'Genre has been created successfully',
                life: 3000
            });
            form.reset();
        }
    });
}

const onSubmit = () => {
    createGenreHandler();
}
</script>

<template>
    <Head title="Create a new genre"/>
    <Toast/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Create a new genre</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else :class="{'text-surface-500 pointer-events-none': item.active}"
                              v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <div class="form mt-4">
                <div class="form__group">
                    <label for="name" class="form__label form__label--required">Name</label>
                    <InputText v-model="form.name" id="name" placeholder="Enter genre name"
                               autofocus
                               @change="onNameChange"
                    />
                    <Transition name="fade">
                        <small v-if="form.errors.name" class="text-red-500 line-clamp-1"
                               v-text="form.errors.name"/>
                    </Transition>
                </div>
                <div class="form__group">
                    <label for="description" class="form__label form__label--required">Description</label>
                    <Editor v-model="form.description" editorStyle="height: 200px;"
                            placeholder="Enter description about genre"/>
                    <Transition name="fade">
                        <small v-if="form.errors.description" class="text-red-500 line-clamp-1"
                               v-text="form.errors.description"/>
                    </Transition>
                </div>
                <div class="form__group">
                    <label for="slug" class="form__label form__label--required">Slug</label>
                    <InputText v-model="form.slug" id="slug" placeholder="Enter genre slug"/>
                    <Transition name="fade">
                        <small v-if="form.errors.slug" class="text-red-500 line-clamp-1"
                               v-text="form.errors.slug"/>
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
