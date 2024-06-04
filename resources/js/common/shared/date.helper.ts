import dayjs from "dayjs";

const STR_MONTHS = [
    'January', 'February', 'March', 'April', 'May', 'June', 'July',
    'August', 'September', 'October', 'November', 'December',
];

export const lastNMonths = (n: number) => {
    const months = [];
    for (let i = 0; i < n; i++) {
        const month = dayjs().subtract(i, 'month').month();
        months.push(STR_MONTHS[month]);
    }

    return months.reverse();
}

export const lastWeekDays = () => {
    const days = [];
    for (let i = 0; i < 7; i++) {
        const day = dayjs().startOf('week').subtract(i, 'day').format('DD/MM');
        days.push(day);
    }

    return days.reverse();
}
