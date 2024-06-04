<script lang="ts" setup>
import Toast from "primevue/toast";
import Avatar from "primevue/avatar";
import Button from "primevue/button";

import InertiaLink from "@/Components/InertiaLink.vue";
</script>

<template>
    <Toast group="realtime-notification" position="bottom-right">
        <template #container="{message, closeCallback}">
            <div
                class="flex flex-col items-start flex-1 relative bg-primary-50 px-6 py-2 rounded-lg text-primary-500 border border-primary-200">
                <div class="flex justify-between items-center w-full">
                    <div class="flex gap-2 items-center">
                        <Avatar :label="message.detail.from.charAt(0).toUpperCase()" shape="circle"/>
                        <span class="font-semibold text-sm" v-text="message.detail.from"/>
                    </div>
                    <span class="text-xs text-surface-500" v-text="message.detail.created_at"/>
                </div>
                <p class="font-medium text-sm my-3 line-clamp-2 max-w-80" v-text="message.detail.summary"/>
                <div class="flex items-center gap-2 self-end">
                    <Button icon="pi pi-times" severity="secondary" text @click="closeCallback"/>
                    <InertiaLink :href="message.detail.viewUrl">
                        <Button icon="pi pi-eye" severity="success" text/>
                    </InertiaLink>
                </div>
            </div>
        </template>
    </Toast>
</template>
