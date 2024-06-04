import {DataTableFilterEvent, DataTableFilterMeta} from "primevue/datatable";
import _, {isArray, join} from "lodash";
import {router} from "@inertiajs/vue3";

export const useFilter = () => {
    const parseFilter = (filters: DataTableFilterMeta) => {
        filters = _.omit(filters, ["global"]);

        const result: any = {};
        Object.entries(filters).forEach(([field, filter]) => {
            result[field] = isArray((<any>filter).value) ? join((<any>filter).value, ",") : (<any>filter).value;
        });

        return {
            filter: result
        };
    };

    const onFilter = ({filters}: DataTableFilterEvent) => {
        const parsed = parseFilter(filters);
        const url = route(route().current()!);

        router.get(url, {
            ...route().params,
            ...parsed,
        }, {
            preserveState: true,
        });
    };

    const onClear = () => {
        const url = route(route().current()!);

        router.get(url);
    };

    return {
        onFilter,
        onClear,
    };
};
