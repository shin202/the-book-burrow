<script setup lang="ts">
import {Head, useForm} from "@inertiajs/vue3";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Password from "primevue/password";

const props = defineProps<{
    token: string
}>();

const form = useForm({
    email: null,
    password: null,
    password_confirmation: null,
    token: props.token
});

const onSubmit = () => {
    form.post(route('password.update'));
}
</script>

<template>
    <Head title="Reset password"/>
    <div class="min-h-screen grid">
        <div class="container mx-auto my-auto px-3 flex justify-center items-center">
            <div
                class="flex flex-col justify-center items-center bg-primary-300 px-6 py-4 rounded shadow-md lg:max-w-md">
                <div class="">
                    <img src="/images/reset-password.svg" alt="Reset password image" class="w-40 h-40">
                </div>
                <h2 class="text-2xl font-bold pt-4">Reset account password</h2>
                <div class="text-surface-700 py-4 text-center">
                    <p>
                        Enter below information to reset your account password.
                    </p>
                </div>
                <div class="w-full flex flex-col space-y-2">
                    <div class="w-full flex flex-col justify-center space-y-1.5">
                        <label for="email">Email</label>
                        <InputText v-model="form.email" id="email" type="email" class="!bg-surface-100"
                                   placeholder="Enter your email"/>
                        <small class="text-red-500" v-text="form.errors.email"/>
                    </div>
                    <div class="w-full flex flex-col justify-center space-y-1.5">
                        <label for="password">New password</label>
                        <Password toggleMask v-model="form.password" inputId="password" inputClass="!bg-surface-100"
                                  placeholder="Enter your new password"/>
                    </div>
                    <div class="w-full flex flex-col justify-center space-y-1.5">
                        <label for="repeat-password">Repeat new password</label>
                        <Password toggleMask v-model="form.password_confirmation" inputId="repeat-password"
                                  inputClass="!bg-surface-100"
                                  placeholder="Repeat your new password"/>
                    </div>
                    <small class="text-red-500" v-text="form.errors.password"/>
                    <InputText hidden v-model="form.token"/>
                </div>
                <Button label="Reset password" class="w-full mt-4 bg-primary-800" @click="onSubmit"/>
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">

</style>
