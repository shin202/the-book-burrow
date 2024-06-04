<script lang="ts" setup>
import {computed, ref} from "vue";
import {useDateFormat} from "@vueuse/core";
import {usePage} from "@inertiajs/vue3";

import Dropdown from "primevue/dropdown";
import Tag from "primevue/tag";
import Button from "primevue/button";

import {useCart, useSeverity} from "@/composables";
import {camelCase, startCase} from "lodash";

const page = usePage<any>();
const {applyCoupon} = useCart();
const {getCouponStatusSeverity} = useSeverity();

const selectedCoupon = ref<string>(page.props.cart.coupon);
const availableCoupons = computed(() => page.props.availableCoupons);
</script>

<template>
    <div class="border-4 border-dotted py-6 px-8 flex flex-col gap-4 mt-8">
        <div class="flex flex-col">
            <Dropdown v-model="selectedCoupon" :options="availableCoupons.data"
                      class="px-4 py-2 !rounded-full h-14"
                      editable
                      emptyMessage="No coupon available"
                      optionLabel="code"
                      optionValue="code"
                      placeholder="Enter or choose a coupon code"
            >
                <template #option="{option}">
                    <div :class="{
                            'cursor-not-allowed': option.available === 0,
                         }"
                         class="flex p-2 gap-2 items-center"
                    >
                        <Tag :value="option.code" severity="success"/>
                        <div class="flex flex-col px-6 gap-1.5">
                            <span><span class="font-bold">Discount:</span> {{ option.value }}</span>
                            <span><span class="font-bold">Usage limit:</span> {{
                                    option.usage_per_user
                                }}</span>
                            <span><span class="font-bold">Available:</span> {{ option.available }}</span>
                            <span><span class="font-bold">Valid to:</span> {{
                                    useDateFormat(option.valid_to, "MMM-DD-YYYY hh:mm:ss a", {locales: "en-US"}).value
                                }}</span>
                            <span>
                                <span class="font-bold">Description: </span>
                                <span v-text="option.description"/>
                            </span>
                        </div>
                        <Tag :severity="getCouponStatusSeverity(option.status)"
                             :value="startCase(camelCase(option.status))"
                             class="ml-auto"/>
                    </div>
                </template>
            </Dropdown>
            <Transition name="fade">
                <small v-if="page.props.errors.code" class="form__feedback"
                       v-text="page.props.errors.code"/>
            </Transition>
        </div>
        <Button :disabled="!selectedCoupon"
                class="h-14 px-6 py-3 !rounded-full"
                label="Apply Coupon" @click="applyCoupon(selectedCoupon)"/>
    </div>
</template>

<style lang="scss" scoped>

</style>
