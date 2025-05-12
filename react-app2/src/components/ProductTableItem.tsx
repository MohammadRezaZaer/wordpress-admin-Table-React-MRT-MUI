import React from "react";
import { Product } from "../interfaces/Product";

interface Props {
    product: Product;
}

const ProductTableItem = ({ product }: Props) => {

    let formattedDate = '';
    if (product.timestamp ) {
        formattedDate = new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        }).format(new Date(product.timestamp * 1000)); // Multiply by 1000 to convert seconds to milliseconds
    }

    return (
        <tr className={`border border-gray-200 ${Math.abs(product.new_price - product.old_price) / product.old_price >= 0.2 ? "bg-red-200" : ""}`}>
            <td className="py-2 px-4"><a href={product.prd_link}>{product.prd_name}</a></td>
            <td className="py-2 px-4">{formattedDate}</td>
            <td className="py-2 px-4">{product.old_price}</td>
            <td className="py-2 px-4">{product.new_price}</td>
            <td className="py-2 px-4"><a href={product.url}>link</a></td>
        </tr>

    );
};

export default ProductTableItem;
