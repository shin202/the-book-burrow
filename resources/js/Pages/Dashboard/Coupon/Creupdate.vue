<script lang="ts" setup>
import {Head, useForm} from "@inertiajs/vue3";
import {computed} from "vue";
import {useToast} from "primevue/usetoast";

import Breadcrumb from "primevue/breadcrumb";
import Dropdown from "primevue/dropdown";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import Calendar from "primevue/calendar";
import Checkbox from "primevue/checkbox";
import Button from "primevue/button";
import Toast from "primevue/toast";
import Tag from "primevue/tag";
import Textarea from "primevue/textarea";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";

import {DiscountTypeEnum, DiscountTypeList} from "@/common/enums";
import {Author, Book, Coupon, Genre, Pagination, User} from "@/types";
import {CreateCouponDto, UpdateCouponDto} from "@/common/dto";
import {useSeverity} from "@/composables";

const props = defineProps<{
    formType: "create" | "update",
    users: Pagination<User[]>,
    books: Pagination<Book[]>,
    authors: Pagination<Author[]>,
    genres: Pagination<Genre[]>,
    coupon?: Coupon
}>();

const toast = useToast();
const {getDiscountSeverity} = useSeverity();

const title = computed(() => {
    return props.formType === "create" ? "Create Coupon" : "Update Coupon";
});

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = computed(() => [
    {label: "All coupons", active: false, route: "dashboard.coupons.index"},
    {label: props.formType === "create" ? "Create new coupons" : "Edit coupons", active: true}
]);

const assignForList = [
    {label: "Users", value: "user"},
    {label: "Authors", value: "author"},
    {label: "Books", value: "book"},
    {label: "Genres", value: "genre"}
];

const assignToList = computed(() => {
    const assignList: { user: any[], book: any[], author: any[], genre: any[] } = {
        user: props.users.data.map(user => ({label: user.username, value: user.id})),
        book: props.books.data.map(book => ({label: book.title, value: book.id})),
        author: props.authors.data.map(author => ({label: author.fullName, value: author.id})),
        genre: props.genres.data.map(genre => ({label: genre.name, value: genre.id}))
    };

    return assignList?.[form.couponable_type!] ?? [];
});

const discountPrefix = computed(() => form.type === DiscountTypeEnum.PERCENTAGE ? "%" : "$");

const form = useForm<CreateCouponDto | UpdateCouponDto>({
    type: props.coupon?.type ?? DiscountTypeEnum.PERCENTAGE,
    code: props.coupon?.code,
    value: props.coupon?.value,
    minimum_order_amount: props.coupon?.minimum_order_amount,
    usage_limit: props.coupon?.usage_limit,
    usage_per_user: props.coupon?.usage_per_user,
    valid_from: window.dayjs(props.coupon?.valid_from).toDate(),
    valid_to: window.dayjs(props.coupon?.valid_to).toDate(),
    couponable_type: props.coupon?.couponable_type ?? "book",
    couponable_id: props.coupon?.couponable_id,
    description: props.coupon?.description,
    is_active: props.coupon?.is_active,
});

const onAssignForChange = () => {
    form.couponable_id = undefined;
};

const createCouponHandler = () => {
    const url = route("dashboard.coupons.store");
    form.post(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "Coupon created successfully",
                life: 3000,
            });
        }
    });
};

const updateCouponHandler = () => {
    const url = route("dashboard.coupons.update", {code: props.coupon?.code});
    form.patch(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "Coupon updated successfully",
                life: 3000,
            });
        }
    });
};

const onSubmit = () => {
    props.formType === "create" && createCouponHandler();
    props.formType === "update" && updateCouponHandler();
};
</script>

<template>
    <Head :title="title"/>
    <Toast/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Add a new coupon</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else :class="{'text-surface-500 pointer-events-none': item.active}"
                              v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <div class="form mt-4">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="form__group flex-1">
                        <label class="form__label form__label--required" for="type">Coupon type</label>
                        <Dropdown v-model="form.type" :options="DiscountTypeList" inputId="type" optionLabel="key"
                                  optionValue="value" placeholder="Choose coupon type">
                            <template #option="{option}">
                                <Tag :severity="getDiscountSeverity(option.value)" :value="option.value"/>
                            </template>
                            <template #value="{value, placeholder}">
                                <Tag v-if="value" :severity="getDiscountSeverity(value)" :value="value"/>
                                <span v-else v-text="placeholder"/>
                            </template>
                        </Dropdown>
                        <Transition name="fade">
                            <small v-if="form.errors.type" class="form__feedback" v-text="form.errors.type"/>
                        </Transition>
                    </div>
                    <div class="form__group flex-1">
                        <label class="form__label form__label--required" for="code">Coupon code</label>
                        <InputText id="code" v-model="form.code" autofocus placeholder="Enter coupon code"/>
                        <Transition name="fade">
                            <small v-if="form.errors.code" class="form__feedback" v-text="form.errors.code"/>
                        </Transition>
                    </div>
                    <div class="form__group flex-1">
                        <label class="form__label form__label--required" for="value">Discount value</label>
                        <InputNumber v-model="form.value" :minFractionDigits="2" :prefix="discountPrefix"
                                     inputId="value" placeholder="Enter discount value"/>
                        <Transition name="fade">
                            <small v-if="form.errors.value" class="form__feedback" v-text="form.errors.value"/>
                        </Transition>
                    </div>
                </div>
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="form__group flex-1">
                        <label class="form__label form__label--optional" for="minimum-order-amount">Minimum order
                            amount</label>
                        <InputNumber v-model="form.minimum_order_amount" :min-fraction-digits="2" currency="USD"
                                     inputId="minimum-order-amount" locale="en-US" mode="currency"
                                     placeholder="Enter minimum order amount"/>
                        <Transition name="fade">
                            <small v-if="form.errors.minimum_order_amount" class="form__feedback"
                                   v-text="form.errors.minimum_order_amount"/>
                        </Transition>
                    </div>
                    <div class="form__group flex-1">
                        <label class="form__label form__label--optional" for="usage-limit">Usage limit</label>
                        <InputNumber v-model="form.usage_limit" :min="0" inputId="usage-limit"
                                     placeholder="Enter usage limit" showButtons/>
                        <Transition name="fade">
                            <small v-if="form.errors.usage_limit" class="form__feedback"
                                   v-text="form.errors.usage_limit"/>
                        </Transition>
                    </div>
                    <div class="form__group flex-1">
                        <label class="form__label form__label--optional" for="usage-limit-per-user">Usage limit per
                            user</label>
                        <InputNumber v-model="form.usage_per_user" :min="0" inputId="usage-limit-per-user"
                                     placeholder="Enter usage limit per user"
                                     showButtons
                                     suffix=" per user"/>
                        <Transition name="fade">
                            <small v-if="form.errors.usage_per_user" class="form__feedback"
                                   v-text="form.errors.usage_per_user"/>
                        </Transition>
                    </div>
                </div>
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="form__group flex-1">
                        <label class="form__label form__label--required" for="valid-from">Valid from</label>
                        <Calendar v-model="form.valid_from" dateFormat="yy-mm-dd" hourFormat="12"
                                  inputId="valid-from" placeholder="Enter coupon valid from date" showTime/>
                        <Transition name="fade">
                            <small v-if="form.errors.valid_from" class="form__feedback"
                                   v-text="form.errors.valid_from"/>
                        </Transition>
                    </div>
                    <div class="form__group flex-1">
                        <label class="form__label form__label--required" for="valid-to">Valid to</label>
                        <Calendar v-model="form.valid_to" dateFormat="yy-mm-dd" hourFormat="12"
                                  inputId="valid-to" placeholder="Enter coupon valid to date" showTime/>
                        <Transition name="fade">
                            <small v-if="form.errors.valid_to" class="form__feedback" v-text="form.errors.valid_to"/>
                        </Transition>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <div class="form__group flex-1">
                        <div class="flex items-center gap-4">
                            <label class="form__label form__label--required" for="assign-for">
                                Assign for
                            </label>
                            <i v-tooltip.right="'What target you want to assign coupons for (users, authors, books or genres)'"
                               class="pi pi-question-circle text-surface-500 text-sm cursor-pointer"/>
                        </div>
                        <Dropdown v-model="form.couponable_type" :options="assignForList" inputId="assign-for"
                                  optionLabel="label" optionValue="value" placeholder="Assign coupon for..."
                                  @update:modelValue="onAssignForChange"
                        />
                        <Transition name="fade">
                            <small v-if="form.errors.couponable_type" class="form__feedback"
                                   v-text="form.errors.couponable_type"/>
                        </Transition>
                    </div>
                    <div class="form__group flex-1">
                        <div class="flex items-center gap-4">
                            <label class="form__label form__label--required" for="assign-to">Assign to</label>
                            <i v-tooltip.right="'Who you want to assign coupons to (users, authors, books or genres)'"
                               class="pi pi-question-circle text-surface-500 text-sm cursor-pointer"/>
                        </div>
                        <Dropdown v-model="form.couponable_id" :maxSelectedLabels="2"
                                  :options="assignToList" :virtualScrollerOptions="{itemSize: 45}"
                                  filter
                                  inputId="assign-to"
                                  optionLabel="label"
                                  optionValue="value"
                                  placeholder="Assign coupon to..."/>
                        <Transition name="fade">
                            <small v-if="form.errors.couponable_id" class="form__feedback"
                                   v-text="form.errors.couponable_id"/>
                        </Transition>
                    </div>
                </div>
                <div class="form__group">
                    <label class="form__label" for="description">Description</label>
                    <Textarea id="description" v-model="form.description" placeholder="Enter coupon description"
                              rows="3"/>
                </div>
                <div class="form__group !flex-row !space-y-0 gap-2 items-center">
                    <Checkbox v-model="form.is_active" binary inputId="active"/>
                    <label class="form__label" for="active">Active now</label>
                </div>
                <div class="form__group">
                    <Button :disalbled="form.processing" :loading="form.processing" icon="pi pi-save" iconPos="right"
                            label="Save" @click="onSubmit"/>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
