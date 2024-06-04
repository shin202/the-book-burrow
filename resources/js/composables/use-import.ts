import {router, usePage} from "@inertiajs/vue3";
import {onBeforeMount, ref} from "vue";
import {useToast} from "primevue/usetoast";
import {useNotification} from "@/composables/use-notification";
import {EventEnum} from "@/common/enums";
import {Notification} from "@/types";

export const useImport = (routeName: string) => {
    const page = usePage();
    const toast = useToast();
    const {showNotification} = useNotification();

    const isImporting = ref(false);

    const setImporting = (value: boolean) => (isImporting.value = value);

    const onImport = (file: File) => {
        setImporting(true);
        const url = route(routeName);

        router.post(url, {file}, {
            onSuccess: () => {
                toast.add({
                    severity: "info",
                    summary: "Importing...",
                    detail: "Please wait while we process the file.",
                    life: 5000,
                });

                setImporting(false);
            },
            onError: () => {
                setImporting(false);
            }
        });
    };

    onBeforeMount(() => {
        window.Echo.private(`admin.${page.props.auth.user.id}.notification`)
            .listen(`.${EventEnum.IMPORT_COMPLETED}`, (data: Notification) => {
                showNotification(data);
                setImporting(false);
            });
    });

    return {
        isImporting,
        onImport
    };
};
