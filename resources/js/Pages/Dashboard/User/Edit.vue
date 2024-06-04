<script lang="ts" setup>
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import iso31661a2 from "iso-3166-1-alpha-2";
import {useToast} from "primevue/usetoast";

import Breadcrumb from "primevue/breadcrumb";
import InputText from "primevue/inputtext";
import Fieldset from "primevue/fieldset";
import Password from "primevue/password";
import Dropdown from "primevue/dropdown";
import Calendar from "primevue/calendar";
import Button from "primevue/button";
import Toast from "primevue/toast";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";

import {Pagination, Role, User} from "@/types";
import {UserGenderEnum} from "@/common/enums";
import PasswordConfirmationDialog from "@/Components/PasswordConfirmationDialog.vue";

const props = defineProps<{
    roles: Pagination<Role[]>,
    user: User
}>();

const toast = useToast();
const page = usePage();

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = ref([
    {label: "All users", route: "dashboard.users.index", active: false},
    {label: "Edit user", active: true}
]);

const gendersList = ref([
    {label: "Male", value: UserGenderEnum.M},
    {label: "Female", value: UserGenderEnum.F},
    {label: "Other", value: UserGenderEnum.O}
]);

const isDialogVisible = ref(false);

const form = useForm({
    username: props.user.username,
    email: props.user.email,
    role_id: props.user.roleId,
    first_name: props.user.firstName,
    last_name: props.user.lastName,
    gender: props.user.gender,
    date_of_birth: window.dayjs(props.user.dateOfBirth).format("YYYY-MM-DD"),
    country: props.user.country,
    password: ""
});

const setDialogVisibility = (value: boolean) => {
    isDialogVisible.value = value;
};

const updateUserHandler = () => {
    form.date_of_birth = window.dayjs(form.date_of_birth).format("YYYY-MM-DD");
    const url = route("dashboard.users.update", {username: props.user.username});
    form.patch(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Updated",
                detail: "User has been updated successfully"
            });
        },
    });
};

const onSubmit = () => {
    updateUserHandler();
};

router.on("error", () => {
    const status = page.props.errors.status as unknown as number;
    if (status === 403) {
        setDialogVisibility(true);
    }
});
</script>

<template>
    <Head title="Edit user"/>
    <Toast/>
    <PasswordConfirmationDialog v-if="isDialogVisible" :callbackFn="updateUserHandler"
                                @closeDialog="setDialogVisibility(false)"/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Edit user {{ user.username }}</h1>
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
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="form__group flex-1">
                                <label class="form__label form__label--required" for="username">Username</label>
                                <InputText id="username" v-model="form.username" autofocus
                                           placeholder="Enter username" type="text"/>
                                <Transition name="fade">
                                    <small v-if="form.errors.username" class="form__feedback"
                                           v-text="form.errors.username"/>
                                </Transition>
                            </div>
                            <div class="form__group flex-1">
                                <label class="form__label form__label--required" for="email">Email</label>
                                <InputText id="email" v-model="form.email" placeholder="Enter email" type="email"/>
                                <Transition name="fade">
                                    <small v-if="form.errors.email" class="form__feedback"
                                           v-text="form.errors.email"/>
                                </Transition>
                            </div>
                        </div>
                        <div class="form__group">
                            <label class="form__label" for="password">Password</label>
                            <Password v-model="form.password" :feedback="false" inputId="password"
                                      placeholder="Enter password" toggleMask/>
                            <Transition name="fade">
                                <small v-if="form.errors.password" class="form__feedback"
                                       v-text="form.errors.password"/>
                            </Transition>
                        </div>
                        <div class="form__group">
                            <label class="form__label form__label--required" for="role">Role</label>
                            <Dropdown v-model="form.role_id"
                                      :options="roles.data"
                                      inputId="role"
                                      optionLabel="key"
                                      optionValue="id"
                                      placeholder="Choose role"
                            />
                            <Transition name="fade">
                                <small v-if="form.errors.role_id" class="form__feedback"
                                       v-text="form.errors.role_id"/>
                            </Transition>
                        </div>
                    </div>
                </Fieldset>
                <Fieldset legend="Others info">
                    <div class="form">
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="form__group flex-1">
                                <label class="form__label form__label--required" for="first-name">First name</label>
                                <InputText id="first-name" v-model="form.first_name" placeholder="Enter first name"/>
                                <Transition name="fade">
                                    <small v-if="form.errors.first_name" class="form__feedback"
                                           v-text="form.errors.first_name"/>
                                </Transition>
                            </div>
                            <div class="form__group flex-1">
                                <label class="form__label form__label--required" for="last-name">Last name</label>
                                <InputText id="last-name" v-model="form.last_name" placeholder="Enter last name"/>
                                <Transition name="fade">
                                    <small v-if="form.errors.last_name" class="form__feedback"
                                           v-text="form.errors.last_name"/>
                                </Transition>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4 items-center">
                            <div class="form__group flex-1">
                                <label class="form__label" for="gender">Gender</label>
                                <Dropdown v-model="form.gender"
                                          :options="gendersList"
                                          inputId="gender"
                                          optionLabel="label"
                                          optionValue="value"
                                          placeholder="Choose gender"/>
                                <Transition name="fade">
                                    <small v-if="form.errors.gender" class="form__feedback"
                                           v-text="form.errors.gender"/>
                                </Transition>
                            </div>
                            <div class="form__group flex-1">
                                <label class="form__label" for="date-of-birth">Date of
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
                                <label class="form__label" for="country">Country</label>
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
                            <Button :disabled="form.processing" :loading="form.processing" icon="pi pi-save"
                                    iconPos="right"
                                    label="Save" @click="onSubmit"/>
                        </div>
                    </div>
                </Fieldset>
            </div>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
