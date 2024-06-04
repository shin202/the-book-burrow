import {Ref, ref} from "vue";
import {Pagination} from "@/types";
import {PageState} from "primevue/paginator";
import {router} from "@inertiajs/vue3";

export const usePaginator = (data: Pagination<any>) => {
    const paginatorTemplate: Ref<string> = ref<string>("RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink");
    const paginatorRowsPerPageOptions: Ref<number[]> = ref<number[]>([5, 10, 20, 50]);
    const first: Ref<number> = ref<number>((data.pagination.currentPage - 1) * data.pagination.perPage);

    const onPageChange = ({page, rows}: PageState): void => {
        const url: string = route(route().current()!, {...route().params});
        const params = {
            page: page + 1,
            perPage: rows,
        };

        router.get(url, params, {
            preserveState: true
        });
    };

    return {
        paginatorTemplate,
        paginatorRowsPerPageOptions,
        first,
        onPageChange
    };
};
