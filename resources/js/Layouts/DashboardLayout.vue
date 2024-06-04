<script lang="ts" setup>
import {computed, ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";

import Menu from "primevue/menu";
import Menubar from "primevue/menubar";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Avatar from "primevue/avatar";
import ScrollPanel from "primevue/scrollpanel";
import OverlayPanel from "primevue/overlaypanel";
import Badge from "primevue/badge";

import LogoText from "@/Components/LogoText.vue";
import InertiaLink from "@/Components/InertiaLink.vue";
import RealtimeNotification from "@/Components/RealtimeNotification.vue";
import {useNotification} from "@/composables/use-notification";

const page = usePage();
const {unReadNotifications, unReadNotificationCount} = useNotification();

const user = computed(() => page.props.auth.user);
const avatarLabel = computed(() => user.value.username.charAt(0).toUpperCase());

const sidebarItems = computed(() => [
    {
        separator: true
    },
    {
        label: "Overview",
        items: [
            {
                label: "Sales Report",
                icon: "pi pi-chart-line",
                route: "dashboard.index",
                active: route().current() === "dashboard.index"
            },
            {
                label: "Notifications",
                icon: "pi pi-bell",
                badge: unReadNotificationCount.value,
                route: "dashboard.notifications.index",
                active: route().current() === "dashboard.notifications.index"
            }
        ]
    },
    {
        label: "Books",
        items: [
            {
                label: "Books List",
                icon: "pi pi-book",
                route: "dashboard.books.index",
                active: route().current() === "dashboard.books.index"
            },
            {
                label: "Add Book",
                icon: "pi pi-plus",
                route: "dashboard.books.create",
                active: route().current() === "dashboard.books.create"
            },
            {
                label: "Deals",
                icon: "pi pi-tag",
                route: "dashboard.deals.index",
                active: route().current() === "dashboard.deals.index"
            }
        ]
    },
    {
        label: "Coupons",
        items: [
            {
                label: "Coupons List",
                icon: "pi pi-tags",
                route: "dashboard.coupons.index",
                active: route().current() === "dashboard.coupons.index"
            },
            {
                label: "Add Coupon",
                icon: "pi pi-plus",
                route: "dashboard.coupons.create",
                active: route().current() === "dashboard.coupons.create"
            }
        ]
    },
    {
        label: "Authors",
        items: [
            {
                label: "Authors List",
                icon: "pi pi-users",
                route: "dashboard.authors.index",
                active: route().current() === "dashboard.authors.index"
            },
            {
                label: "Add Author",
                icon: "pi pi-user-plus",
                route: "dashboard.authors.create",
                active: route().current() === "dashboard.authors.create"
            }
        ]
    },
    {
        label: "Publishers",
        items: [
            {
                label: "Publishers List",
                icon: "pi pi-users",
                route: "dashboard.publishers.index",
                active: route().current() === "dashboard.publishers.index"
            },
            {
                label: "Add Publisher",
                icon: "pi pi-user-plus",
                route: "dashboard.publishers.create",
                active: route().current() === "dashboard.publishers.create"
            }
        ]
    },
    {
        label: "Genres",
        items: [
            {
                label: "Genres List",
                icon: "pi pi-th-large",
                route: "dashboard.genres.index",
                active: route().current() === "dashboard.genres.index"
            },
            {
                label: "New Genre",
                icon: "pi pi-plus",
                route: "dashboard.genres.create",
                active: route().current() === "dashboard.genres.create"
            }
        ]
    },
    {
        label: "Orders",
        items: [
            {
                label: "Orders List",
                icon: "pi pi-shopping-cart",
                route: "dashboard.orders.index",
                active: route().current() === "dashboard.orders.index"
            }
        ]
    },
    {
        label: "Users",
        items: [
            {
                label: "Users List",
                icon: "pi pi-users",
                route: "dashboard.users.index",
                active: route().current() === "dashboard.users.index"
            },
            {
                label: "Add New User",
                icon: "pi pi-user-plus",
                route: "dashboard.users.create",
                active: route().current() === "dashboard.users.create"
            }
        ]
    },
    {
        label: "Others",
        items: [
            {
                label: "Gallery",
                icon: "pi pi-images",
            },
            {
                label: "Banner",
                icon: "pi pi-image",
                route: "dashboard.banners.index",
                active: route().current() === "dashboard.banners.index"
            }
        ]
    },
    {
        label: "Settings",
        items: [
            {
                label: "Settings",
                icon: "pi pi-cog",
                route: "dashboard.settings.index",
                active: route().current() === "dashboard.settings.index"
            }
        ]
    },
    {
        separator: true
    }
]);

const isOpenSidebar = ref(false);
const notificationRef = ref();
const userOverlayMenuRef = ref();

const onToggleSidebar = () => {
    isOpenSidebar.value = !isOpenSidebar.value;
};

const onToggleNotification = (event: any) => {
    notificationRef.value.toggle(event);
};

const onMarkAllAsRead = () => {
    router.get(route("dashboard.notifications.read-all"));
};

const onToggleUserOverlayMenu = (event: any) => {
    userOverlayMenuRef.value.toggle(event);
};

const onLogout = () => {
    router.post(route("dashboard.logout"));
};
</script>

<template>
    <RealtimeNotification/>
    <div class="dashboard-container">
        <Menu :class="{'opened': isOpenSidebar}"
              :model="sidebarItems"
              class="dashboard__sidebar">
            <template #start>
                <div class="hidden lg:flex justify-center items-center space-x-4 px-1.5 py-3">
                    <LogoText class="!text-2xl"/>
                </div>
            </template>
            <template #item="{item}">
                <InertiaLink v-if="item.route" :class="{'bg-surface-100 text-primary-600': item.active}"
                             :href="route(item.route)"
                             class="flex items-center px-4 py-2 rounded-md"
                >
                    <i :class="item.icon" class="text-lg"/>
                    <span class="ml-2" v-text="item.label"/>
                    <Badge v-if="item.badge" :value="item.badge" class="ml-auto"/>
                </InertiaLink>
                <div v-else class="flex items-center px-4 py-2"
                >
                    <i v-if="item.icon" :class="item.icon" class="text-lg"/>
                    <span class="ml-2" v-text="item.label"/>
                    <Badge v-if="item.badge" :value="item.badge" class="ml-auto"/>
                </div>
            </template>
        </Menu>
        <main :class="{'translate-x-80': isOpenSidebar}"
              class="flex-1 relative transition-all duration-200 ease-out"
        >
            <Menubar class="dashboard__header">
                <template #start>
                    <div class="flex justify-center items-center gap-4">
                        <Button :icon="isOpenSidebar ? 'pi pi-times' : 'pi pi-bars'" class="!text-lg"
                                severity="secondary"
                                text
                                type="button" @click="onToggleSidebar"/>
                        <LogoText class="!text-xl lg:hidden"/>
                        <IconField class="hidden lg:block lg:w-96" iconPosition="right">
                            <InputIcon class="pi pi-search"/>
                            <InputText class="!bg-surface-0" placeholder="Search"/>
                        </IconField>
                    </div>
                </template>
                <template #end>
                    <div class="flex justify-center items-center space-x-16 divide-x divide-x-surface-300">
                        <div class="hidden lg:flex justify-center items-center gap-3">
                            <div class="">
                                <Button :badge="unReadNotificationCount" class="mr-6 w-full" icon="pi pi-bell"
                                        severity="secondary" text
                                        @click="onToggleNotification"/>
                                <OverlayPanel ref="notificationRef">
                                    <div class="flex flex-col gap-4 min-w-60">
                                        <div class="flex justify-between gap-2">
                                            <h2 class="text-lg font-bold">Notifications</h2>
                                            <InertiaLink :href="route('dashboard.notifications.index')"
                                                         class="text-primary-500 text-sm hover:underline">View
                                                all
                                            </InertiaLink>
                                        </div>
                                        <ScrollPanel v-if="unReadNotifications.length > 0" style="height: 260px">
                                            <div v-for="item in unReadNotifications"
                                                 :key="item.id"
                                                 class="relative my-2.5 bg-surface-100 px-4 py-2 rounded flex items-center gap-6 cursor-pointer hover:bg-surface-200 transition-all duration-200 ease-in-out">
                                                <i :class="item.icon"
                                                   class="text-2xl px-4 py-2 rounded-full w-14 h-14 text-surface-0 flex justify-center items-center"/>
                                                <div class="flex flex-col space-y-2 flex-1">
                                                    <div class="flex justify-between items-center">
                                                        <p class="font-semibold text-sm" v-text="item.title"/>
                                                        <p class="text-xs text-surface-500" v-text="item.created_at"/>
                                                    </div>
                                                    <p class="text-xs line-clamp-2" v-text="item.summary"/>
                                                </div>
                                                <i class="absolute top-2 right-2 w-2 h-2 rounded-full bg-primary-500 animate-ping"/>
                                            </div>
                                        </ScrollPanel>
                                        <p v-else
                                           class="text-center text-surface-500 flex flex-col justify-center items-center gap-4">
                                            <i class="pi pi-bell-slash text-5xl"/>
                                            <span>No new notifications</span>
                                        </p>
                                        <Button :disabled="unReadNotifications.length === 0" label="Mark all as read"
                                                text
                                                @click="onMarkAllAsRead"
                                        />
                                    </div>
                                </OverlayPanel>
                            </div>
                            <div>
                                <div class="flex gap-3 cursor-pointer" @click="onToggleUserOverlayMenu">
                                    <Avatar :image="user.avatar" :label="avatarLabel" shape="circle" size="large"/>
                                    <p class="flex flex-col">
                                        <span class="font-semibold text-md" v-text="user.username"/>
                                        <span class="text-xs text-surface-500">Admin</span>
                                    </p>
                                </div>
                                <OverlayPanel ref="userOverlayMenuRef">
                                    <div class="flex flex-col gap-2 divide-y">
                                        <div class="flex items-center gap-3">
                                            <Avatar :image="user.avatar" :label="user.username.charAt(0).toUpperCase()"
                                                    shape="circle"/>
                                            <div class="flex flex-col">
                                                <span class="font-bold text-md" v-text="user.username"/>
                                                <span class="text-sm text-surface-500" v-text="user.email"/>
                                            </div>
                                        </div>
                                        <ul class="flex flex-col space-y-2">
                                            <li class="py-1.5 px-2 rounded hover:bg-surface-100">
                                                <a class="flex items-center gap-2 w-full" href="#">
                                                    <i class="pi pi-cog"/>
                                                    <span>Settings</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <button
                                            class="flex items-center gap-2 w-full py-1.5 px-2 my-2 rounded hover:bg-surface-100"
                                            @click="onLogout"
                                        >
                                            <i class="pi pi-sign-out"/>
                                            <span>Logout</span>
                                        </button>
                                    </div>
                                </OverlayPanel>
                            </div>
                        </div>
                        <InertiaLink :href="route('dashboard.settings.index')">
                            <Button class="!text-lg" icon="pi pi-spin pi-cog" severity="secondary" text
                                    type="button"/>
                        </InertiaLink>
                    </div>
                </template>
            </Menubar>
            <ScrollPanel class="dashboard__content">
                <slot/>
            </ScrollPanel>
        </main>
    </div>
</template>

<style lang="scss">
:root {
    --primary-50: 243 251 253;
    --primary-100: 195 237 245;
    --primary-200: 148 224 237;
    --primary-300: 101 210 228;
    --primary-400: 53 196 220;
    --primary-500: 6 182 212;
    --primary-600: 5 155 180;
    --primary-700: 4 127 148;
    --primary-800: 3 100 117;
    --primary-900: 2 73 85;
    --primary-950: 1 48 58;

    --surface-0: 255 255 255;
    --surface-50: 249 250 251;
    --surface-100: 243 244 246;
    --surface-200: 229 231 235;
    --surface-300: 209 213 219;
    --surface-400: 156 163 175;
    --surface-500: 107 114 128;
    --surface-600: 75 85 99;
    --surface-700: 55 65 81;
    --surface-800: 31 41 55;
    --surface-900: 17 24 39;

    --primary: var(--primary-500);
}

::-webkit-scrollbar-track {
    background: rgb(var(--surface-100));
}

::-webkit-scrollbar-thumb {
    background-color: rgb(var(--surface-300));
    border-radius: 6px;
    border: 3px solid rgb(var(--surface-200));
}

html {
    @apply bg-surface-0;
}

.dashboard {
    &-container {
        @apply relative flex min-h-screen overflow-hidden;
    }

    &__sidebar {
        @apply w-80 fixed top-0 left-0 bottom-0 z-10 -translate-x-full transition-all duration-200 ease-out overflow-y-auto;
        &.opened {
            @apply -translate-x-0;
        }
    }
}
</style>
