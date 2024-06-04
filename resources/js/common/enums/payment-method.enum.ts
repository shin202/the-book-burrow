export enum PaymentMethodEnum {
    STRIPE = 0,
    CASH = 1,
}

export const PaymentMethodList = Object.entries(PaymentMethodEnum).map(([key, value]) => ({key, value}));
