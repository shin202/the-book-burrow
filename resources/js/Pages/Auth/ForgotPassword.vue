<script setup lang="ts">
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import {useToast} from "primevue/usetoast";

import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Toast from "primevue/toast";

import InertiaLink from "@/Components/InertiaLink.vue";

const page = usePage<any>();
const toast = useToast();

const form = useForm({
    email: null
});

const onSubmit = () => {
    form.post(route('password.email'));
}

router.on('finish', () => {
    if (page.props.toast?.message) {
        toast.add({severity: page.props.toast.severity, detail: page.props.toast.message, life: 5000})
    }
});
</script>

<template>
    <Head title="Forgot password"/>
    <Toast/>
    <div class="min-h-screen grid">
        <div class="container mx-auto my-auto px-3 flex justify-center items-center">
            <div
                class="flex flex-col justify-center items-center bg-primary-300 px-6 py-4 rounded shadow-md lg:max-w-md">
                <div class="">
                    <img src="/images/forgot-password.svg" alt="Forgot password image" class="w-40 h-40">
                </div>
                <h2 class="text-2xl font-bold pt-4">Forgot password?</h2>
                <div class="text-surface-700 py-4">
                    <p>
                        Don't worry! Just fill in your email and we'll send you a link to reset your password.
                    </p>
                </div>
                <div class="w-full flex flex-col justify-center space-y-1.5">
                    <label for="email">Email</label>
                    <InputText v-model="form.email" id="email" type="email" class="!bg-surface-100"
                               placeholder="Enter your email"/>
                    <small class="text-red-500" v-text="form.errors.email"/>
                </div>
                <Button label="Reset password" class="w-full mt-4 bg-primary-800" @click="onSubmit"
                        :disabled="form.processing" :loading="form.processing"/>
                <InertiaLink :href="route('home.index')" class="btn btn-primary mt-4">
                    <Button label="Back to Home" icon="pi pi-arrow-right" iconPos="right" text
                            class="text-primary-800 hover:text-primary-700 font-bold"/>
                </InertiaLink>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">

</style>
