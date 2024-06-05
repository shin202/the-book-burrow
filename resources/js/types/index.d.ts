import {
    DiscountTypeEnum,
    EventEnum,
    ModelStatusEnum,
    OrderStatusEnum,
    PaymentMethodEnum,
    StockStatusEnum,
    UserGenderEnum,
    UserStatusEnum
} from "@/common/enums";

export interface User {
    id: number;
    username: string;
    email: string;
    status: UserStatusEnum;
    profile?: Profile;
    avatar?: string;
    roles?: Role[];
    unreadNotifications: Notification[];

    [key: string]: any;
}

export interface Role {
    key: string;
    value: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User
    };
};

export interface Pagination<T> {
    data: T;
    links: {
        first: string;
        last: string;
        next: string;
        prev: string;
    },
    pagination: {
        currentPage: number;
        total: number;
        perPage: number;
        lastPage: number;
        count: number;
    }

    [key: string]: any;
}

export interface Profile {
    id: number;
    first_name: string;
    last_name: string;
    gender: UserGenderEnum;
    date_of_birth: Date | string;
    country: string;
}

export interface Book {
    id: number;
    title: string;
    isbn: string;
    description: string;
    coverImage: string;
    numberOfPages: number;
    publisher: any;
    publicationDate: Date | string;
    authors: any;
    genres: any;
    slug: string;
    price: number;
    cost: number;
    quantityInStock: number;

    [key: string]: any;
}

export interface Author {
    id: number;
    firstName: string;
    lastName: string;
    fullName: string;
    biography?: string;
    avatar: string;
    slug: string;
    booksCount?: number;

    [key: string]: any;
}

export interface Genre {
    id: number;
    name: string;
    slug: string;
    description?: string;
}

export interface Publisher {
    id: number;
    name: string;
    contact_information?: {
        email: string;
        website: string;
        phone: string;
    };
    slug: string;
    email?: string;
    website?: string;
    phone?: string;
    description?: string;
    status: ModelStatusEnum;
}

export interface Notification {
    id: string;
    channel: string;
    event: EventEnum;
    title: string;
    summary: string;
    content?: string;
    download_url?: string;
    created_at: Date | string;
    read_at?: Date | string;

    [key: string]: any;
}

export interface Coupon {
    id: number;
    code: string;
    type: DiscountTypeEnum;
    value: number;
    minimum_order_amount: number;
    usage_limit: number;
    usage_per_user: number;
    valid_from: Date | string;
    valid_to: Date | string;
    status: string;

    [key: string]: any;
}

export interface Banner {
    id: number;
    title: string;
    description: string;
    image: string;
    link: string;
    status: "active" | "inactive";
}

export interface Deal {
    id: number;
    book: Book;
    discount_type: DiscountTypeEnum;
    discount_value: string | number;
    start_date: Date | string;
    end_date: Date | string;

    [key: string]: any;
}

interface OrderItem {
    book: Book;
    quantity: number;
    price: number;
}

interface Order {
    user: User;
    order_number: number;
    billing_name: string;
    billing_email: string;
    billing_phone: string;
    billing_address: string;
    billing_city: string;
    billing_state: string;
    billing_country: string;
    billing_zip: string;
    billing_discount_code: string | null;
    billing_discount: number | null;
    billing_subtotal: number;
    billing_total: number;
    total_profit: number;
    status: OrderStatusEnum;
    created_at: Date | string;
    items: OrderItem[];
    payment?: {
        transaction_id: string;
        status: string;
        method: string;
        paid_at: Date | string;
    };
    status_history: { status: OrderStatusEnum, created_at: Date | string }[];
    payment_method: PaymentMethodEnum;
}

interface Review {
    username: string;
    rating: number;
    review_text: string;
    created_at: Date | string;
}

interface BookDetail {
    id: number;
    title: string;
    isbn: string;
    description: string;
    cover_image: string;
    numberOfPages: number;
    publisher: Publisher;
    publication_date: Date | string;
    quantity_in_stock: number;
    slug: string;
    authors: Author[]
    genres: Genre[]
    stock_status: StockStatusEnum
    has_discount: boolean
    original_price: string
    discount_price: string
    reviews: Review[],
    reviews_count: number
    average_rating: number
    rating_group_count: {
        rating: number,
        count: number
    }[],
    is_rated: boolean
    user_rating: number
}
