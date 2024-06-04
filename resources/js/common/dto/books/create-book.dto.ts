export interface CreateBookDto {
    title: string;
    isbn: string;
    description?: string;
    cover_image: File;
    number_of_pages: number;
    publisher_id: number;
    publication_date: Date | string;
    slug: string;
    authors: number[];
    genres: number[];
    cost: number;
    price: number;
    quantity_in_stock: number;
}
