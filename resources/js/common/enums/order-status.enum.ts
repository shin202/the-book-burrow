export enum OrderStatusEnum {
    PENDING = "pending",
    PROCESSING = "processing",
    FAILED = "failed",
    COMPLETED = "completed",
    SHIPPED = "shipped",
    DELIVERED = "delivered",
    CANCELLED = "cancelled",
    REFUNDED = "refunded",
}

export const OrderStatusList: {
    key: string,
    value: string,
}[] = Object.entries(OrderStatusEnum).map(([key, value]) => ({key, value}));
