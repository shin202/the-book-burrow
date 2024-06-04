<script lang="ts">
import LandingLayout from "@/Layouts/LandingLayout.vue";

export default {
    layout: LandingLayout
};
</script>
<script lang="ts" setup>
import {Head, usePage} from "@inertiajs/vue3";

import Breadcrumb from "primevue/breadcrumb";
import Button from "primevue/button";
import Message from "primevue/message";


import InertiaLink from "@/Components/InertiaLink.vue";
import {useCart} from "@/composables";
import {Book, Coupon, Pagination} from "@/types";
import CouponCode from "@/Components/CouponCode.vue";
import {computed} from "vue";
import BookCard from "@/Components/BookCard.vue";

defineProps<{
    cart: any,
    availableCoupons: Pagination<Coupon[]>,
    relatedBooks: {
        userAlsoEnjoyed: Pagination<Book[]>,
        samesLikeThis: Pagination<Book[]>
    } | null
}>();

const page = usePage();
const {updateQuantity, removeCoupon, removeItem} = useCart();

const quantityError = computed(() => {
    return page.props.errors.quantity;
});
</script>

<template>
    <Head title="Cart"/>
    <div class="container mx-auto px-3 mt-4">
        <div
            class="flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-3 px-6 py-8 bg-primary-300">
            <div class="text-4xl md:text-5xl lg:text-8xl font-bold">Cart</div>
            <Breadcrumb :home="{label: 'Home', route: 'landing.index', active: false}"
                        :model="[{label: 'Cart', route: 'cart.index', active: true}]" class="!bg-primary-300">
                <template #item="{item}">
                    <InertiaLink v-if="item.route" :class="{'text-primary-800 pointer-events-none': item.active}"
                                 :href="route(item.route)"
                                 class="uppercase" v-text="item.label"/>
                    <span v-else :class="{'text-primary-800 pointer-events-none': item.active}" class="uppercase"
                          v-text="item.label"/>
                </template>
            </Breadcrumb>
        </div>
        <Transition name="fade">
            <Message v-if="quantityError" severity="error">{{ quantityError }}</Message>
        </Transition>
        <div class="flex flex-col lg:flex-row mt-14">
            <div class="flex-1">
                <!-- Cart Items (Mobile) -->
                <div class="mt-8 flex flex-col gap-4 divide-y md:hidden">
                    <div v-for="item in cart.items" :key="item.id" class="py-4 flex gap-4">
                        <Button class="!text-xs hidden md:block self-center hover:rotate-90" icon="pi pi-times"
                                outlined
                                rounded severity="secondary" size="small" text/>
                        <div class="w-20 h-28 bg-primary-300 overflow-hidden">
                            <img :alt="item.title" :src="item.cover_image" class="w-full h-full object-cover"/>
                        </div>
                        <div class="flex flex-col md:flex-row md:justify-between flex-1">
                            <div class="font-bold flex justify-between items-center">
                                <a class="line-clamp-2 max-w-60" href="#" v-text="item.title"/>
                                <Button class="!text-xs md:hidden ml-auto" icon="pi pi-times" severity="secondary"
                                        size="small"
                                        text/>
                            </div>
                            <div
                                class="flex flex-col md:flex-row gap-2 divide-y mt-4 text-sm w-full md:justify-between md:ml-20">
                                <div class="w-full flex justify-between items-center py-2.5">
                                    <span class="uppercase md:hidden">Price:</span>
                                    <span v-text="item.price"/>
                                </div>
                                <div class="w-full flex justify-between items-center py-2.5">
                                    <span class="uppercase md:hidden">Quantity:</span>
                                    <div class="flex justify-center items-center border">
                                        <button class="w-8 h-8 bg-surface-0 hover:bg-primary-800"
                                                @click="updateQuantity(item.id, item.quantity - 1)">-
                                        </button>
                                        <input v-model="item.quantity" class="w-8 h-8 text-center" type="number"/>
                                        <button class="w-8 h-8 bg-surface-0 hover:bg-primary-800"
                                                @click="updateQuantity(item.id, item.quantity + 1)">+
                                        </button>
                                    </div>
                                </div>
                                <div class="w-full flex justify-between items-center py-2.5">
                                    <span class="uppercase md:hidden">Subtotal:</span>
                                    <span v-text="item.subtotal"/>
                                </div>
                                <div v-if="item.has_discount" class="w-full flex justify-between items-center py-2.5">
                                    <span class="uppercase md:hidden">Discount:</span>
                                    <span class="text-red-500" v-text="`-${item.discount_total}`"/>
                                </div>
                                <div v-if="item.has_discount" class="w-full flex justify-between items-center py-2.5">
                                    <span class="uppercase md:hidden">New subtotal:</span>
                                    <span v-text="item.new_subtotal"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Cart Items (Mobile) -->

                <!-- Cart Items (>= 768px) -->
                <div class="lg:max-w-screen-sm xl:max-w-full overflow-x-auto">
                    <table v-if="cart.count > 0" class="table-auto w-full divide-y hidden md:table">
                        <thead>
                        <tr>
                            <th class="py-2 uppercase font-normal"/>
                            <th class="py-2 uppercase font-normal">Product</th>
                            <th class="py-2 uppercase font-normal text-right">Price</th>
                            <th class="py-2 uppercase font-normal text-center">Quantity</th>
                            <th class="py-2 uppercase font-normal text-right">Subtotal</th>
                            <th v-if="cart.has_discount" class="py-2 uppercase font-normal text-right">Discount</th>
                            <th v-if="cart.has_discount" class="py-2 uppercase font-normal">New subtotal</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y">
                        <tr v-for="item in cart.items" :key="item.id">
                            <td class="py-2">
                                <Button class="!text-xs" icon="pi pi-times" outlined rounded severity="secondary"
                                        size="small"
                                        text @click="removeItem(item.id)"/>
                            </td>
                            <td class="py-2 px-4">
                                <div class="flex gap-4 w-96">
                                    <div class="w-20 h-28 bg-primary-300 overflow-hidden">
                                        <img :alt="item.title" :src="item.cover_image"
                                             class="w-full h-full object-cover"/>
                                    </div>
                                    <div>
                                        <span>Product title</span>
                                        <h3 class="font-bold font-serif hover:text-primary">
                                            <InertiaLink :href="route('books.show', item.slug)">
                                                <span v-text="item.title"/>
                                            </InertiaLink>
                                        </h3>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 px-4 text-right" v-text="item.price"/>
                            <td class="py-2 px-4">
                                <div class="flex justify-center">
                                    <button class="w-8 h-8 bg-surface-0 hover:bg-primary-800 hover:text-surface-0"
                                            @click="updateQuantity(item.id, item.quantity - 1)">-
                                    </button>
                                    <input v-model="item.quantity"
                                           :min="1"
                                           class="w-16 h-8 text-center border-none outline-none"
                                           type="number"
                                           @change="updateQuantity(item.id, item.quantity)"/>
                                    <button class="w-8 h-8 bg-surface-0 hover:bg-primary-800 hover:text-surface-0"
                                            @click="updateQuantity(item.id, item.quantity + 1)">+
                                    </button>
                                </div>
                            </td>
                            <td class="py-2 px-4 text-right" v-text="item.subtotal"/>
                            <td v-if="item.has_discount" class="py-2 px-4 text-right text-red-500"
                                v-text="`-${item.discount_total}`"/>
                            <td v-if="item.has_discount" class="py-2 px-4 text-right"
                                v-text="item.new_subtotal"/>
                        </tr>
                        </tbody>
                    </table>
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
                </div>
                <!-- Cart Items (>= 768px) -->
                <CouponCode/>
            </div>
            <div class="mt-4 p-8 border flex-1 md:max-w-96 lg:mt-0 lg:ml-6">
                <div>
                    <h2 class="font-bold text-3xl">Cart totals</h2>
                    <div class="divide-y mt-4">
                        <div class="flex justify-between items-center py-2">
                            <span class="font-bold text-lg text-surface-600">Subtotal</span>
                            <span class="font-bold text-lg" v-text="cart.subtotal"/>
                        </div>
                        <div class="flex justify-between items-center py-2">
                        <span class="font-bold text-lg text-surface-600">
                            Shipping
                            <i
                                class="ml-1 pi pi-question-circle"/>
                        </span>
                            <span class="font-bold text-lg">Free</span>
                        </div>
                        <div v-if="cart.has_discount" class="flex justify-between items-center py-2">
                            <span class="font-bold text-lg text-surface-600">
                                Discount ({{ cart.coupon }})
                                <Button class="!text-xs" icon="pi pi-times" rounded severity="secondary" size="small"
                                        text @click="removeCoupon"/>
                            </span>
                            <span class="font-bold text-lg text-red-500" v-text="`-${cart.discount}`"/>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="font-bold text-3xl text-surface-600">Total</span>
                            <span class="font-bold text-2xl" v-text="cart.total"/>
                        </div>
                    </div>
                    <InertiaLink :href="cart.canCheckout ? route('checkout.index') : '#'">
                        <Button :disabled="!cart.canCheckout"
                                class="h-14 px-6 py-3 !rounded-full w-full mt-8"
                                label="Proceed to checkout"/>
                    </InertiaLink>
                </div>
            </div>
        </div>
        <div v-if="relatedBooks !== null">
            <!-- Related Books -->
            <div class="mt-8">
                <h2 class="font-bold font-serif text-xl">Customers also <span class="italic"> enjoyed</span></h2>
                <div class="mt-4">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8 px-4">
                        <BookCard v-for="book in relatedBooks.userAlsoEnjoyed.data" :key="book.slug" :book="book"/>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <h2 class="font-bold font-serif text-xl">Sames like this</h2>
                <div class="mt-4">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8 px-4">
                        <BookCard v-for="book in relatedBooks.samesLikeThis.data" :key="book.slug" :book="book"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>
