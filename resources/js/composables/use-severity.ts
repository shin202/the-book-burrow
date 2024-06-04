import {CouponStatusEnum, DiscountTypeEnum, OrderStatusEnum, StockStatusEnum} from "@/common/enums";

export const useSeverity = () => {
    const getDiscountSeverity = (discountType: DiscountTypeEnum) => {
        const severity = {
            [DiscountTypeEnum.FIXED]: "success",
            [DiscountTypeEnum.PERCENTAGE]: "info",
        };

        return severity[discountType];
    };

    const getOrderStatusSeverity = (status: OrderStatusEnum) => {
        const severity = {
            [OrderStatusEnum.PENDING]: "warning",
            [OrderStatusEnum.PROCESSING]: "info",
            [OrderStatusEnum.FAILED]: "danger",
            [OrderStatusEnum.COMPLETED]: "success",
            [OrderStatusEnum.SHIPPED]: undefined,
            [OrderStatusEnum.DELIVERED]: "success",
            [OrderStatusEnum.CANCELLED]: "danger",
            [OrderStatusEnum.REFUNDED]: "danger",
        };

        return severity[status];
    };

    const getStockStatusSeverity = (status: StockStatusEnum) => {
        const severity = {
            [StockStatusEnum.IN_STOCK]: "success",
            [StockStatusEnum.OUT_OF_STOCK]: "danger",
            [StockStatusEnum.LOW_STOCK]: "warning",
        };

        return severity[status];
    };

    const getCouponStatusSeverity = (status: CouponStatusEnum) => {
        const severity = {
            [CouponStatusEnum.ENABLED]: "success",
            [CouponStatusEnum.DISABLED]: "secondary",
            [CouponStatusEnum.EXPIRED]: "danger",
            [CouponStatusEnum.LIMIT_REACHED]: "warning",
            [CouponStatusEnum.AVAILABLE]: "info",
        };

        return severity[status];
    };

    return {
        getDiscountSeverity,
        getOrderStatusSeverity,
        getStockStatusSeverity,
        getCouponStatusSeverity,
    };
};
