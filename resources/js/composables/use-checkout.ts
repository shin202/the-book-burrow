import {onBeforeMount, ref} from "vue";
import {loadStripe} from "@stripe/stripe-js";
import {useForm, usePage} from "@inertiajs/vue3";

export const useCheckout = () => {
    const page = usePage<any>();
    const isProcessing = ref(false);
    const errorMessage = ref("");

    const stripe = ref();
    const paymentElement = ref();
    const form = useForm({
        first_name: null,
        last_name: null,
        billing_email: null,
        billing_phone: null,
        billing_country: null,
        billing_address: null,
        billing_city: null,
        billing_state: null,
        billing_zip: null,
        notes: null,
        stripe_payment_method_id: null,
    });

    const setProcessing = (value: boolean) => (isProcessing.value = value);

    const setErrorMessage = (message: string = "") => (errorMessage.value = message);

    const clearErrorMessage = () => setErrorMessage();

    const initStripe = async () => {
        stripe.value = await loadStripe(import.meta.env.VITE_STRIPE_KEY);
        const element = stripe.value.elements({clientSecret: page.props.intent.client_secret});
        paymentElement.value = element.create("payment");
        paymentElement.value.mount("#payment-element");
    };

    const checkout = async (e: Event) => {
        e.preventDefault();

        setProcessing(true);

        const billing_details = {
            name: `${form.first_name} ${form.last_name}`,
            email: form.billing_email,
            phone: form.billing_phone,
            address: {
                line1: form.billing_address,
                city: form.billing_city,
                state: form.billing_state,
                postal_code: form.billing_zip,
                country: form.billing_country,
            },
        };

        const {error, paymentMethod} = await stripe.value.createPaymentMethod("card", paymentElement.value, {
            billing_details,
        });

        if (error) {
            console.error(error);
            setProcessing(false);
        } else {
            const url = route("checkout.process");
            form.stripe_payment_method_id = paymentMethod.id;
            form.post(url, {
                onSuccess: () => {
                    setProcessing(false);
                },
                onError: (errors) => {
                    setProcessing(false);
                    setErrorMessage(errors.message);
                },
            });
        }
    };

    const placeOrder = async (e: Event) => {
        e.preventDefault();
        const url = route("order.store");
        form.post(url, {
            onSuccess: () => {
                setProcessing(false);
            },
            onError: (errors) => {
                setProcessing(false);
                setErrorMessage(errors.message);
            },
        });
    };

    onBeforeMount(() => initStripe());

    return {
        form,
        isProcessing,
        errorMessage,
        clearErrorMessage,
        checkout,
        placeOrder,
    };
};
