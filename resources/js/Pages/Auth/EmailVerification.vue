<script lang="ts" setup>
import {computed, onMounted, ref} from "vue";
import {Head, router, usePage} from "@inertiajs/vue3";
import {useIntervalFn} from "@vueuse/core";
import {useToast} from "primevue/usetoast";

import Button from "primevue/button";
import Toast from "primevue/toast";

import InertiaLink from "@/Components/InertiaLink.vue";

const props = defineProps<{
    verified: boolean;
}>();

const toast = useToast();
const page = usePage<any>();

const countDown = ref(5);

const title = computed(() => props.verified ? "Email Verified" : "Email Sent");

const startCountdown = () => {
    if (!props.verified) {
        return;
    }

    useIntervalFn(() => {
        if (countDown.value > 0) {
            countDown.value--;
        } else {
            window.location.href = route("home.index");
        }
    }, 1000);
};

const onResend = () => {
    router.post(route("verification.resend"));
};

router.on("finish", () => {
    if (page.props.toast?.message) {
        toast.add({severity: page.props.toast.severity, summary: "Success", detail: page.props.toast.message});
    }
});

onMounted(() => {
    startCountdown();
});
</script>

<template>
    <Head :title="title"/>
    <Toast/>
    <div class="min-h-screen grid">
        <div class="container mx-auto my-auto px-3 flex justify-center items-center">
            <div
                class="flex flex-col justify-center items-center bg-primary-300 px-6 py-4 rounded shadow-md lg:max-w-md">
                <div class="">
                    <img alt="Email Sent Image" class="w-40 h-40" src="/images/email-sent.svg">
                </div>
                <h2 class="text-2xl font-bold" v-text="title"/>
                <div v-if="!verified" class="text-surface-700 py-4">
                    <p>
                        We've sent you an email with a link to verify your email address.
                        Please check your inbox.
                    </p>
                    <p class="pt-2 text-surface-400">
                        Don't see the email? <span
                        class="text-primary-800 hover:text-primary-700 font-bold cursor-pointer"
                        @click="onResend">Resend</span>
                    </p>
                </div>
                <div v-else class="text-surface-700 py-4">
                    <p>
                        Thank you for verifying your email address.
                        You will now be redirected to the home page in {{ countDown }} second(s).
                    </p>
                </div>
                <InertiaLink :href="route('landing.index')" class="btn btn-primary">
                    <Button class="text-primary-800 hover:text-primary-700 font-bold" icon="pi pi-arrow-right" iconPos="right" label="Back to Home"
                            text/>
                </InertiaLink>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>
