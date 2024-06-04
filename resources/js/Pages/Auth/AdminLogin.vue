<script lang="ts" setup>
import {Head, useForm} from "@inertiajs/vue3";

import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import Checkbox from "primevue/checkbox";
import Button from "primevue/button";
import InertiaLink from "@/Components/InertiaLink.vue";

const form = useForm({
    usernameOrEmail: null,
    password: null,
    remember: false,
});
</script>

<template>
    <Head title="Dashboard - Please login"/>
    <section class="min-h-screen grid">
        <div class="container mx-auto my-auto lg:my-16 px-3 lg:px-16 lg:flex lg:max-w-[70rem]">
            <div
                class="flex flex-col justify-center space-y-4 bg-primary-200 shadow-md rounded lg:rounded-l-md px-6 py-4 lg:flex-1">
                <div class="flex flex-col items-center">
                    <InertiaLink :href="route('landing.index')">
                        <img alt="The Book Burrow Logo" class="w-40 h-40" src="/logo.svg"/>
                    </InertiaLink>
                    <h2 class="text-2xl font-bold self-start">Login</h2>
                    <p class="text-sm text-gray-500 self-start truncate">Welcome back Admin, please login to your
                        account</p>
                </div>
                <div class="py-1.5 flex flex-col space-y-2">
                    <div class="flex flex-col space-y-1">
                        <label class="font-semibold" for="username-or-email">Username or email</label>
                        <InputGroup>
                            <InputGroupAddon class="!bg-primary-300">
                                <i class="pi pi-user"/>
                            </InputGroupAddon>
                            <InputText id="username-or-email" v-model="form.usernameOrEmail"
                                       aria-describedby="username-or-email-help"
                                       autofocus
                                       placeholder="Enter your username or email"/>
                        </InputGroup>
                        <small id="username-or-email-help" class="text-red-400" v-text="form.errors.usernameOrEmail"/>
                    </div>
                    <div class="flex flex-col space-y-1">
                        <label class="font-semibold" for="password">Password</label>
                        <InputGroup>
                            <InputGroupAddon class="!bg-primary-300">
                                <i class="pi pi-lock"/>
                            </InputGroupAddon>
                            <Password v-model="form.password" :feedback="false" aria-describedby="password-help"
                                      class="w-full"
                                      inputClass="rounded-l-none w-full"
                                      inputId="password" placeholder="Enter your password" toggleMask
                            />
                        </InputGroup>
                        <small id="password-help" class="text-red-400" v-text="form.errors.password"/>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-1.5">
                            <Checkbox v-model="form.remember" :binary="true" inputId="remember"/>
                            <label class="font-semibold" for="remember">Remember me</label>
                        </div>
                        <InertiaLink :href="route('password.request')"
                                     class="text-primary-800 hover:text-primary-900 font-semibold">Forgot password?
                        </InertiaLink>
                    </div>
                    <Button :disabled="form.processing" :loading="form.processing" class="!mt-4 w-full bg-primary-800"
                            label="Login" @click="form.post('/login')"/>
                </div>
            </div>
            <div class="hidden lg:block w-[30rem] h-full relative rounded-r-md overflow-hidden">
                <img alt="Login Banner" class="absolute w-full h-full block object-cover object-bottom"
                     src="/images/photo-1577627444534-b38e16c9d796.jpeg"/>
            </div>
        </div>
    </section>
</template>

<style lang="scss" scoped>

</style>
