import {computed, onBeforeMount} from "vue";
import {usePage} from "@inertiajs/vue3";
import {capitalize, filter, find, map, split} from "lodash";
import {useToast} from "primevue/usetoast";
import {PageProps} from "@/types";

interface GeneralSettings {
    id: number;
    key: string;
    value: string;
    isLoading: boolean;
}

/**
 * General application settings
 *
 */
export const useSetting = () => {
    const page = usePage<PageProps<{ general_settings: GeneralSettings[] }>>();
    const toast = useToast();

    const settings = computed(() => {
        return map(filter(page.props.general_settings, o => o.key !== "site.logo"), settingMapper);
    });

    const settingMapper = (setting: GeneralSettings) => {
        const label = split(setting.key, ".")
            .map(capitalize).join(" ");

        return {
            ...setting,
            label,
        };
    };

    const setting = (key: string) => {
        const value = find(page.props.general_settings, {key})?.value;
        if (key === "site.logo") {
            return `${import.meta.env.VITE_APP_URL}/${value}`;
        }

        return value;
    };

    const setLoading = (key: string, value: boolean) => {
        page.props.general_settings = page.props.general_settings.map(setting => {
            if (setting.key === key) {
                setting.isLoading = value;
            }

            return setting;
        });
    };

    const updateSettingHandler = async (url: string, data: FormData) => {
        try {
            const res = await window.axios.post(url, data, {
                headers: {
                    "Content-Type": "multipart/form-data"
                }
            });

            toast.add({
                severity: "success",
                summary: "Success",
                detail: "General settings has been updated",
                life: 3000
            });
        } catch (e) {
            console.log(e);
        }
    };

    const setSetting = async (key: string, value: any) => {
        const url = route("dashboard.settings.general.update", {key});

        const formData = new FormData();
        formData.append("_method", "patch");
        formData.append("value", value);

        setLoading(key, true);
        await updateSettingHandler(url, formData);
        setLoading(key, false);
    };

    onBeforeMount(() => {
        page.props.general_settings = page.props.general_settings.map(setting => {
            return {
                ...setting,
                isLoading: false
            };
        });
    });

    return {
        settings,
        setting,
        setSetting
    };
};
