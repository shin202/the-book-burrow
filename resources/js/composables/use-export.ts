import {computed, onBeforeMount, ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import {useToast} from "primevue/usetoast";
import {EventEnum} from "@/common/enums";
import {useNotification} from "@/composables/use-notification";
import {Notification} from "@/types";

export const useExport = (routeName: string, exporter: string) => {
    const page = usePage();
    const toast = useToast();
    const {showNotification} = useNotification();

    const isExporting = ref(false);
    const totalRows = ref(0);
    const processedRows = ref(0);
    const exportProgress = computed(() => {
        if (totalRows.value === 0) {
            return 0;
        }

        return Math.round((processedRows.value / totalRows.value) * 100);
    });

    const setExporting = (value: boolean) => (isExporting.value = value);

    const setTotalRows = (value: number) => (totalRows.value = value);

    const setProcessedRows = (value: number) => (processedRows.value = value);

    const onExport = () => {
        setExporting(true);
        const url = route(routeName);

        router.get(url, {}, {
            onSuccess: () => {
                toast.add({
                    severity: "info",
                    summary: "Exporting...",
                    detail: "Please wait while we prepare the file for download.",
                    life: 5000,
                });
            },
            onError: () => {
                setExporting(false);
            }
        });
    };

    onBeforeMount(() => {
        window.Echo.private(`exporter.${exporter}.${page.props.auth.user.id}`)
            .listen(".exporter.processing", (data: any) => {
                setTotalRows(data.totalRows);
                setProcessedRows(data.processedRows);
                setExporting(true);
            });

        window.Echo.private(`admin.${page.props.auth.user.id}.notification`)
            .listen(`.${EventEnum.EXPORT_COMPLETED}`, (data: Notification) => {
                showNotification(data);
                setExporting(false);
                setTimeout(() => {
                    setTotalRows(0);
                    setProcessedRows(0);
                }, 3000);
            });
    });

    return {
        isExporting,
        exportProgress,
        onExport,
    };
};
