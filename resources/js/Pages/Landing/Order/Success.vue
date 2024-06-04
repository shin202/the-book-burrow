<script lang="ts">
import LandingLayout from "@/Layouts/LandingLayout.vue";

export default {
    layout: LandingLayout
};
</script>

<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";

import Breadcrumb from "primevue/breadcrumb";
import Button from "primevue/button";

import InertiaLink from "@/Components/InertiaLink.vue";
import {useConfetti} from "@/composables/use-confetti";

defineProps<{
    order_number: string
}>();

useConfetti({
    particleCount: 500,
    spread: 120,
    origin: {y: 0.6},
    shapes: ["circle", "square"],
    colors: ["#26ccff", "#a25afd", "#ff5e7e", "#88ff5a", "#fcff42", "#ffa62d", "#ff36ff"],
});
</script>

<template>
    <Head title="Order completed"/>
    <div class="container mx-auto px-3">
        <div
            class="flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-3 px-16 py-5 bg-primary-300">
            <div class="text-4xl md:text-5xl lg:text-7xl font-bold">Order Completed</div>
            <Breadcrumb :home="{label: 'Home', route: 'landing.index', active: false}"
                        :model="[{label: 'Order completed', active: true}]" class="!bg-primary-300">
                <template #item="{item}">
                    <InertiaLink v-if="item.route" :class="{'text-primary-800 pointer-events-none': item.active}"
                                 :href="route(item.route)"
                                 class="uppercase" v-text="item.label"/>
                    <span v-else :class="{'text-primary-800 pointer-events-none': item.active}" class="uppercase"
                          v-text="item.label"/>
                </template>
            </Breadcrumb>
        </div>
        <div class="mt-14">
            <div class="px-6 py-4 flex flex-col justify-center items-center">
                <svg class="w-20 h-20" height="512" style="enable-background:new 0 0 512 512"
                     version="1.1" viewBox="0 0 520 520" width="512" x="0" xml:space="preserve"
                     xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" y="0"><g><g data-name="7-Check"><path d="M199.078 473.954a41.224 41.224 0 0 1-32.441-15.5L52.757 318.95a45.67 45.67 0 0 1 33.929-74.529c.489-.015.981-.023 1.471-.023a45.552 45.552 0 0 1 30.717 11.892l77.239 70.288a12 12 0 0 0 8.076 3.125q.324 0 .648-.018a12 12 0 0 0 8.339-4.029L450.011 58.067q.156-.177.306-.36a13.234 13.234 0 0 1 9.441-4.839c.318-.021.646-.033.973-.033a13.222 13.222 0 0 1 9.125 3.592c4.524 4.189 6 11.326 3.227 15.586-.047.072-236.657 381.132-236.657 381.132-8.138 12.428-20.89 19.988-35 20.746-.782.042-1.57.063-2.344.063z" data-original="#b0ef8f" fill="#b0ef8f" opacity="1"></path><path
                    d="M460.734 64.835a1.245 1.245 0 0 1 .969.4 2.69 2.69 0 0 1 .705 1.218l-236.1 380.234c-6.109 9.264-15.169 14.668-25.532 15.224-.568.031-1.141.046-1.705.046a29.43 29.43 0 0 1-23.143-11.09l-113.88-139.5a33.671 33.671 0 0 1 25.012-54.952q.549-.017 1.1-.017a33.575 33.575 0 0 1 22.642 8.768l77.239 70.287a24 24 0 0 0 16.152 6.25c.431 0 .864-.012 1.3-.035a24.005 24.005 0 0 0 16.677-8.059L459 66.02q.314-.354.613-.72a1.169 1.169 0 0 1 .954-.459c.057 0 .114-.006.17-.006m0-24c-.6 0-1.2.02-1.8.062a25.176 25.176 0 0 0-17.9 9.217L204.19 317.7l-77.239-70.288A57.38 57.38 0 0 0 88.156 232.4q-.923 0-1.85.029a57.67 57.67 0 0 0-42.845 94.111l113.881 139.5a53.451 53.451 0 0 0 41.736 19.913q1.486 0 2.987-.08c18.021-.968 34.224-10.531 44.484-26.284L483.138 78.562c5.968-9.161 3.715-22.751-5.129-30.94a25.231 25.231 0 0 0-17.279-6.787z"
                    data-original="#009045"
                    fill="#009045" opacity="1"></path></g></g></svg>
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-bold text-center">Thank you for your order!</h1>
                    <span class="text-center font-bold text-surface-500">Order Number: #{{ order_number }}</span>
                    <div class="text-center">Your order has been processing.</div>
                    <div class="text-center">You will receive an email confirmation shortly.</div>
                    <Button class="mt-5 bg-primary-800" label="Continue Shopping"/>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>
