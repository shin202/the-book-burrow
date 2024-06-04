import {ref} from "vue";
import {useDebounceFn, useUrlSearchParams} from "@vueuse/core";
import {router} from "@inertiajs/vue3";

export const useSearch = () => {
    const params = useUrlSearchParams("history");

    const search = ref<string>(<string>params?.search);
    const suggestions = ref();

    const onSearch = useDebounceFn(() => {
        const url = route(<string>route().current());
        const params = {
            ...route().params,
            search: search.value
        };

        router.get(url, params, {
            preserveState: true
        });
    }, 1000, {maxWait: 3000});

    const onLiveSearch = useDebounceFn(async () => {
        const url = route("search.live");
        const params = {
            query: search.value,
        };

        const response = await window.axios.get(url, {
            headers: {
                "Content-Type": "application/json",
            },
            params
        });

        suggestions.value = response.data;
    }, 1000, {maxWait: 3000});

    return {
        search,
        suggestions,
        onSearch,
        onLiveSearch
    };
};
