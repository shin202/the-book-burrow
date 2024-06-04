export enum EventEnum {
    EXPORT_COMPLETED = "export.completed",
    IMPORT_COMPLETED = "import.completed",
    ORDER_PLACED = "order.placed",
}

export const getEventIcon = (event: EventEnum) => {
    const icons = {
        [EventEnum.EXPORT_COMPLETED]: "pi pi-download bg-violet-300",
        [EventEnum.IMPORT_COMPLETED]: "pi pi-cloud-upload bg-green-300",
        [EventEnum.ORDER_PLACED]: "pi pi-shopping-cart bg-blue-300",
    };

    return icons[event];
};
