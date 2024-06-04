<script lang="ts" setup>
import {usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import Sidebar from "primevue/sidebar";
import Button from "primevue/button";
import InertiaLink from "@/Components/InertiaLink.vue";

const page = usePage();

const cart = computed<any>(() => page.props.cart);

const isOpenShoppingCart = ref(false);

const onToggleShoppingCart = () => {
    isOpenShoppingCart.value = !isOpenShoppingCart.value;
};
</script>

<template>
    <div>
        <Button :badge="`${cart.count}`" class="min-w-14" icon="pi pi-shopping-cart"
                size="large" text @click="onToggleShoppingCart"/>
        <Sidebar v-model:visible="isOpenShoppingCart" class="w-96" position="right">
            <template #container="{closeCallback}">
                <div class="py-4 px-6 divide-y">
                    <div class="flex justify-between items-center py-2">
                        <h2 class="font-bold text-xl tracking-wider">Shopping cart ({{ cart.count }})</h2>
                        <Button class="hover:rotate-90" icon="pi pi-times" plain severity="contrast"
                                size="large" text
                                @click="closeCallback"/>
                    </div>
                    <div v-if="Object.keys(cart.items).length > 0"
                         class="flex flex-col gap-4 max-w-full py-6 px-4 max-h-[calc(100vh-16rem)] min-h-[calc(100vh-16rem)] overflow-y-auto">
                        <div v-for="item in cart.items">
                            <div class="flex gap-4 items-center">
                                <div class="w-16 h-24 bg-surface-200 rounded-xl overflow-hidden">
                                    <img :src="item.cover_image" alt="product image" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold line-clamp-2" v-text="item.title"/>
                                    <span class="text-surface-500">{{ item.quantity }} x {{
                                            item.price
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex justify-center items-center h-96">
                        <p class="text-surface-500 text-md font-bold flex flex-col justify-center items-center gap-2">
                            <i class="pi pi-shopping-cart text-6xl"/>
                            <span class="text-md text-center">
                                Looks like you haven't added any items to your cart yet.
                                <span class="text-primary-800 hover:underline">
                                    <a href="#">
                                        Shopping now
                                        <i class="pi pi-shopping-cart"/>
                                    </a>
                                </span>
                            </span>
                        </p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between items-center py-2">
                            <h3 class="font-bold text-lg">Subtotal</h3>
                            <span class="font-bold text-lg">{{ cart.subtotal }}</span>
                        </div>
                        <div class="flex flex-col gap-4">
                            <InertiaLink :href="route('cart.index')" class="w-full">
                                <Button class="px-6 py-3 !rounded-full w-full" label="View cart"
                                        severity="secondary" size="large"/>
                            </InertiaLink>
                            <InertiaLink :href="route('checkout.index')" class="w-full">
                                <Button class="px-6 py-3 !rounded-full w-full"
                                        icon="pi pi-shopping-cart"
                                        label="CHECKOUT"
                                        size="large"/>
                            </InertiaLink>
                        </div>
                    </div>
                </div>
            </template>
        </Sidebar>
    </div>
</template>

<style lang="scss" scoped>

</style>
