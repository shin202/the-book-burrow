export enum StockStatusEnum {
    IN_STOCK = "in_stock",
    OUT_OF_STOCK = "out_of_stock",
    LOW_STOCK = "low_stock",
}

export const StockStatusList = Object.entries(StockStatusEnum).map(([key, value]) => ({key, value}));
