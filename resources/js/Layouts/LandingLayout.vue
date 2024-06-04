<script lang="ts" setup>
import {computed, ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";

import Toast from "primevue/toast";
import Button from "primevue/button";
import Sidebar from "primevue/sidebar";
import OverlayPanel from "primevue/overlaypanel";
import Avatar from "primevue/avatar";
import AutoComplete from "primevue/autocomplete";
import InputGroup from "primevue/inputgroup";

import LogoText from "@/Components/LogoText.vue";
import TheLandingFooter from "@/Components/TheLandingFooter.vue";
import InertiaLink from "@/Components/InertiaLink.vue";
import Cart from "@/Components/Cart.vue";

import {useSearch} from "@/composables";

defineProps<{
    isAuthenticated: boolean;
}>();

const page = usePage<any>();
const {onLiveSearch, search, suggestions} = useSearch();

const user = computed(() => page.props.auth.user);

const isOpenNavigationMenu = ref(false);
const userOverlayMenuRef = ref();

const onToggleNavigationMenu = () => {
    isOpenNavigationMenu.value = !isOpenNavigationMenu.value;
};

const onToggleUserOverlayMenu = (event: any) => {
    userOverlayMenuRef.value.toggle(event);
};

const onLogout = () => {
    router.post(route("logout"));
};
</script>

<template>
    <Toast position="bottom-right"/>
    <Sidebar v-model:visible="isOpenNavigationMenu" class="!bg-primary-200">
        <template #container="{closeCallback}">
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between py-3 px-4 bg-primary-300">
                    <span class="font-bold text-lg">Main menu</span>
                    <button class="w-8 h-8 p-2 rounded-full hover:bg-primary-200 flex justify-center items-center"
                            @click="closeCallback">
                        <i class="pi pi-times text-xs"/>
                    </button>
                </div>
                <ul class="flex flex-col px-4">
                    <li class="py-2 capitalize font-bold text-lg hover:text-primary-800 relative flex before:absolute before:w-20 before:h-0.5 before:bg-primary-800 before:bottom-0 before:scale-x-0 hover:before:scale-x-100 before:transition-transform before:duration-500 before:ease-out before:origin-left">
                        <InertiaLink :href="route('landing.index')" class="flex-1">Home</InertiaLink>
                    </li>
                    <li class="py-2 capitalize font-bold text-lg hover:text-primary-800 relative flex before:absolute before:w-20 before:h-0.5 before:bg-primary-800 before:bottom-0 before:scale-x-0 hover:before:scale-x-100 before:transition-transform before:duration-500 before:ease-out before:origin-left">
                        <InertiaLink :href="route('shop.index')" class="flex-1">Shop</InertiaLink>
                    </li>
                    <li class="py-2 capitalize font-bold text-lg relative flex flex-col">
                        <span class="flex-1">Pages</span>
                        <ul class="flex flex-col px-4">
                            <li class="py-2 capitalize font-bold text-lg hover:text-primary-800 relative flex before:absolute before:w-20 before:h-0.5 before:bg-primary-800 before:bottom-0 before:scale-x-0 hover:before:scale-x-100 before:transition-transform before:duration-500 before:ease-out before:origin-left">
                                <InertiaLink :href="route('authors.index')" class="flex-1">Authors</InertiaLink>
                            </li>
                        </ul>
                    </li>
                    <li class="py-2 capitalize font-bold text-lg hover:text-primary-800 relative flex before:absolute before:w-20 before:h-0.5 before:bg-primary-800 before:bottom-0 before:scale-x-0 hover:before:scale-x-100 before:transition-transform before:duration-500 before:ease-out before:origin-left">
                        <a class="flex-1" href="#">Blog</a>
                    </li>
                    <li class="py-2 capitalize font-bold text-lg hover:text-primary-800 relative flex before:absolute before:w-20 before:h-0.5 before:bg-primary-800 before:bottom-0 before:scale-x-0 hover:before:scale-x-100 before:transition-transform before:duration-500 before:ease-out before:origin-left">
                        <a class="flex-1" href="#">Contact</a>
                    </li>
                </ul>
            </div>
        </template>
    </Sidebar>
    <header class="header">
        <div class="header__inner">
            <Button class="mr-2" icon="pi pi-bars" plain size="large" text
                    @click="onToggleNavigationMenu"/>
            <LogoText class="!text-xl lg:!text-3xl"/>
            <div class="flex-1 px-16 justify-center items-center gap-2 hidden lg:flex">
                <!--                <button-->
                <!--                    class="lg:hidden xl:flex px-6 py-3 min-w-48 bg-primary-800 text-surface-0 text-md justify-center items-center rounded-full">-->
                <!--                    <div class="flex gap-2 justify-center items-center">-->
                <!--                        <i class="pi pi-th-large"/>-->
                <!--                        <span>Genres</span>-->
                <!--                    </div>-->
                <!--                    <i class="pi pi-chevron-down ml-auto text-xs"/>-->
                <!--                </button>-->
                <div class="flex-1 relative">
                    <InputGroup iconPosition="right">
                        <AutoComplete v-model="search" :suggestions="suggestions"
                                      :virtualScrollerOptions="{ itemSize: 120 }"
                                      class="w-full"
                                      inputClass="!w-full !rounded-l-full !bg-primary-300 !py-3" optionLabel="title"
                                      placeholder="Search" scrollHeight="400px"
                                      @change="onLiveSearch">
                            <template #option="{option}">
                                <InertiaLink :href="route('books.show', option.slug)">
                                    <div class="flex gap-4 py-4">
                                        <div class="w-14 h-20 overflow-hidden rounded-lg">
                                            <img :src="option.cover_image" alt="" class="w-full h-full object-cover"/>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <h3 class="font-bold line-clamp-2 font-serif" v-text="option.title"/>
                                            <InertiaLink :href="route('authors.show', {slug: option.author.slug})"
                                                         class="w-fit text-sm text-surface-500 hover:text-primary hover:underline"
                                                         v-text="option.author.full_name"/>
                                        </div>
                                    </div>
                                </InertiaLink>
                            </template>
                            <template #footer>
                                <div class="p-2 flex justify-center items-center">
                                    <InertiaLink :href="route('search.index', {query: search})"
                                                 class="text-primary-800 hover:underline">View all results for {{
                                            search
                                        }}
                                    </InertiaLink>
                                </div>
                            </template>
                        </AutoComplete>
                        <Button icon="pi pi-search"/>
                    </InputGroup>
                </div>
            </div>
            <div class="flex justify-center items-center divide-x">
                <div>
                    <Button class="min-w-14 hidden lg:flex" icon="pi pi-user" size="large" text
                            @click="onToggleUserOverlayMenu"
                    />
                    <OverlayPanel ref="userOverlayMenuRef">
                        <div v-if="isAuthenticated" class="flex flex-col gap-2 divide-y">
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
                                    <InertiaLink :href="route('my-profile.index')"
                                                 class="flex items-center gap-2 w-full">
                                        <i class="pi pi-user"/>
                                        <span>Profile</span>
                                    </InertiaLink>
                                </li>
                                <li class="py-1.5 px-2 rounded hover:bg-surface-100">
                                    <InertiaLink :href="route('my-profile.orders.index')"
                                                 class="flex items-center gap-2 w-full">
                                        <i class="pi pi-shopping-cart"/>
                                        <span>My orders</span>
                                    </InertiaLink>
                                </li>
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
                        <InertiaLink v-else
                                     :href="route('login')"
                                     class="flex items-center gap-2 hover:bg-surface-100 px-2 py-1 rounded"
                        >
                            <i class="pi pi-sign-in"/>
                            <span>Login</span>
                        </InertiaLink>
                    </OverlayPanel>
                </div>
                <Button class="min-w-14 hidden lg:flex" icon="pi pi-heart" size="large" text/>
                <Cart class="px-2"/>
            </div>
        </div>
    </header>
    <slot/>
    <div class="dock-menu lg:hidden">
        <div class="dock-menu__inner flex">
            <div class="dock-menu__item">
                <i class="pi pi-home"/>
                <span>Home</span>
            </div>
            <div class="dock-menu__item">
                <i class="pi pi-user"/>
                <span>Account</span>
            </div>
            <div class="dock-menu__item">
                <i class="pi pi-search"/>
                <span>Search</span>
            </div>
            <div class="dock-menu__item">
                <i class="pi pi-heart"/>
                <span>Wishlist</span>
            </div>
        </div>
    </div>
    <TheLandingFooter/>
</template>

<style lang="scss" scoped>
.header {
    @apply w-full bg-primary-200 h-24 px-4 xl:px-20 lg:px-16 flex justify-center items-center sticky top-0 z-50;

    &__inner {
        @apply flex justify-between items-center w-full;
    }
}

.dock-menu {
    @apply fixed z-10 bottom-0 left-0 right-0 bg-primary-300 h-16 mx-2 shadow border border-primary-500;

    &__inner {
        @apply flex justify-center items-center divide-x divide-primary-500;
    }

    &__item {
        @apply text-sm flex flex-col justify-center items-center py-2 flex-1;

        > i {
            @apply text-lg;
        }

        > span {
            @apply font-bold;
        }
    }
}
</style>
