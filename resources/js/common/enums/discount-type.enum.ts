export enum DiscountTypeEnum {
    PERCENTAGE = "percentage",
    FIXED = "fixed"
}

export const DiscountTypeList: {
    key: string;
    value: string;
}[] = Object.entries(DiscountTypeEnum).map(([key, value]) => ({key, value}));
