import {useForm} from "@inertiajs/vue3";
import {useToast} from "primevue/usetoast";

export const useReview = () => {
    const toast = useToast();

    const ratingForm = useForm({
        rating: undefined,
    });

    const reviewForm = useForm({
        review_text: ""
    });

    const onSubmitReview = (slug: string) => {
        const url = route("books.reviews.store", {slug});
        reviewForm.post(url, {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Review submitted",
                    detail: "Thank you for your review",
                    life: 3000
                });
            }
        });
    };

    const onSubmitRating = (slug: string) => {
        const url = route("books.ratings.store", {slug});
        ratingForm.post(url, {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Rating submitted",
                    detail: "Thank you for your rating",
                    life: 3000
                });
            }
        });
    };

    return {
        ratingForm,
        reviewForm,
        onSubmitReview,
        onSubmitRating
    };
};
