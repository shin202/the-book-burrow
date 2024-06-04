<script lang="ts">
import LandingLayout from "@/Layouts/LandingLayout.vue";

export default {
    layout: LandingLayout
};
</script>
<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";
import {getCountries} from "iso-3166-1-alpha-2";

import Breadcrumb from "primevue/breadcrumb";
import InputText from "primevue/inputtext";
import Dropdown from "primevue/dropdown";
import Textarea from "primevue/textarea";
import Button from "primevue/button";
import RadioButton from "primevue/radiobutton";
import Accordion from "primevue/accordion";
import AccordionTab from "primevue/accordiontab";
import Message from "primevue/message";


import InertiaLink from "@/Components/InertiaLink.vue";

import {useCart} from "@/composables";
import CouponCode from "@/Components/CouponCode.vue";
import {Coupon, Pagination} from "@/types";
import {computed, ref} from "vue";
import {useCheckout} from "@/composables/use-checkout";
import {PaymentMethodEnum, PaymentMethodList} from "@/common/enums";

defineProps<{
    cart: any
    availableCoupons: Pagination<Coupon[]>
}>();

const {removeCoupon} = useCart();
const {checkout, placeOrder, clearErrorMessage, form, isProcessing, errorMessage} = useCheckout();

const paymentMethodIndex = ref(0);

const submitBtnLabel = computed(() => {
    return PaymentMethodList.filter((item) => item.value === paymentMethodIndex.value)[0].value === PaymentMethodEnum.STRIPE ? "Pay now" : "Place order";
});

const onSubmit = (e: Event) => {
    paymentMethodIndex.value === PaymentMethodEnum.STRIPE ? checkout(e) : placeOrder(e);
};
</script>

<template>
    <Head title="Checkout"/>
    <div class="container mx-auto px-3 mb-8">
        <div
            class="flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-3 px-6 py-8 bg-primary-300">
            <div class="text-4xl md:text-5xl lg:text-8xl font-bold">Checkout</div>
            <Breadcrumb :home="{label: 'Home', route: 'landing.index', active: false}"
                        :model="[{label: 'Checkout', route: 'checkout.index', active: true}]" class="!bg-primary-300">
                <template #item="{item}">
                    <InertiaLink v-if="item.route" :class="{'text-primary-800 pointer-events-none': item.active}"
                                 :href="route(item.route)"
                                 class="uppercase" v-text="item.label"/>
                    <span v-else :class="{'text-primary-800 pointer-events-none': item.active}" class="uppercase"
                          v-text="item.label"/>
                </template>
            </Breadcrumb>
        </div>
        <div class="flex flex-col lg:flex-row mt-14 gap-4">
            <form class="form flex-1">
                <h3 class="font-bold font-serif text-2xl">Billing details</h3>
                <div class="form__group">
                    <label class="form__label form__label--required" for="first-name">First name</label>
                    <InputText id="first-name" v-model="form.first_name" class="px-4 py-2 !rounded-full h-12"
                               placeholder="John"/>
                    <Transition name="fade">
                        <small v-if="form.errors.first_name" class="form__feedback" v-text="form.errors.first_name"/>
                    </Transition>
                </div>
                <div class="form__group">
                    <label class="form__label form__label--required" for="last-name">Last name</label>
                    <InputText id="last-name" v-model="form.last_name" class="px-4 py-2 !rounded-full h-12"
                               placeholder="Doe"/>
                    <Transition name="fade">
                        <small v-if="form.errors.last_name" class="form__feedback" v-text="form.errors.last_name"/>
                    </Transition>
                </div>
                <div class="form__group">
                    <label class="form__label form__label--required" for="email">Email</label>
                    <InputText id="email" v-model="form.billing_email" class="px-4 py-2 !rounded-full h-12"
                               placeholder="example@gmail.com"
                               type="email"/>
                    <Transition name="fade">
                        <small v-if="form.errors.billing_email" class="form__feedback"
                               v-text="form.errors.billing_email"/>
                    </Transition>
                </div>
                <div class="form__group">
                    <label class="form__label form__label--required" for="phone">Phone</label>
                    <InputText id="phone" v-model="form.billing_phone" class="px-4 py-2 !rounded-full h-12"
                               placeholder="(012)-345-7890"
                    />
                    <Transition name="fade">
                        <small v-if="form.errors.billing_phone" class="form__feedback"
                               v-text="form.errors.billing_phone"/>
                    </Transition>
                </div>
                <div class="form__group">
                    <label class="form__label form__label--required" for="country-or-region">Country/Region</label>
                    <Dropdown v-model="form.billing_country" :options="getCountries()"
                              :virtualScrollerOptions="{itemSize: 40}"
                              class="px-4 py-2 !rounded-full h-12"
                              filter inputId="country-or-region"
                              placeholder="Vietnam"/>
                    <Transition name="fade">
                        <small v-if="form.errors.billing_country" class="form__feedback"
                               v-text="form.errors.billing_country"/>
                    </Transition>
                </div>
                <div class="form__group">
                    <label class="form__label form__label--required" for="address">Address</label>
                    <InputText id="address" v-model="form.billing_address" class="px-4 py-2 !rounded-full h-12"
                               placeholder="House number, street name"
                    />
                    <Transition name="fade">
                        <small v-if="form.errors.billing_address" class="form__feedback"
                               v-text="form.errors.billing_address"/>
                    </Transition>
                </div>
                <div class="form__group">
                    <label class="form__label form__label--required" for="city">Town/City</label>
                    <InputText id="city" v-model="form.billing_city" class="px-4 py-2 !rounded-full h-12"
                               placeholder="Hanoi"/>
                    <Transition name="fade">
                        <small v-if="form.errors.billing_city" class="form__feedback"
                               v-text="form.errors.billing_city"/>
                    </Transition>
                </div>
                <div class="form__group">
                    <label class="form__label form__label--required" for="state">State/Province</label>
                    <InputText id="state" v-model="form.billing_state" class="px-4 py-2 !rounded-full h-12"
                               placeholder="Hanoi"/>
                    <Transition name="fade">
                        <small v-if="form.errors.billing_state" class="form__feedback"
                               v-text="form.errors.billing_state"/>
                    </Transition>
                </div>
                <div class="form__group">
                    <label class="form__label form__label--required" for="zip">ZIP</label>
                    <InputText id="zip" v-model="form.billing_zip" class="px-4 py-2 !rounded-full h-12"
                               placeholder="100000"/>
                    <Transition name="fade">
                        <small v-if="form.errors.billing_state" class="form__feedback"
                               v-text="form.errors.billing_state"/>
                    </Transition>
                </div>
                <div>
                    <h3 class="font-bold font-serif text-2xl mt-4">Additional information</h3>
                    <div class="form__group mt-2">
                        <label class="form__label form__label--required" for="note">Order notes</label>
                        <Textarea id="note" v-model="form.notes"
                                  placeholder="Notes about your order, e.g. special notes for delivery."
                                  rows="5"/>
                        <Transition name="fade">
                            <small v-if="form.errors.notes" class="form__feedback" v-text="form.errors.notes"/>
                        </Transition>
                    </div>
                </div>
            </form>
            <div class="bg-surface-0 rounded-lg px-4 py-6 shadow-md flex-1">
                <h3 class="font-bold text-2xl">Your order</h3>
                <div class="flex flex-col divide-y">
                    <div class="flex justify-between items-center py-4">
                        <div class="text-lg font-medium">Product</div>
                        <div class="text-lg font-medium">Subtotal</div>
                    </div>
                    <div>
                        <div v-for="item in cart.items" class="flex justify-between items-center py-6">
                            <InertiaLink :href="route('books.show', item.slug)"
                                         class="text-primary hover:text-primary/80">
                                <div class="line-clamp-2">
                                    {{ item.title }} x {{ item.quantity }}
                                </div>
                            </InertiaLink>
                            <div class="" v-text="item.price"/>
                        </div>
                    </div>
                    <div class="flex justify-between items-center py-4">
                        <div class="font-medium">Subtotal</div>
                        <div class="" v-text="cart.subtotal"/>
                    </div>
                    <div v-if="cart.has_discount">
                        <div class="flex justify-between items-center py-4">
                            <div class="flex items-center">
                                Discount <span class="font-bold">({{ cart.coupon }})</span>
                                <Button class="!text-xs" icon="pi pi-times" rounded severity="secondary" size="small"
                                        text @click="removeCoupon"/>
                            </div>
                            <div class="text-red-500">
                                -{{ cart.discount }}
                            </div>
                        </div>
                        <div class="flex justify-between items-center py-4">
                            <div class="">New subtotal</div>
                            <div class="" v-text="cart.new_subtotal"/>
                        </div>
                    </div>
                    <div class="flex justify-between items-center py-4">
                        <div class="font-medium">Shipping</div>
                        <div class="">Free</div>
                    </div>
                    <div class="flex justify-between items-center py-4">
                        <div class="text-xl uppercase font-bold">Total</div>
                        <div class="text-xl font-bold">{{ cart.total }}</div>
                    </div>
                </div>
                <CouponCode/>
                <Accordion v-model:activeIndex="paymentMethodIndex">
                    <AccordionTab>
                        <template #header>
                            <div class="flex items-center gap-2">
                                <RadioButton v-model="paymentMethodIndex" :value="0"/>
                                <div class="text-lg font-medium">Credit card</div>
                            </div>
                        </template>
                        <div id="payment-element"/>
                    </AccordionTab>
                    <AccordionTab>
                        <template #header>
                            <div class="flex items-center gap-2">
                                <RadioButton v-model="paymentMethodIndex" :value="1"/>
                                <div class="text-lg font-medium">Cash on delivery</div>
                            </div>
                        </template>
                        <p>
                            Pay with cash upon delivery.
                        </p>
                    </AccordionTab>
                </Accordion>
                <Message v-if="errorMessage" severity="error" @close="clearErrorMessage">{{ errorMessage }}</Message>
                <div class="flex justify-center mt-4">
                    <Button
                        :disabled="isProcessing"
                        :label="submitBtnLabel"
                        :loading="isProcessing"
                        class="!text-base h-14 px-6 py-3 !rounded-full w-full mt-8"
                        type="button"
                        @click="onSubmit"/>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>
