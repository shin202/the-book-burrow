export enum CouponStatusEnum {
    ENABLED = "enabled",
    DISABLED = "disabled",
    LIMIT_REACHED = "limit_reached",
    EXPIRED = "expired",
    AVAILABLE = "available",
}

export const CouponStatusList = Object.entries(CouponStatusEnum).map(([key, value]) => ({key, value}));
