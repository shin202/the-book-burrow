<script lang="ts" setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";

import Breadcrumb from "primevue/breadcrumb";
import Dropdown from "primevue/dropdown";
import InputNumber from "primevue/inputnumber";
import Calendar from "primevue/calendar";
import Button from "primevue/button";
import Toast from "primevue/toast";
import Message from "primevue/message";
import Tag from "primevue/tag";

import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import InertiaLink from "@/Components/InertiaLink.vue";

import {Book, Deal, Pagination} from "@/types";
import {DiscountTypeEnum, DiscountTypeList} from "@/common/enums";
import {useToast} from "primevue/usetoast";
import {useSeverity} from "@/composables";

const props = defineProps<{
    formType: "create" | "update",
    books: Pagination<Book[]>,
    deal?: Deal
}>();

const toast = useToast();
const page = usePage();
const {getDiscountSeverity} = useSeverity();

const title = computed(() => {
    return props.formType === "create" ? "Create Deal" : "Update Deal";
});

const homeBreadcrumb = {label: "Dashboard", route: "dashboard.index", active: false};
const breadcrumbItems = computed(() => [
    {label: "All book's deals", active: false, route: "dashboard.deals.index"},
    {label: props.formType === "create" ? "Create new deal" : "Edit book's deal", active: true}
]);

const form = useForm({
    book_id: props.deal?.book_id,
    discount_type: props.deal?.discount_type,
    discount_value: <number>props.deal?.discount_value,
    start_date: props.deal?.start_date ? window.dayjs(props.deal.start_date).toDate() : null,
    end_date: props.deal?.end_date ? window.dayjs(props.deal.end_date).toDate() : null
});

const errorMessage = ref("");

const createDealHandler = () => {
    const url = route("dashboard.deals.store");
    form.post(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Created",
                detail: "Deal has been created successfully",
                life: 3000
            });

            form.reset();
        },
        onError: () => {
            errorMessage.value = page.props.errors.message;
            setTimeout(() => {
                errorMessage.value = "";
            }, 3000);
        }
    });
};

const updateDealHandler = () => {
    const url = route("dashboard.deals.update", {id: props.deal?.id});
    form.patch(url, {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Updated",
                detail: "Deal has been updated successfully",
                life: 3000
            });
        },
        onError: () => {
            errorMessage.value = page.props.errors.message;
            setTimeout(() => {
                errorMessage.value = "";
            }, 3000);
        }
    });
};

const onSubmit = (event: Event) => {
    event.preventDefault();

    props.formType === "create" && createDealHandler();
    props.formType === "update" && updateDealHandler();
};
</script>

<template>
    <Head :title="title"/>
    <Toast/>
    <DashboardLayout>
        <div class="container mx-auto px-6 mt-4">
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Create a new deal</h1>
                <Breadcrumb :home="homeBreadcrumb" :model="breadcrumbItems" class="text-sm">
                    <template #item="{item}">
                        <InertiaLink v-if="item.route" :class="{'text-surface-500 pointer-events-none': item.active}"
                                     :href="route(item.route)" v-text="item.label"/>
                        <span v-else :class="{'text-surface-500 pointer-events-none': item.active}"
                              v-text="item.label"/>
                    </template>
                </Breadcrumb>
            </div>
            <form class="form mt-4">
                <Transition name="fade">
                    <Message v-if="errorMessage" :closable="false" severity="error">
                        <span v-text="errorMessage"/>
                    </Message>
                </Transition>
                <div class="form__group"><label class="form__label form__label--required"
                                                for="book-id">Book</label>
                    <Dropdown v-model="form.book_id" :autoFilterFocus="true" :disabled="formType === 'update'"
                              :options="books.data"
                              :virtualScrollerOptions="{itemSize: 40}"
                              filter
                              inputId="book-id" optionLabel="title" optionValue="id" placeholder="Choose a book"/>
                    <Transition name="fade">
                        <small v-if="form.errors.book_id" class="form__feedback" v-text="form.errors.book_id"/>
                    </Transition>
                </div>
                <div class="flex flex-wrap gap-4">
                    <div class="form__group flex-1">
                        <label class="form__label form__label--required" for="discount-type">Discount type</label>
                        <Dropdown v-model="form.discount_type" :options="DiscountTypeList" inputId="discount-type"
                                  optionLabel="key"
                                  optionValue="value" placeholder="Choose a discount type">
                            <template #option="{option}">
                                <Tag :severity="getDiscountSeverity(option.value)" :value="option.value"/>
                            </template>
                            <template #value="{value, placeholder}">
                                <Tag v-if="value" :severity="getDiscountSeverity(value)" :value="value"/>
                                <span v-else v-text="placeholder"/>
                            </template>
                        </Dropdown>
                        <Transition name="fade">
                            <small v-if="form.errors.discount_type" class="form__feedback"
                                   v-text="form.errors.discount_type"/>
                        </Transition>
                    </div>
                    <div class="form__group flex-1">
                        <label class="form__label form__label--required" for="discount-value">Discount value</label>
                        <InputNumber v-if="form.discount_type === DiscountTypeEnum.FIXED" v-model="form.discount_value"
                                     currency="USD"
                                     inputId="discount-value" locale="en-US" mode="currency"
                                     placeholder="Enter discount value"/>
                        <InputNumber v-else v-model="form.discount_value" inputId="discount-value" mode="decimal"
                                     placeholder="Enter discount value" suffix="%"/>
                        <Transition name="fade">
                            <small v-if="form.errors.discount_value" class="form__feedback"
                                   v-text="form.errors.discount_value"/>
                        </Transition>
                    </div>
                </div>
                <div class="flex flex-wrap gap-4">
                    <div class="form__group flex-1">
                        <label class="form__label form__label--required" for="start-date">Start date</label>
                        <Calendar v-model="form.start_date" dateFormat="yy-mm-dd" hourFormat="12" iconDisplay="input"
                                  inputId="start-date"
                                  placeholder="Choose a start date" showTime/>
                        <Transition name="fade">
                            <small v-if="form.errors.start_date" class="form__feedback"
                                   v-text="form.errors.start_date"/>
                        </Transition>
                    </div>
                    <div class="form__group flex-1">
                        <label class="form__label form__label--required" for="end-date">End date</label>
                        <Calendar v-model="form.end_date" dateFormat="yy-mm-dd" hourFormat="12" iconDisplay="input"
                                  inputId="end-date"
                                  placeholder="Choose a end date" showTime/>
                        <Transition name="fade">
                            <small v-if="form.errors.end_date" class="form__feedback" v-text="form.errors.end_date"/>
                        </Transition>
                    </div>
                </div>
                <div class="form__group">
                    <Button :disabled="form.processing" :loading="form.processing" icon="pi pi-save" iconPos="right"
                            label="Save"
                            type="submit" @click="onSubmit"/>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>

<style lang="scss" scoped>

</style>
