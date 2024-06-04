<script lang="ts" setup>
import FileUpload, {FileUploadSelectEvent} from "primevue/fileupload";
import Image from "primevue/image";
import Button from "primevue/button";

const emit = defineEmits<{
    (event: "fileSelect", file: File): void
}>();

const onFileSelect = ({files}: FileUploadSelectEvent) => {
    emit("fileSelect", files[0]);
};
</script>

<template>
    <FileUpload ref="fileUpload" :fileLimit="1" :multiple="false" accept="image/*"
                @select="onFileSelect"
    >
        <template #header="{chooseCallback, clearCallback, files}">
            <div class="flex flex-wrap justify-between items-center flex-1 gap-2">
                <div class="flex gap-2">
                    <Button icon="pi pi-images" outlined rounded @click="chooseCallback()"/>
                    <Button :disabled="!files || files.length === 0" icon="pi pi-times" outlined
                            rounded
                            severity="danger"
                            @click="clearCallback()"/>
                </div>
            </div>
        </template>
        <template #content="{files, removeFileCallback}">
            <div v-if="files.length > 0" class="flex space-x-4">
                <div v-for="(file, index) of files" :key="file.name"
                     class="border border-dotted border-primary-500">
                    <div
                        class="w-40 h-40 flex flex-col justify-center items-center space-y-4 relative group">
                        <Image :alt="file.name" :src="(<any>file).objectURL" role="presentation"/>
                        <Button
                            class="!absolute -bottom-5 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 ease-in-out"
                            icon="pi pi-times" rounded
                            severity="danger"
                            @click="removeFileCallback(index)"/>
                    </div>
                </div>
            </div>
        </template>
        <template #empty>
            <div class="flex flex-col space-y-4">
                <div class="flex items-center justify-center flex-col text-surface-500">
                    <i class="pi pi-cloud-upload border-2 p-5 text-8xl rounded-full text-primary-300"/>
                    <p class="mt-4 mb-0">Drag and drop files to here to upload.</p>
                </div>
            </div>
        </template>
    </FileUpload>
</template>

<style lang="scss" scoped>

</style>
