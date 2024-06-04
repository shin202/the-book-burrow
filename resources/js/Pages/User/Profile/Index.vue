<script lang="ts">
import LandingLayout from "@/Layouts/LandingLayout.vue";

export default {
    layout: [LandingLayout]
};
</script>

<script lang="ts" setup>
import {computed, ref} from "vue";
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import {useToast} from "primevue/usetoast";

import Avatar from "primevue/avatar";
import Button from "primevue/button";
import Menu from "primevue/menu";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import RadioButton from "primevue/radiobutton";
import Calendar from "primevue/calendar";
import Panel from "primevue/panel";
import Message from "primevue/message";
import {Cropper, CircleStencil} from "vue-advanced-cropper";

import {UserGenderEnum, UserStatusEnum} from "@/common/enums";
import {PageProps, User} from "@/types";
import UserLayout from "@/Layouts/UserLayout.vue";

const page = usePage<PageProps<any>>();
const toast = useToast();

const user = computed<User>(() => {
    return page.props.auth.user;
});

const avatarLabel = computed(() => {
    return user.value.avatar ? undefined : user.value.username.charAt(0).toUpperCase();
});

const userStatus = computed(() => {
    return user.value.status === UserStatusEnum.ACTIVE ? "pi pi-verified text-green-500" : "pi pi-exclamation-circle text-red-500";
});

const personalInfoMenu = ref();
const isDisabledPersonalInfo = ref(true);
const personalInfoItems = ref([
    {
        label: "Edit",
        icon: "pi pi-pencil",
        command: () => {
            isDisabledPersonalInfo.value = false;
        }
    },
]);

const avatarMenu = ref();
const avatarMenuItems = ref([
    {
        label: "Upload a photo...",
        icon: "pi pi-upload",
        command: () => {
            avatarUploader.value.click();
        }
    },
    {
        label: "Remove photo...",
        icon: "pi pi-trash",
        disabled: !user.value.avatar,
        command: () => onDeleteAvatar(),
    }
]);

const avatarForm = useForm({
    avatar: null,
});
const avatarUploader = ref();
const avatarPreview = ref();
const cropper = ref();
const isShowCropper = ref(false);

const personalInfoForm = useForm({
    firstName: user.value.profile?.first_name,
    lastName: user.value.profile?.last_name,
    gender: user.value.profile?.gender,
    dateOfBirth: user.value.profile?.date_of_birth,
    nationality: user.value.profile?.nationality,
});

const onTogglePersonalInfoMenu = (event: any) => {
    personalInfoMenu.value.toggle(event);
};

const onToggleAvatarMenu = (event: any) => {
    avatarMenu.value.toggle(event);
};

const onAvatarChange = (event: Event) => {
    const file = (<HTMLInputElement>event.target).files?.[0];
    if (!file) return;
    avatarPreview.value = URL.createObjectURL(file);
    isShowCropper.value = true;
};

const onUploadAvatar = () => {
    const {canvas} = cropper.value.getResult();
    if (canvas) {
        canvas.toBlob((blob: any) => {
            avatarForm.avatar = blob;
            router.post(route("my-profile.update-avatar"), {
                _method: "PUT",
                avatar: avatarForm.avatar,
            });
            onClearAvatar();
        });
    }
};

const onClearAvatar = () => {
    avatarForm.avatar = null;
    avatarPreview.value = null;
    isShowCropper.value = false;
    page.props.errors.avatar = null;
};

const onDeleteAvatar = () => {
    router.delete(route("my-profile.delete-avatar"));
};

const onCancelEditPersonalInfo = () => {
    isDisabledPersonalInfo.value = true;
    onResetPersonalInfo();
};

const onSavePersonalInfo = () => {
    router.patch(route("my-profile.update-personal-information"), {
        first_name: personalInfoForm.firstName,
        last_name: personalInfoForm.lastName,
        gender: personalInfoForm.gender,
        date_of_birth: personalInfoForm.dateOfBirth ? new Date(personalInfoForm.dateOfBirth).toISOString().split("T")[0] : null,
        nationality: personalInfoForm.nationality,
    });
    isDisabledPersonalInfo.value = true;
};

const onResetPersonalInfo = () => {
    personalInfoForm.firstName = user.value?.profile?.first_name;
    personalInfoForm.lastName = user.value?.profile?.last_name;
    personalInfoForm.gender = user.value?.profile?.gender;
    personalInfoForm.dateOfBirth = user.value?.profile?.date_of_birth;
    personalInfoForm.nationality = user.value?.profile?.nationality;
};

router.on("finish", () => {
    if (page.props.toast?.message) {
        toast.add({severity: page.props.toast.severity, summary: page.props.toast.message, life: 3000});
    }
});
</script>

<template>
    <Head title="My profile"/>
    <UserLayout>
        <div class="mt-4 px-6 flex flex-col gap-4 w-full">
            <div class="flex justify-center items-center">
                <div class="relative">
                    <Avatar :image="user.avatar" :label="avatarLabel"
                            class="!bg-primary-400 !text-surface-500 w-44 h-44 !text-4xl"
                            shape="circle"
                            size="xlarge"/>
                    <Button class="!absolute bottom-3 -left-2 shadow-md" icon="pi pi-pencil" label="Edit" outlined
                            severity="help"
                            size="small" @click="onToggleAvatarMenu"/>
                    <Menu ref="avatarMenu" :model="avatarMenuItems" popup>
                        <template #item="{item}">
                            <button :class="{
                                'opacity-50': item.disabled
                            }" :disabled="item.disabled" class="flex items-center space-x-2 px-4 py-2 select-none">
                                <i :class="item.icon"/>
                                <span v-text="item.label"/>
                            </button>
                        </template>
                    </Menu>
                    <input id="avatar" ref="avatarUploader" accept="image/*" hidden name="avatar" type="file"
                           @input="onAvatarChange">
                    <Dialog v-model:visible="isShowCropper" :closable="false" class="w-full lg:max-w-[45rem]"
                            header="Preview upload"
                            modal>
                        <Cropper ref="cropper" :src="avatarPreview" :stencil-component="CircleStencil"
                                 :stencil-props="{
                                aspectRatio: 3/4,
                                movable: true,
                                resizable: true,
                            }">

                        </Cropper>
                        <small class="text-red-500" v-text="page.props.errors.avatar"/>
                        <template #footer>
                            <Button class="px-6 py-2" label="Cancel" outlined severity="secondary"
                                    @click="onClearAvatar"/>
                            <Button class="px-6 py-2" label="Save" outlined severity="success"
                                    @click="onUploadAvatar"/>
                        </template>
                    </Dialog>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-center items-center space-x-2">
                    <h2 class="text-2xl font-bold text-center">{{ user.username }}</h2>
                    <i :class="userStatus"/>
                </div>
                <p class="text-center text-gray-500">{{ user.email }}</p>
                <Message v-if="user.status === UserStatusEnum.INACTIVE" :closable="false" severity="warn">
                <span>
                    <span>Your account is not verified. Please check your email to verify your account.</span>
                    <a class="text-blue-500 hover:underline hover:text-blue-600" href="#"> Resend email verification</a>
                </span>
                </Message>
            </div>
            <div class="">
                <Panel class="mt-4">
                    <template #header>
                        <h3 class="text-lg font-bold flex items-center">
                            <i class="pi pi-user pr-2"/>
                            <span>Personal Information</span>
                        </h3>
                    </template>
                    <div class="mt-2 flex flex-col space-y-4">
                        <div>
                            <div class="flex items-center space-x-2 relative">
                                <label for="firstName">First name:</label>
                                <Transition>
                                    <span v-if="isDisabledPersonalInfo" v-text="personalInfoForm.firstName ?? 'None'"/>
                                    <InputText v-else id="firstName" v-model="personalInfoForm.firstName"
                                               class="flex-1" placeholder="Enter your first name"/>
                                </Transition>
                            </div>
                            <small class="text-red-500" v-text="page.props.errors.first_name"/>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2 relative">
                                <label for="lastName">Last name:</label>
                                <Transition>
                                    <span v-if="isDisabledPersonalInfo" v-text="personalInfoForm.lastName ?? 'None'"/>
                                    <InputText v-else id="lastName" v-model="personalInfoForm.lastName"
                                               class="flex-1" placeholder="Enter your last name"/>
                                </Transition>
                            </div>
                            <small class="text-red-500" v-text="page.props.errors.last_name"/>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2 relative">
                                <span>Gender:</span>
                                <Transition>
                                    <span v-if="isDisabledPersonalInfo" v-text="personalInfoForm.gender ?? 'None'"/>
                                    <div v-else class="flex justify-center items-center space-x-4">
                                        <div class="flex justify-center items-center space-x-2">
                                            <label for="male">Male</label>
                                            <RadioButton v-model="personalInfoForm.gender" :value="UserGenderEnum.M"
                                                         inputId="male"
                                                         name="gender"/>
                                        </div>
                                        <div class="flex justify-center items-center space-x-2">
                                            <label for="female">Female</label>
                                            <RadioButton v-model="personalInfoForm.gender" :value="UserGenderEnum.F"
                                                         inputId="female"
                                                         name="gender"/>
                                        </div>
                                    </div>
                                </Transition>
                            </div>
                            <small class="text-red-500" v-text="page.props.errors.gender"/>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2 relative">
                                <label for="dateOfBirth">Date Of Birth:</label>
                                <Transition>
                                    <span v-if="isDisabledPersonalInfo"
                                          v-text="personalInfoForm.dateOfBirth ?? 'None'"/>
                                    <Calendar v-else v-model="personalInfoForm.dateOfBirth" class="flex-1"
                                              dateFormat="dd/mm/yy" inputId="dateOfBirth"
                                              placeholder="Enter your dob" showIcon/>
                                </Transition>
                            </div>
                            <small class="text-red-500" v-text="page.props.errors.date_of_birth"/>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2 relative">
                                <label for="nationality">Nationality:</label>
                                <Transition>
                                    <span v-if="isDisabledPersonalInfo"
                                          v-text="personalInfoForm.nationality ?? 'None'"/>
                                    <InputText v-else id="nationality" v-model="personalInfoForm.nationality"
                                               class="flex-1" placeholder="Enter your nationality"/>
                                </Transition>
                            </div>
                            <small class="text-red-500" v-text="page.props.errors.nationality"/>
                        </div>
                    </div>
                    <template #icons>
                        <Button icon="pi pi-cog" severity="contrast" text @click="onTogglePersonalInfoMenu"/>
                        <Menu ref="personalInfoMenu" :model="personalInfoItems" popup/>
                    </template>
                    <template #footer>
                        <div v-if="!isDisabledPersonalInfo" class="flex justify-end gap-3">
                            <Button label="Cancel" severity="secondary" text @click="onCancelEditPersonalInfo"/>
                            <Button label="Save" severity="success" text @click="onSavePersonalInfo"/>
                        </div>
                    </template>
                </Panel>
                <!--            <Panel class="mt-4">-->
                <!--                <template #header>-->
                <!--                    <h3 class="text-lg font-bold flex items-center">-->
                <!--                        <i class="pi pi-receipt pr-2"/>-->
                <!--                        <span>Billing Information</span>-->
                <!--                    </h3>-->
                <!--                </template>-->
                <!--                <div class="mt-2 grid grid-cols-1 lg:grid-cols-2 gap-4 items-center">-->
                <!--                    <Card v-for="i in 4" class="border border-surface-200">-->
                <!--                        <template #title>Shipping Address #{{ i }}</template>-->
                <!--                        <template #content>-->
                <!--                            <div class="">-->
                <!--                                <p>Full name: </p>-->
                <!--                                <p>Phone number:</p>-->
                <!--                                <p>Email address: </p>-->
                <!--                                <p>Address: </p>-->
                <!--                                <p>City: </p>-->
                <!--                                <p>Country: </p>-->
                <!--                                <p>Postal code: </p>-->
                <!--                                <p>State or province:</p>-->
                <!--                            </div>-->
                <!--                        </template>-->
                <!--                        <template #footer>-->
                <!--                            <div class="flex justify-center items-center gap-3 mt-1">-->
                <!--                                <Button class="px-6 py-2 w-full" label="Edit"/>-->
                <!--                                <Button class="px-6 py-2 w-full" label="Delete" severity="danger"/>-->
                <!--                            </div>-->
                <!--                        </template>-->
                <!--                    </Card>-->
                <!--                </div>-->
                <!--            </Panel>-->
            </div>
        </div>
    </UserLayout>
</template>

<style lang="scss" scoped>

</style>
