import {ref} from "vue";
import {useForm} from "@inertiajs/vue3";

export const useConfirmPassword = (callback?: CallableFunction) => {
    const confirmPasswordForm = useForm({
        currentPassword: '',
    });
    const isSuccess = ref(false);

    const setSuccess = (value: boolean) => (isSuccess.value = value);

    const confirmPasswordHandler = () => {
        const url = route('dashboard.password.confirm');
        confirmPasswordForm.post(url, {
            preserveState: true,
            onSuccess: () => {
                if (callback) {
                    callback();
                }
                setSuccess(true);
            }
        });
    }

    const onConfirmPasswordFormSubmit = () => {
        confirmPasswordHandler();
    }

    return {
        confirmPasswordForm,
        isSuccess,
        onConfirmPasswordFormSubmit,
    }
}
