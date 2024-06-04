import {router, usePage} from "@inertiajs/vue3";
import {useDebounceFn} from "@vueuse/core";
import {useToast} from "primevue/usetoast";

export const useCart = () => {
    const toast = useToast();
    const page = usePage();

    const addToCart = (productId: number, quantity: number = 1) => {
        const url = route("cart.store");
        router.post(url, {
            product_id: productId,
            quantity,
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "info",
                    summary: "Message",
                    detail: "Product has been added to cart",
                    life: 3000,
                });
            },
            onError: () => errorHandler()
        });
    };

    const updateQuantity = useDebounceFn((id: string, quantity: number = 1) => {
        const url = route("cart.update", id);
        router.put(url, {
            quantity,
        });
    });

    const removeItem = (id: string) => {
        const url = route("cart.destroy", id);
        router.delete(url, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "info",
                    summary: "Message",
                    detail: "Product has been removed from cart",
                    life: 3000,
                });
            }
        });
    };

    const applyCoupon = (code: string) => {
        const url = route("cart.coupons.store");
        router.post(url, {
            code
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "info",
                    summary: "Message",
                    detail: "Coupon has been applied",
                    life: 3000,
                });
            }
        });
    };

    const removeCoupon = () => {
        const url = route("cart.coupons.destroy");
        router.delete(url, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "info",
                    summary: "Message",
                    detail: "Coupon has been removed",
                    life: 3000,
                });
            }
        });
    };

    const errorHandler = () => {
        const error = page.props.errors.quantity;
        if (error) {
            toast.add({
                severity: "info",
                summary: "Message",
                detail: error,
                life: 3000,
            });
        }
    };

    return {
        addToCart,
        updateQuantity,
        removeItem,
        applyCoupon,
        removeCoupon,
    };
};
