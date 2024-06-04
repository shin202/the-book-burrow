<script lang="ts" setup>
import {ref, watch} from "vue";
import {useConfirmPassword} from "@/composables";

import Dialog from "primevue/dialog";
import Password from "primevue/password";
import Button from "primevue/button";

const props = defineProps<{
    callbackFn?: CallableFunction
}>()

const emit = defineEmits<{
    (e: 'closeDialog'): void
}>()

const {confirmPasswordForm, isSuccess, onConfirmPasswordFormSubmit} = useConfirmPassword(props.callbackFn)

const isDialogVisible = ref(true)

const onCloseDialog = () => {
    isDialogVisible.value = false
    confirmPasswordForm.reset()
    emit('closeDialog')
}

watch(isSuccess, (value) => {
    if (value) {
        onCloseDialog()
    }
})
</script>

<template>
    <Dialog v-model:visible="isDialogVisible" :closable="false" modal>
        <template #header>
            <span class="font-semibold text-lg">Confirm Password</span>
        </template>
        <span class="font-medium text-base">For security reason, please confirm your password to continue.</span>
        <div class="flex flex-col gap-2 mt-4">
            <div class="flex flex-col gap-2">
                <Password id="current-password" v-model="confirmPasswordForm.currentPassword" :feedback="false"
                          inputClass="py-3" placeholder="Enter your password"
                          toggleMask/>
                <Transition name="fade">
                    <small v-if="confirmPasswordForm.errors.currentPassword" class="form__feedback"
                           v-text="confirmPasswordForm.errors.currentPassword"/>
                </Transition>
            </div>
            <div class="flex gap-2 items-center self-end mt-4">
                <Button label="Nevermind" severity="secondary" @click="onCloseDialog"/>
                <Button :disabled="confirmPasswordForm.processing" :loading="confirmPasswordForm.processing"
                        label="Confirm"
                        severity="help" @click="onConfirmPasswordFormSubmit"/>
            </div>
        </div>
    </Dialog>
</template>

<style lang="scss" scoped>

</style>
