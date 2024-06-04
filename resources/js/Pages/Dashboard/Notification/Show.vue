<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";
import {ref} from "vue";

import Breadcrumb from "primevue/breadcrumb";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import {Notification} from "@/types";
import InertiaLink from "@/Components/InertiaLink.vue";

const props = defineProps<{
    notification: {
        data: Notification
    }
}>();

const home = ref({
    label: "Dashboard", route: "dashboard.index", active: route().current() === "dashboard.index"
});

const breadcrumbItems = ref([
    {
        label: "Notifications",
        route: "dashboard.notifications.index",
        active: route().current() === "dashboard.notifications.index"
    }, {
        label: props.notification.data.title,
        active: true,
    }
]);
</script>

<template>
    <Head :title="`Dashboard - Notifications - ${notification.data.title}`"/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800" v-text="notification.data.title"/>
                <Breadcrumb :home="home" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else class="text-surface-500 pointer-events-none">Details</span>
                    </template>
                </Breadcrumb>
            </div>
            <div class="flex justify-center items-center mt-4" v-html="notification.data.content"/>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>
</style>
