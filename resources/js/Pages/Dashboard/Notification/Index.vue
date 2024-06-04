<script lang="ts" setup>
import {Head, router} from "@inertiajs/vue3";
import {computed, ref} from "vue";

import Breadcrumb from "primevue/breadcrumb";
import Button from "primevue/button";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";
import {Notification, Pagination} from "@/types";
import {getEventIcon} from "@/common/enums";

const props = defineProps<{
    notifications: Pagination<Notification[]>
}>();

const home = ref({
    label: "Dashboard", route: "dashboard.index", active: route().current() === "dashboard.index"
});
const breadcrumbItems = ref([
    {
        label: "Notifications",
        route: "dashboard.notifications.index",
        active: true,
    }
]);

const lastRef = ref<HTMLElement | null>(null);

const notificationsData = computed(() => {
    return props.notifications.data.map((item) => {
        return {
            ...item,
            icon: getEventIcon(item.event)
        };
    });
});
const topNotifications = computed(() => notificationsData.value.slice(0, 2));

const onMarkAllAsRead = () => {
    router.get(route("dashboard.notifications.read-all"));
};
</script>

<template>
    <Head title="Dashboard - Notifications"/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Notifications</h1>
                <Breadcrumb :home="home" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <div v-if="notificationsData.length > 0" class="flex flex-col gap-4 mt-4">
                <Button class="self-end" label="Mark all as read" text @click="onMarkAllAsRead"/>
                <div class="flex flex-col gap-2">
                    <span class="text-lg font-medium">News</span>
                    <div class="flex flex-col gap-4">
                        <InertiaLink v-for="item in topNotifications" :key="item.id"
                                     :class="{ 'bg-surface-100 opacity-70': item.read_at}"
                                     :href="route('dashboard.notifications.show', item.id)"
                                     class="flex justify-between items-center gap-4 bg-primary-100 py-6 px-8 rounded-lg"
                        >
                            <i :class="item.icon"
                               class="text-2xl px-4 py-2 rounded-full w-14 h-14 text-surface-0 flex justify-center items-center"/>
                            <div class="flex flex-col space-y-2 flex-1">
                                <div class="flex justify-between items-center">
                                    <p class="font-semibold text-md" v-text="item.title"/>
                                    <p class="text-sm text-surface-500" v-text="item.created_at"/>
                                </div>
                                <p class="text-sm line-clamp-2" v-text="item.summary"/>
                            </div>
                        </InertiaLink>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <span class="text-lg font-medium">Previous</span>
                    <div class="flex flex-col gap-4">
                        <InertiaLink v-for="item in notificationsData.slice(2)" :key="item.id"
                                     :class="{ 'bg-surface-100 opacity-70': item.read_at}"
                                     :href="route('dashboard.notifications.show', item.id)"
                                     class="flex justify-between items-center gap-4 bg-primary-100 py-6 px-8 rounded-lg"
                        >
                            <i :class="item.icon"
                               class="text-2xl px-4 py-2 rounded-full w-14 h-14 text-surface-0 flex justify-center items-center"/>
                            <div class="flex flex-col space-y-2 flex-1">
                                <div class="flex justify-between items-center">
                                    <p class="font-semibold text-md" v-text="item.title"/>
                                    <p class="text-sm text-surface-500" v-text="item.created_at"/>
                                </div>
                                <p class="text-sm line-clamp-2" v-text="item.summary"/>
                            </div>
                        </InertiaLink>
                    </div>
                </div>
                <div ref="lastRef"></div>
            </div>
            <div v-else
                 class="mt-4 shadow-md rounded-lg bg-primary-0 px-6 py-4 flex flex-col gap-2 justify-center items-center">
                <i class="pi pi-info-circle text-5xl text-surface-500"/>
                <span class="font-bold">No notifications</span>
                <p>Looks like you have no <span class="text-primary-500">notifications</span> yet.</p>
            </div>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
