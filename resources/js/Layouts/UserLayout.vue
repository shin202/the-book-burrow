<script lang="ts" setup>
import {computed, ref} from "vue";
import {usePage} from "@inertiajs/vue3";

import Menu from "primevue/menu";
import Avatar from "primevue/avatar";
import {PageProps, User} from "@/types";
import InertiaLink from "@/Components/InertiaLink.vue";

const page = usePage<PageProps>();

const menuItems = ref<any>([
    {
        label: "Profile",
        items: [
            {
                label: "My information",
                icon: "pi pi-user",
                route: "my-profile.index",
                active: route().current() === "my-profile.index"
            },
            {
                label: "My orders",
                icon: "pi pi-shopping-cart",
                route: "my-profile.orders.index",
                active: route().current() === "my-profile.orders.index" || route().current() === "my-profile.orders.show"
            },
            {
                label: "Wishlist",
                icon: "pi pi-heart",
                active: route().current() === "my-profile.wishlist"
            },
            {
                label: "Settings",
                icon: "pi pi-cog",
                active: route().current() === "my-profile.settings"
            },
        ]
    },
    {
        label: "Security",
        items: [
            {
                label: "Change password",
                icon: "pi pi-lock",
                active: route().current() === "my-profile.change-password"
            },
        ]
    },
    {
        label: "Support",
        items: [
            {label: "Help", icon: "pi pi-question-circle"},
            {label: "Contact us", icon: "pi pi-envelope"},
        ]
    },
    {
        label: "Logout",
        icon: "pi pi-sign-out",
    }
]);
const isExpandedMenu = ref(false);

const user = computed<User>(() => {
    return page.props.auth.user;
});

const avatarLabel = computed(() => {
    return user.value.username.charAt(0).toUpperCase();
});

const onToggleMenu = () => {
    isExpandedMenu.value = !isExpandedMenu.value;
};
</script>

<template>
    <div class="flex flex-col md:flex-row min-h-screen relative">
        <Menu :class="isExpandedMenu ? 'max-h-[40rem]' : 'max-h-20'"
              :model="menuItems"
              :pt="{
                  'submenuheader': 'px-4 font-bold text-surface-500 py-2 text-sm'
              }"
              class="!w-full lg:!w-[30rem] !bg-primary-300 overflow-hidden md:w-80 md:max-h-screen sticky top-24 z-10 transition-all duration-200 ease-in-out">
            <template #start>
                <div class="flex justify-between items-center cursor-pointer md:pointer-events-none"
                     @click="onToggleMenu">
                    <div class="flex items-center space-x-2 p-2">
                        <Avatar :image="user.avatar" :label="avatarLabel"
                                class="!bg-primary-400 !text-surface-500 w-14 h-14 !text-xl"
                                shape="circle"
                                size="large"/>
                        <div>
                            <h2 class="text-lg font-bold">{{ user.username }}</h2>
                            <p class="text-gray-500">{{ user.email }}</p>
                        </div>
                    </div>
                    <i class="pi pi-chevron-down pr-8 text-xs md:hidden"/>
                </div>
            </template>
            <template #submenuheader="{item}">
                <div class="flex items-center space-x-2">
                    <span>{{ item.label }}</span>
                </div>
            </template>
            <template #item="{item, label}">
                <div :class="{'bg-primary-200 text-primary-800': item.active}"
                     class="hover:text-primary-800 hover:bg-primary-200"
                >
                    <InertiaLink v-if="item.route" :href="route(item.route)"
                                 class="flex items-center space-x-2 px-4 py-2">
                        <i :class="item.icon" class="text-xl"/>
                        <span>{{ label }}</span>
                    </InertiaLink>
                    <div v-else class="flex items-center space-x-2 px-4 py-2">
                        <i :class="item.icon" class="text-xl"/>
                        <span>{{ label }}</span>
                    </div>
                </div>
            </template>
        </Menu>
        <slot/>
    </div>
</template>

<style lang="scss" scoped>

</style>
