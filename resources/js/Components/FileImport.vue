<script lang="ts" setup>
import {ref} from "vue";

import Button from "primevue/button";
import Badge from "primevue/badge";
import OverlayPanel from "primevue/overlaypanel";
import FileUpload, {FileUploadUploaderEvent} from "primevue/fileupload";

import {humanFileSize} from "@/common/shared";
import {useImport} from "@/composables/use-import";

const props = defineProps<{
    routeName: string
}>();

const {onImport, isImporting} = useImport(props.routeName);

const importOverlayRef = ref();

const onToggleImportOverlay = (event: any) => {
    importOverlayRef.value.toggle(event);
};

const onImportData = ({files}: FileUploadUploaderEvent) => {
    const file = (<File[]>files)[0];
    importOverlayRef.value.hide();
    onImport(file);
};
</script>

<template>
    <div>
        <Button :disabled="isImporting" :loading="isImporting" icon="pi pi-upload"
                label="Import"
                severity="info"
                @click="onToggleImportOverlay"/>
        <OverlayPanel ref="importOverlayRef">
            <div class="">
                <FileUpload
                    ref="fileImportRef"
                    :customUpload="true"
                    :fileLimit="1"
                    :multiple="false"
                    accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                    @uploader="onImportData"
                >
                    <template #header="{chooseCallback, uploadCallback, clearCallback, files}">
                        <div class="flex flex-col items-center gap-2">
                            <div class="flex items-center gap-2">
                                <Button icon="pi pi-file"
                                        label="Choose file" @click="chooseCallback"/>
                                <Button
                                    :disabled="!files || files.length === 0"
                                    icon="pi pi-upload"
                                    label="Import" severity="info"
                                    @click="uploadCallback"/>
                                <Button
                                    :disabled="!files || files.length === 0"
                                    icon="pi pi-times"
                                    label="Clear" severity="danger"
                                    @click="clearCallback"/>
                            </div>
                        </div>
                    </template>
                    <template
                        #content="{files, removeFileCallback}">
                        <div v-if="files.length > 0">
                            <div v-for="(file, index) in files" :key="file.name"
                                 class="flex gap-2 items-center px-4 py-2 bg-surface-100 rounded">
                                <span class="font-semibold" v-text="file.name"/>
                                <span v-text="`${humanFileSize(file.size)}`"/>
                                <Badge severity="warning" value="Pending"/>
                                <Button icon="pi pi-times" severity="danger"
                                        text @click="removeFileCallback(index)"/>
                            </div>
                        </div>
                    </template>
                    <template #empty>
                        <span>Drag and drop file here to import.</span>
                    </template>
                </FileUpload>
            </div>
        </OverlayPanel>
    </div>
</template>

<style lang="scss" scoped>

</style>
