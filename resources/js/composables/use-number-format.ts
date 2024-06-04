export const useNumberFormat = (value: any, style: string = "decimal", currency: string = "USD", locales: string = "en-US") => {
    return new Intl.NumberFormat(locales, {
        style,
        currency,
        notation: "compact",
        maximumFractionDigits: 2,
    }).format(value);
};
