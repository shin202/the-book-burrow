import {DiscountTypeEnum} from "@/common/enums";

export interface CreateCouponDto {
    type: DiscountTypeEnum;
    code: string;
    value: number;
    minimum_order_amount: number;
    usage_limit: number;
    usage_per_user: number;
    valid_from: Date;
    valid_to: Date;
    couponable_type: "user" | "book" | "genre" | "author";
    couponable_id: number;
    description: string | null;
    is_active: boolean;
}
