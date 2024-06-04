<script lang="ts">
import LandingLayout from "@/Layouts/LandingLayout.vue";

export default {
    layout: LandingLayout
};
</script>
<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";

import Breadcrumb from "primevue/breadcrumb";
import Tag from "primevue/tag";
import Rating from "primevue/rating";
import InputNumber from "primevue/inputnumber";
import Button from "primevue/button";
import Editor from "primevue/editor";
import Avatar from "primevue/avatar";
import ProgressBar from "primevue/progressbar";

import InertiaLink from "@/Components/InertiaLink.vue";

import {Book, BookDetail, Pagination} from "@/types";
import {camelCase, upperCase} from "lodash";
import {useCart, useReview, useSeverity} from "@/composables";
import {computed, ref} from "vue";
import {useDateFormat, useParallax} from "@vueuse/core";
import BookCard from "@/Components/BookCard.vue";

const props = defineProps<{
    book: BookDetail,
    relatedBooks: {
        userAlsoEnjoyed: Pagination<Book[]>,
        samesLikeThis: Pagination<Book[]>
    }
}>();

const {getStockStatusSeverity} = useSeverity();

const isExpandedDescription = ref(false);

const quantity = ref(1);

const onToggleDescription = () => {
    isExpandedDescription.value = !isExpandedDescription.value;
};

const {ratingForm, reviewForm, onSubmitReview, onSubmitRating} = useReview();

const totalRatings = computed(() => {
    return props.book.rating_group_count.reduce((acc, item) => acc + item.count, 0);
});

const coverImageRef = ref();

const {tilt, roll} = useParallax(coverImageRef);

const imageStyle = computed(() => {
    return {
        transform: `rotateX(${roll.value * 20}deg) rotateY(${tilt.value * 20}deg) scale(${1 + tilt.value / 10})`,
    };
});

const {addToCart} = useCart();
</script>

<template>
    <Head :title="book.title"/>
    <div class="container mx-auto px-3 lg:px-6 mt-4">
        <Breadcrumb :home="{label: 'Home', route: 'landing.index', active: false}"
                    :model="[{label: book.genres?.[0].name, active: false}, {label: book.title.toUpperCase(), active: true}]"
                    class="!bg-primary-200">
            <template #item="{item}">
                <InertiaLink v-if="item.route" :class="{'text-primary-800 pointer-events-none': item.active}"
                             :href="route(item.route)"
                             class="uppercase" v-text="item.label"/>
                <span v-else :class="{'text-primary-800 pointer-events-none': item.active}" class="uppercase"
                      v-text="item.label"/>
            </template>
        </Breadcrumb>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center mt-4">
            <div class="p-8 shadow-lg rounded-xl w-full h-full">
                <div class="h-[30rem] bg-primary-300 w-full overflow-hidden">
                    <img ref="coverImageRef" :src="book.cover_image" :style="imageStyle" alt="Book cover"
                         class="w-full h-full object-contain mx-auto"/>
                </div>
            </div>
            <div class="p-8 shadow-lg rounded-xl flex-1 w-full flex flex-col divide-y space-y-3">
                <div class="flex flex-col space-y-4 py-4">
                    <Tag :severity="getStockStatusSeverity(book.stock_status)"
                         :value="upperCase(camelCase(book.stock_status))" class="w-fit"/>
                    <h1 class="text-4xl font-serif" v-text="book.title"/>
                    <div class="divide-x flex flex-wrap items-center gap-4 text-sm">
                        <InertiaLink :href="route('authors.show', {slug: book.authors?.[0].slug})"
                                     class="px-2">
                            <span class="text-surface-500">Author</span>:
                            <span class="hover:underline hover:text-primary">{{ book.authors?.[0].full_name }}</span>
                        </InertiaLink>
                        <span class="px-2">
                            <span class="flex items-center gap-2">
                                <Rating :cancel="false" :modelValue="book.average_rating" readonly>
                                    <template #officon>
                                        <i class="pi pi-star text-surface-400"/>
                                    </template>
                                    <template #onicon>
                                        <i class="pi pi-star-fill text-orange-400"/>
                                    </template>
                                </Rating>
                                <span v-text="book.average_rating"/>
                            </span>
                        </span>
                        <span class="px-2">
                            <span class="text-surface-500">ISBN</span>: {{ book.isbn }}
                        </span>
                    </div>
                </div>
                <div class="px-2 py-4 flex flex-col space-y-4">
                    <div>
                        <div v-if="book.has_discount">
                            <span class="text-xl font-bold text-primary-900"
                                  v-text="book.discount_price"/>
                            <span class="text-sm text-surface-500 line-through pl-2"
                                  v-text="book.original_price"/>
                        </div>
                        <span v-else class="text-xl font-bold text-primary-900" v-text="book.original_price"/>
                    </div>
                    <div>
                        <p :class="{'line-clamp-2': !isExpandedDescription}" class="text-sm text-surface-700"
                           v-html="book.description"/>
                        <Button :icon="isExpandedDescription ? 'pi pi-chevron-up' : 'pi pi-chevron-down'"
                                :label="isExpandedDescription ? 'Show less' : 'Show more'"
                                class="mt-2"
                                iconPos="right" severity="secondary" text
                                @click="onToggleDescription"/>
                    </div>
                    <div class="flex flex-wrap gap-4 items-center">
                        <div>
                            <label class="text-surface-500 text-sm mb-2 block" for="quantity">Quantity</label>
                            <InputNumber v-model="quantity" :min="1" buttonLayout="horizontal" inputClass="w-20"
                                         inputId="quantity"
                                         mode="decimal"
                                         showButtons>
                                <template #incrementbuttonicon>
                                    <span class="pi pi-plus"/>
                                </template>
                                <template #decrementbuttonicon>
                                    <span class="pi pi-minus"/>
                                </template>
                            </InputNumber>
                        </div>
                        <Button class="!rounded-full px-8 py-4 !bg-primary-800" icon="pi pi-shopping-cart"
                                label="Add to cart" @click="addToCart(book.id, quantity)"/>
                        <Button icon="pi pi-heart" label="Add to wishlist" severity="secondary" text/>
                    </div>
                </div>
                <div class="px-2 py-4 flex flex-wrap gap-2 text-sm divide-x items-center">
                    <p class="px-2">
                        <span class="text-surface-500">Publisher: </span>
                        <span
                            class="font-bold underline underline-offset-4 decoration-primary-800 px-2 relative hover:text-primary-800">
                        {{ book.publisher.name }}</span>
                    </p>
                    <p class="px-2">
                        <span class="text-surface-500">Publication date: </span>
                        <span>{{ useDateFormat(book.publication_date, "MMM-DD-YYYY", {locales: "en-US"}).value }}</span>
                    </p>
                    <p class="px-2">
                        <span class="text-surface-500">Genres: </span>
                        <span v-for="genre in book.genres" :key="genre.id"
                              class="font-bold underline underline-offset-4 decoration-primary-800 px-2 relative hover:text-primary-800">
                            {{ genre.name }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Reviews & Ratings -->
        <div class="mt-14">
            <h2 class="font-bold font-serif text-xl">Reviews & Ratings</h2>
            <div class="flex justify-center items-center flex-col text-2xl mt-4 space-y-6">
                <div v-if="!book.is_rated">
                    <p class="font-serif">
                        What do <span class="italic">you</span> think?
                    </p>
                    <div class="flex flex-col justify-center items-center space-y-2">
                        <Rating v-model="ratingForm.rating" :cancel="false">
                            <template #officon>
                                <i class="pi pi-star text-surface-400"/>
                            </template>
                            <template #onicon>
                                <i class="pi pi-star-fill text-orange-400"/>
                            </template>
                        </Rating>
                        <Button :disabled="ratingForm.processing" :loading="ratingForm.processing"
                                label="Rate this book"
                                severity="secondary" text @click="onSubmitRating(book.slug)"/>
                    </div>
                </div>
                <div v-else class="flex flex-col justify-center items-center gap-4">
                    <p class="font-serif">
                        You have already rated this book
                    </p>
                    <Rating :cancel="false" :modelValue="book.user_rating" readonly>
                        <template #officon>
                            <i class="pi pi-star text-surface-400"/>
                        </template>
                        <template #onicon>
                            <i class="pi pi-star-fill text-orange-400"/>
                        </template>
                    </Rating>
                </div>
                <div class="w-full flex flex-col">
                    <div>
                        <label class="text-surface-500 text-sm mb-2 block" for="review">Review</label>
                        <Editor id="review" v-model="reviewForm.review_text"
                                class="w-full text-base"
                                editorStyle="height: 200px;"
                                placeholder="Write your review here"/>
                        <Transition name="fade">
                            <small v-if="reviewForm.errors.review_text" class="form__feedback"
                                   v-text="reviewForm.errors.review_text"/>
                        </Transition>
                    </div>
                    <Button :disabled="reviewForm.processing || !reviewForm.review_text"
                            :loading="reviewForm.processing"
                            class="!rounded-full !bg-primary-800 self-end mt-2 text-lg" icon="pi pi-send"
                            label="Submit review"
                            @click="onSubmitReview(book.slug)"/>
                </div>
            </div>
            <div>
                <h3 class="font-bold font-serif text-lg mt-6">Reviews <span class="text-surface-500 text-sm">({{
                        book.reviews_count
                    }})</span>
                </h3>
                <div class="mt-8 flex flex-col justify-center items-center">
                    <div class="text-3xl flex items-center gap-3">
                        <Rating :cancel="false" :modelValue="book.average_rating" readonly>
                            <template #officon>
                                <i class="pi pi-star text-surface-400"/>
                            </template>
                            <template #onicon>
                                <i class="pi pi-star-fill text-orange-400"/>
                            </template>
                        </Rating>
                        <div class="flex items-center divide-x">
                            <span class="font-bold text-2xl font-serif px-1.5" v-text="book.average_rating"/>
                            <span class="text-surface-500 text-sm px-1.5">{{ totalRatings }} ratings</span>
                            <span class="text-surface-500 text-sm px-1.5">{{ book.reviews_count }} reviews</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-center w-full max-w-[35rem] mt-4 gap-6">
                        <div v-for="(item, index) in book.rating_group_count" :key="index"
                             class="flex items-center justify-center w-full gap-4">
                            <span class="font-bold underline">{{ item.rating }} stars</span>
                            <ProgressBar :showValue="false" :value="item.count / totalRatings * 100"
                                         class="mt-2 flex-1"/>
                            <span class="text-surface-500">{{ item.count }} ratings</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-8 mt-8">
                    <div v-for="(review, index) in book.reviews" :key="index"
                         class="py-8 px-6 bg-primary-300/50 rounded-lg min-h-40 flex flex-col lg:flex-row gap-8 relative">
                        <div class="flex flex-col justify-center items-center gap-2 min-w-60">
                            <div class="flex flex-col gap-2 justify-center items-center">
                                <Avatar :label="review.username.charAt(0).toUpperCase()" class="!bg-primary-200"
                                        shape="circle" size="xlarge"/>
                                <span class="text-lg font-bold" v-text="review.username"/>
                            </div>
                            <Rating :cancel="false" :modelValue="review.rating" readonly>
                                <template #officon>
                                    <i class="pi pi-star text-surface-400"/>
                                </template>
                                <template #onicon>
                                    <i class="pi pi-star-fill text-orange-400"/>
                                </template>
                            </Rating>
                        </div>
                        <p class="text-surface-700 text-base" v-html="review.review_text"/>
                        <span
                            class="text-surface-500 text-xs absolute right-4 top-2">{{
                                useDateFormat(review.created_at, "MMM-DD-YYYY hh:mm:ss", {locales: "en-US"}).value
                            }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Reviews & Ratings -->

        <!-- Related Books -->
        <div class="mt-14">
            <h2 class="font-bold font-serif text-xl">Customers also <span class="italic"> enjoyed</span></h2>
            <div class="mt-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8 px-4">
                    <BookCard v-for="book in relatedBooks.userAlsoEnjoyed.data" :key="book.slug" :book="book"/>
                </div>
            </div>
        </div>

        <div class="mt-14 mb-8">
            <h2 class="font-bold font-serif text-xl">Sames like this</h2>
            <div class="mt-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8 px-4">
                    <BookCard v-for="book in relatedBooks.samesLikeThis.data" :key="book.slug" :book="book"/>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>
