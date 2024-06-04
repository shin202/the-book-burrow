<script lang="ts" setup>
import {Head, router, usePage} from "@inertiajs/vue3";
import {onBeforeMount, ref} from "vue";
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";

import Breadcrumb from "primevue/breadcrumb";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Avatar from "primevue/avatar";
import Tag from "primevue/tag";
import IconField from "primevue/iconfield";
import ProgressBar from "primevue/progressbar";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import InputIcon from "primevue/inputicon";
import Toast from "primevue/toast";
import Dropdown from "primevue/dropdown";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";
import ConfirmDeleteDialog from "@/Components/ConfirmDeleteDialog.vue";

import {Pagination, User} from "@/types";
import {UserStatusEnum} from "@/common/enums";
import Paginator from "primevue/paginator";
import {FilterMatchMode, FilterOperator} from "primevue/api";
import PasswordConfirmationDialog from "@/Components/PasswordConfirmationDialog.vue";
import {usePaginator, useSearch} from "@/composables";
import {useFilter} from "@/composables/use-filter";

const props = defineProps<{
    users: Pagination<User[]>
}>();

const page = usePage();
const toast = useToast();
const confirm = useConfirm();

const {search, onSearch} = useSearch();
const {paginatorTemplate, paginatorRowsPerPageOptions, first, onPageChange} = usePaginator(props.users);
const {onFilter, onClear} = useFilter();

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = ref([
    {label: "All users", route: "dashboard.users.index", active: true}
]);

const userStatusList = ref([
    {label: "active", value: UserStatusEnum.ACTIVE},
    {label: "inactive", value: UserStatusEnum.INACTIVE}
]);

const selectedUsers = ref<User[]>([]);

const isExporting = ref(false);
const isDeleting = ref(false);
const isPasswordConfirmVisible = ref(false);

const filters = ref();

const initFilters = () => {
    filters.value = {
        global: {value: null, matchMode: FilterMatchMode.CONTAINS},
        id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
        username: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
        email: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
        status: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
    };
};

const getStatusSeverity = (status: UserStatusEnum) => {
    const severity = {
        [UserStatusEnum.ACTIVE]: "success",
        [UserStatusEnum.INACTIVE]: "warning",
    };

    return severity[status];
};

const setExporting = (value: boolean) => (isExporting.value = value);

const setDeleting = (value: boolean) => (isDeleting.value = value);

const setPasswordConfirmVisible = (value: boolean) => (isPasswordConfirmVisible.value = value);

const setSelectedUsers = (value: User[] = []) => (selectedUsers.value = value);

const onExportUsers = () => {
    setExporting(true);
    const url = route("dashboard.users.export");

    router.get(url, {}, {
        onSuccess: () => {
            toast.add({
                severity: "info",
                summary: "Exporting users...",
                detail: "We are gathering the data. Please wait a moment, we will notify you once it is ready.",
                life: 3000,
            });
            setExporting(false);
        }
    });
};

const onClearFilter = () => {
    initFilters();
    onClear();
};

const onDeleteUserHandler = (username: string | string[]) => {
    setDeleting(true);
    const url = route("dashboard.users.destroy", {usernames: username});

    router.delete(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Deleted",
                detail: "User(s) has been deleted successfully.",
                life: 3000
            });
            setSelectedUsers();
            setDeleting(false);
        },
        onError: () => setDeleting(false)
    });
};

const onDeleteUser = (username: string) => {
    setSelectedUsers(props.users.data.filter(user => user.username === username));
    confirm.require({
        group: "confirmDeleteDialog",
        message: "Are you sure you want to delete this user? (Note: This action cannot be undone)",
        icon: "pi pi-exclamation-triangle",
        accept: () => onDeleteUserHandler(username)
    });
};

const onDeleteSelectedUsers = () => {
    const usernames = selectedUsers.value.map(user => user.username);
    confirm.require({
        group: "confirmDeleteDialog",
        message: "Are you sure you want to delete selected users? (Note: This action cannot be undone)",
        icon: "pi pi-exclamation-triangle",
        accept: () => onDeleteUserHandler(usernames)
    });
};

const deleteUserCallback = () => {
    const usernames = selectedUsers.value.map(user => user.username);
    onDeleteUserHandler(usernames);
};

router.on("error", () => {
    const status = page.props.errors.status as unknown as number;
    if (status === 403) {
        setPasswordConfirmVisible(true);
    }
});

onBeforeMount(() => {
    initFilters();
});
</script>

<template>
    <Head title="Users list"/>
    <Toast/>
    <ConfirmDeleteDialog/>
    <PasswordConfirmationDialog v-if="isPasswordConfirmVisible"
                                :callbackFn="deleteUserCallback" @closeDialog="setPasswordConfirmVisible(false)"/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">All users</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <DataTable v-model:filters="filters"
                       v-model:selection="selectedUsers"
                       :value="users.data"
                       class="max-w-[calc(100vw-3em)]"
                       dataKey="id" filterDisplay="menu"
                       lazy
                       scrollHeight="450px"
                       scrollable
                       stripedRows
                       @filter="onFilter"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-center lg:justify-between">
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <Button icon="pi pi-filter-slash"
                                    outlined
                                    severity="success"
                                    @click="onClearFilter"/>
                            <Button :disabled="selectedUsers.length === 0" :loading="isDeleting" icon="pi pi-trash"
                                    severity="danger" @click="onDeleteSelectedUsers"/>
                            <IconField iconPosition="right">
                                <InputIcon class="pi pi-search cursor-pointer hover:!text-primary-500"/>
                                <InputText v-model="search" placeholder="Search here..." type="text"
                                           @change="onSearch"/>
                            </IconField>
                        </div>
                        <div class="flex flex-wrap gap-2 items-center justify-center">
                            <Button :disabled="isExporting" :loading="isExporting" icon="pi pi-download" label="Export"
                                    severity="help" @click="onExportUsers"/>
                            <InertiaLink :href="route('dashboard.users.create')">
                                <Button icon="pi pi-plus" label="Add new"/>
                            </InertiaLink>
                        </div>
                        <ProgressBar class="w-full"/>
                    </div>
                </template>
                <Column selectionMode="multiple"/>
                <Column dataType="numeric" field="id" filterField="id" header="#">
                    <template #filter="{filterModel}">
                        <InputText v-model="filterModel.value" class="w-full" placeholder="Search by ID" type="text"/>
                    </template>
                </Column>
                <Column header="User" style="min-width: 10rem;">
                    <template #body="{data}">
                        <div class="">
                            <Avatar :image="data.avatar"
                                    :label="!data.avatar ? data.username.charAt(0).toUpperCase() : undefined"
                                    shape="circle"/>
                            <span class="ml-2 font-semibold" v-text="data.fullName ?? data.username"/>
                        </div>
                    </template>
                </Column>
                <Column field="username" header="Username">
                    <template #filter="{filterModel}">
                        <InputText v-model="filterModel.value" class="w-full" placeholder="Search by username"
                                   type="text"/>
                    </template>
                </Column>
                <Column filterField="email" header="Email">
                    <template #body="{data}">
                        <a :href="'mailto:' + data.email" class="text-primary-500 hover:underline" v-text="data.email"/>
                    </template>
                    <template #filter="{filterModel}">
                        <InputText v-model="filterModel.value" class="w-full" placeholder="Search by email"
                                   type="email"/>
                    </template>
                </Column>
                <Column header="Gender">
                    <template #body="{data}">
                        <span>{{ data.gender ?? "unknown" }}</span>
                    </template>
                </Column>
                <Column header="Age">
                    <template #body="{data}">
                        <span>{{ data.age ?? "unknown" }}</span>
                    </template>
                </Column>
                <Column header="Country">
                    <template #body="{data}">
                        <span>{{ data.country ?? "unknown" }}</span>
                    </template>
                </Column>
                <Column filterField="status" header="Status">
                    <template #body="{data}">
                        <Tag :severity="getStatusSeverity(data.status)" :value="data.status" v-text="data.status"/>
                    </template>
                    <template #filter="{filterModel}">
                        <Dropdown v-model="filterModel.value" :options="userStatusList" class="w-full"
                                  optionLabel="label" optionValue="value" placeholder="Select a status">
                            <template #option="{option}">
                                <Tag :severity="getStatusSeverity(option.value)" :value="option.value"
                                />
                            </template>
                        </Dropdown>
                    </template>
                </Column>
                <Column header="Action(s)">
                    <template #body="{data}">
                        <div class="flex flex-wrap gap-2 items-center">
                            <Button icon="pi pi-eye" outlined rounded severity="success" text/>
                            <InertiaLink :href="route('dashboard.users.edit', {username: data.username})">
                                <Button icon="pi pi-pencil" outlined rounded text/>
                            </InertiaLink>
                            <Button :disabled="isDeleting" :loading="isDeleting" icon="pi pi-trash" outlined
                                    rounded severity="danger" text
                                    @click="onDeleteUser(data.username)"/>
                        </div>
                    </template>
                </Column>
                <template #footer>
                    <div class="flex justify-between items-center">
                    <span
                        class="text-xs text-surface-500 font-light">Showing {{
                            users.pagination.count
                        }} entries</span>
                        <Paginator v-model:first="first" :rows="users.pagination.perPage"
                                   :rowsPerPageOptions="paginatorRowsPerPageOptions"
                                   :template="paginatorTemplate"
                                   :totalRecords="users.pagination.total"
                                   @page="onPageChange"
                        />
                    </div>
                </template>
                <template #empty>
                    <div class="flex flex-col justify-center items-center gap-2 text-surface-500">
                        <i class="pi pi-info-circle text-info text-4xl"/>
                        <p class="text-center text-lg">No users found.</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
