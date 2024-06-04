<script lang="ts" setup>
import {Head, useForm} from "@inertiajs/vue3";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import Button from "primevue/button";
import Password from "primevue/password";
import InputText from "primevue/inputtext";
import Toast from "primevue/toast";
import InertiaLink from "@/Components/InertiaLink.vue";

const form = useForm({
    username: null,
    email: null,
    password: null,
    password_confirmation: null,
});
</script>

<template>
    <Head title="Welcome"/>
    <Toast/>
    <section class="min-h-screen grid">
        <div class="container mx-auto my-auto lg:my-16 px-3 lg:px-16 lg:flex lg:max-w-[70rem]">
            <div
                class="flex flex-col justify-center space-y-4 bg-primary-200 shadow-md rounded lg:rounded-l-md px-6 py-4 lg:flex-1">
                <div class="flex flex-col items-center">
                    <InertiaLink :href="route('landing.index')">
                        <img alt="The Book Burrow Logo" class="w-40 h-40" src="/logo.svg"/>
                    </InertiaLink>
                    <h2 class="text-2xl font-bold self-start">Welcome to The Book Burrow</h2>
                    <p class="text-sm text-gray-500 self-start truncate">Create your account</p>
                </div>
                <div class="py-1.5 flex flex-col space-y-2">
                    <div class="flex flex-col space-y-1">
                        <label class="font-semibold" for="username">Username</label>
                        <InputGroup>
                            <InputGroupAddon class="!bg-primary-300">
                                <i class="pi pi-user"/>
                            </InputGroupAddon>
                            <InputText id="username" v-model="form.username"
                                       aria-describedby="username-help"
                                       autofocus
                                       placeholder="Enter your username"
                                       type="text"/>
                        </InputGroup>
                        <small id="username-help" class="text-red-400 truncate max-w-sm" v-text="form.errors.username"/>
                    </div>
                    <div class="flex flex-col space-y-1">
                        <label class="font-semibold" for="email">Email</label>
                        <InputGroup>
                            <InputGroupAddon class="!bg-primary-300">
                                <i class="pi pi-envelope"/>
                            </InputGroupAddon>
                            <InputText id="email" v-model="form.email"
                                       aria-describedby="email-help"
                                       placeholder="Enter your email address"
                                       type="email"/>
                        </InputGroup>
                        <small id="email-help" class="text-red-400 truncate max-w-sm" v-text="form.errors.email"/>
                    </div>
                    <div class="flex flex-col space-y-1">
                        <div class="flex space-x-2">
                            <div class="flex flex-col space-y-1">
                                <label class="font-semibold" for="password">Password</label>
                                <InputGroup>
                                    <InputGroupAddon class="!bg-primary-300">
                                        <i class="pi pi-lock"/>
                                    </InputGroupAddon>
                                    <Password v-model="form.password" :feedback="false" aria-describedby="password-help"
                                              class="w-full"
                                              inputClass="rounded-l-none"
                                              inputId="password" placeholder="Enter your password"
                                              toggleMask
                                    />
                                </InputGroup>
                            </div>
                            <div class="flex flex-col space-y-1">
                                <label class="font-semibold" for="repeat-password">Repeat Password</label>
                                <InputGroup>
                                    <InputGroupAddon class="!bg-primary-300">
                                        <i class="pi pi-lock"/>
                                    </InputGroupAddon>
                                    <Password v-model="form.password_confirmation" :feedback="false"
                                              aria-describedby="repeat-password-help"
                                              class="w-full"
                                              inputClass="rounded-l-none"
                                              inputId="repeat-password" placeholder="Re-enter password"
                                              toggleMask
                                    />
                                </InputGroup>
                            </div>
                        </div>
                        <small id="password-help" class="text-red-400 truncate max-w-72 lg:max-w-md"
                               v-text="form.errors.password"/>
                    </div>
                    <Button :loading="form.processing" class="!mt-4 w-full bg-primary-800"
                            label="Create your account" @click="form.post('/register')"/>
                    <div class="flex space-x-1 items-center font-semibold">
                        <span class="text-gray-500">Have an account?</span>
                        <InertiaLink :href="route('login')">
                            <span class="text-primary-800 hover:text-primary-900">Login now</span>
                        </InertiaLink>
                    </div>
                </div>
            </div>
            <div class="hidden lg:block w-[30rem] h-full relative rounded-r-md overflow-hidden">
                <img alt="Login Banner" class="absolute w-full h-full object-cover object-center block"
                     src="/images/photo-1544185310-0b3cf501672b.jpeg"/>
            </div>
        </div>
    </section>
</template>

<style lang="scss" scoped>

</style>
