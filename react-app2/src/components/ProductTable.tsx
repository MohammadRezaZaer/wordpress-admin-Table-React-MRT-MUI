import React, {  useState } from "react";
import { useQuery ,useMutation,} from 'react-query';
import ProductTableItem from "./ProductTableItem";
import { useQueryClient } from 'react-query';


function ProductTable({ handleDeleteLog,handleStartFetch }) {
    const [currentPage, setCurrentPage] = useState(1);
    const { status, data, error } = useProducts(currentPage);
// console.log(data)
    let hasmore= false;
    let mydata=data;
    if (data && data.hasMore) {
        hasmore = true;
        mydata=Object.values(data).slice(0, -1);
    }
    if (status === 'loading') {
        return <div className="text-center mt-10">Loading...</div>;
    }

    if (status === 'error') {
        return <div className="text-center mt-10"> <div >Error: {error.message}</div>
            <br/>
        <button className="bg-red-500 rounded p-2" onClick={() => handleStartFetch()}> Start over Log</button>
            </div>
            ;
    }



    return (

        <div>
            <div className="py-4">
                <h1 className="text-2xl font-bold mb-4">Product Price List</h1>
                <button className="bg-red-500 rounded p-2 m-2" onClick={() => handleDeleteLog()}> Delete Log</button>
                <button className="bg-green-300 rounded p-2 m-2" onClick={() => handleStartFetch()}> Start Log</button>
                <table className="w-full border-collapse border border-gray-200">
                    <thead>
                    <tr className="bg-gray-200">
                        <th className="py-2 px-4 text-left">product</th>
                        <th className="py-2 px-4 text-left">date</th>
                        <th className="py-2 px-4 text-left">old price</th>
                        <th className="py-2 px-4 text-left">new price</th>
                        <th className="py-2 px-4 text-left">url</th>
                    </tr>
                    </thead>
                    <tbody>
                    {mydata.map((product) => (
                        product.id&& <ProductTableItem key={product.id} product={product} />
                    ))}
                    </tbody>
                </table>
            </div>

            <div>
                {currentPage > 1 && (
                    <button onClick={() => setCurrentPage(currentPage - 1)}>Prev</button>
                )}

                {hasmore && (
                    <button onClick={() => setCurrentPage(currentPage + 1)}>Next</button>
                )}
            </div>

            <p>Page {currentPage}</p>
        </div>


    );
}

export default function ProductTableContainer() {
    const queryClient = useQueryClient();

    const { mutate:mutateDelete, isLoadingdelete } = useDeleteLog();


    const handleDeleteLog = async () => {
        if (window.confirm("Are you sure you want to delete this log?")) {
            try {
                await mutateDelete();
                queryClient.invalidateQueries("products");
                // Handle successful deletion here
            } catch (error) {
                // Handle error here
            }
        }
    };
    const { mutate:mutateLogStart, isLoadingstart } = useStartLog();
    const handleStartLog = async () => {
        if (window.confirm("Are you sure you want to log process?")) {
            try {
                await mutateLogStart();

                // Handle successful deletion here
            } catch (error) {
                // Handle error here
            }
        }
    };
    return <ProductTable handleDeleteLog={handleDeleteLog} handleStartFetch={handleStartLog}  />;
}

function useProducts(page: number) {
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    // headers.append('x-wp-nonce', mrzProductPriceListData.nonce); // include the nonce in the headers

    return useQuery(['products', page], async () => {
        try {
            // const response = await fetch(/*mrzProductPriceListData.apiEndpointUrl+*/`http://localhost/shoob2/wp-json/mrz-product-price-list/v1/products?page=${page}`, { headers });
            const response = await fetch(mrzProductPriceListData.apiEndpointUrl+`products?page=${page}`, { headers });
            if (!response.ok) {
                throw new Error( ' :Network response was not ok');
            }
            return response.json();
        } catch (error) {
            throw new Error('No such file');
        }
    }, {
        retry: 1, // maximum number of retries
        retryDelay: 1000, // delay in milliseconds between retries
        refetchOnWindowFocus: false // disable refetch on window focus
    });
}


function useDeleteLog() {
    return useMutation(
        async () => {
            const response = await fetch(
                `${mrzProductPriceListData.apiEndpointUrl}deleteLog`,
                { method: "POST" }
            );
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        }
    );
}
function useStartLog() {
    const queryClient = useQueryClient();
    return useMutation(
        async () => {
            const response = await fetch(
                `${mrzProductPriceListData.apiEndpointUrl}startLog`,
                { method: "POST" }
            );
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            queryClient.invalidateQueries("products")
            return response.json();
        }
    );
}