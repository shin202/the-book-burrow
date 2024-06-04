import {DataTableSortEvent} from "primevue/datatable";
import {router} from "@inertiajs/vue3";

export const useSort = () => {
    const onSort = ({sortOrder, sortField}: DataTableSortEvent) => {
        const url = route(route().current()!);
        const sortPrefix = sortOrder === 1 ? "" : "-";

        router.get(url, {
            ...route().params,
            sort: `${sortPrefix}${sortField}`
        });
    };

    return {
        onSort
    };
};
