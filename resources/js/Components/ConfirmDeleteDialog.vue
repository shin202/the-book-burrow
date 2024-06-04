<script lang="ts" setup>
import ConfirmDialog from 'primevue/confirmdialog';
import Button from "primevue/button";
</script>

<template>
    <ConfirmDialog group="confirmDeleteDialog">
        <template #container="{message, acceptCallback, rejectCallback}">
            <div class="flex flex-col items-center p-4 rounded bg-surface-0">
                <div class="text-red-500 flex justify-center items-center gap-2 border-b py-1.5 w-full">
                    <i class="pi pi-exclamation-triangle text-warning text-4xl"/>
                    <span class="text-md lg:text-lg">{{ message.header ?? 'Danger Zone' }}</span>
                </div>
                <p class="mt-2 px-4 py-6" v-text="message.message"/>
                <div class="flex items-center gap-4 w-full">
                    <Button :label="message.rejectLabel ?? 'Cancel'" class="flex-1" severity="secondary"
                            @click="rejectCallback"/>
                    <Button :label="message.acceptLabel ?? 'Yes'" class="flex-1" severity="danger"
                            @click="acceptCallback"/>
                </div>
            </div>
        </template>
    </ConfirmDialog>
</template>
