<script lang="ts" setup>
import {Head, useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {useToast} from "primevue/usetoast";
import iso31661a2 from "iso-3166-1-alpha-2";

import Breadcrumb from "primevue/breadcrumb";
import Fieldset from "primevue/fieldset";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Dropdown from "primevue/dropdown";
import Calendar from "primevue/calendar";
import Button from "primevue/button";
import Toast from "primevue/toast";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";
import {UserGenderEnum} from "@/common/enums";
import {Pagination, Role} from "@/types";

defineProps<{
    roles: Pagination<Role[]>,
}>();

const toast = useToast();

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = ref([
    {label: "Add new user", active: true}
]);

const gendersList = Object.entries(UserGenderEnum).map(([key, value]) => ({label: key, value}));

const form = useForm({
    username: "",
    email: "",
    password: "",
    password_confirmation: "",
    role_id: "",
    first_name: "",
    last_name: "",
    gender: UserGenderEnum.M,
    date_of_birth: "",
    country: "",
});

const createUserHandler = () => {
    const url = route("dashboard.users.store");
    form.date_of_birth = window.dayjs(form.date_of_birth).format("YYYY-MM-DD");
    form.post(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Created",
                detail: "User has been created successfully",
                life: 3000
            });
            form.reset();
        }
    });
};

const onSubmit = () => {
    createUserHandler();
};
</script>

<template>
    <Head title="Add a new user"/>
    <Toast/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Add a new user</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else :class="{'text-surface-500 pointer-events-none': item.active}"
                              v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
                <Fieldset legend="Essential info">
                    <div class="form">
                        <div class="form__group">
                            <label class="form__label form__label--required" for="username">Username</label>
                            <InputText id="username" v-model="form.username" autofocus placeholder="Enter username"/>
                            <Transition name="fade">
                                <small v-if="form.errors.username" class="form__feedback"
                                       v-text="form.errors.username"/>
                            </Transition>
                        </div>
                        <div class="form__group">
                            <label class="form__label form__label--required" for="email">Email</label>
                            <InputText id="email" v-model="form.email" placeholder="Enter email"/>
                            <Transition name="fade">
                                <small v-if="form.errors.email" class="form__feedback" v-text="form.errors.email"/>
                            </Transition>
                        </div>
                        <div class="form__group">
                            <div class="flex flex-wrap gap-4 items-center">
                                <div class="form__group flex-1">
                                    <label class="form__label form__label--required" for="password">Password</label>
                                    <Password id="password" v-model="form.password" :feedback="false"
                                              placeholder="Enter password"
                                              toggleMask/>
                                </div>
                                <div class="form__group flex-1">
                                    <label class="form__label form__label--required" for="password_confirmation">Confirm
                                        password</label>
                                    <Password id="password_confirmation" v-model="form.password_confirmation"
                                              :feedback="false" placeholder="Confirm password"
                                              toggleMask/>
                                </div>
                            </div>
                            <Transition name="fade">
                                <small v-if="form.errors.password" class="form__feedback"
                                       v-text="form.errors.password"/>
                            </Transition>
                        </div>
                        <div class="form__group">
                            <label class="form__label form__label--required" for="role">Role</label>
                            <Dropdown v-model="form.role_id" :options="roles.data" inputId="role" optionLabel="key"
                                      optionValue="id" placeholder="Choose role"/>
                            <Transition name="fade">
                                <small v-if="form.errors.role_id" class="form__feedback" v-text="form.errors.role_id"/>
                            </Transition>
                        </div>
                    </div>
                </Fieldset>
                <Fieldset legend="Others info">
                    <div class="form">
                        <div class="flex flex-wrap gap-4 items-center">
                            <div class="form__group flex-1">
                                <label class="form__label form__label--required" for="first-name">First name</label>
                                <InputText id="first-name" v-model="form.first_name" placeholder="Enter first name"/>
                                <Transition name="fade">
                                    <small v-if="form.errors.first_name" class="form__feedback"
                                           v-text="form.errors.first_name"/>
                                </Transition>
                            </div>
                            <div class="form__group flex-1">
                                <label class="form__label form__label--required" for="last_name">Last name</label>
                                <InputText id="last_name" v-model="form.last_name" placeholder="Enter last name"/>
                                <Transition name="fade">
                                    <small v-if="form.errors.last_name" class="form__feedback"
                                           v-text="form.errors.last_name"/>
                                </Transition>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4 items-center">
                            <div class="form__group flex-1">
                                <label class="form__label form__label--optional" for="gender">Gender</label>
                                <Dropdown v-model="form.gender" :options="gendersList" inputId="gender"
                                          optionLabel="label"
                                          optionValue="value"
                                          placeholder="Choose gender"/>
                                <Transition name="fade">
                                    <small v-if="form.errors.gender" class="form__feedback"
                                           v-text="form.errors.gender"/>
                                </Transition>
                            </div>
                            <div class="form__group flex-1">
                                <label class="form__label form__label--optional" for="date-of-birth">Date of
                                    birth</label>
                                <Calendar v-model="form.date_of_birth" dateFormat="mm-dd-yy" inputId="date-of-birth"
                                          placeholder="Enter dob"
                                          showIcon/>
                                <Transition name="fade">
                                    <small v-if="form.errors.date_of_birth" class="form__feedback"
                                           v-text="form.errors.date_of_birth"/>
                                </Transition>
                            </div>
                            <div class="form__group flex-1">
                                <label class="form__label form__label--optional" for="country">Country</label>
                                <Dropdown v-model="form.country" :options="iso31661a2.getCountries()"
                                          :virtualScrollerOptions="{itemSize: 30}"
                                          filter
                                          inputId="country"
                                          placeholder="Choose nationality"
                                />
                                <Transition name="fade">
                                    <small v-if="form.errors.country" class="form__feedback"
                                           v-text="form.errors.country"/>
                                </Transition>
                            </div>
                        </div>
                        <div class="form__group">
                            <Button :disabled="form.processing" :loading="form.processing" icon="pi pi-save" iconPos="right"
                                    label="Save" @click="onSubmit"/>
                        </div>
                    </div>
                </Fieldset>
            </div>
        </div>
    </DashboardLayout>
</template>
