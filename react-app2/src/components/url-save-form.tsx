"use client"

import { zodResolver } from "@hookform/resolvers/zod"
import { useForm } from "react-hook-form"
import { z } from "zod"
import React, { useEffect, useState } from "react"
import {useFetchUrl, useGetUrl, useSaveUrl} from "./hooks/hooks";

const FormSchema = z.object({
    url: z.string().url({
        message: "Please enter a valid URL.",
    }),
})

export function InputForm() {
    const [successMessage, setSuccessMessage] = useState<string | null>(null)
    const [fetchMessage, setFetchMessage] = useState<string | null>(null)
    const [errorMessage, setErrorMessage] = useState<string | null>(null)

    const form = useForm<z.infer<typeof FormSchema>>({
        resolver: zodResolver(FormSchema),
        defaultValues: {
            url: "",
        },
    })

    const { data: initialUrlData, isLoading: isLoadingInitialUrl } = useGetUrl()
    const { mutate: saveUrl, isLoading: isSaving } = useSaveUrl()
    const { mutate: fetchFromUrl, isLoading: isFetching } = useFetchUrl()

    // Set default URL from server
    useEffect(() => {
        if (initialUrlData?.url) {
            form.setValue("url", initialUrlData.url)
        }
    }, [initialUrlData?.url, form])

    const handleFetch = () => {
        fetchFromUrl({ fetch: true }, {
            onSuccess: () => {
                setFetchMessage("Fetch request sent successfully and saved to db.")
            },
            onError: (error: any) => {
                setFetchMessage(error?.message || "Server error during fetch.")
            },
        })
    }

    const onSubmit = (data: z.infer<typeof FormSchema>) => {
        setSuccessMessage(null)
        setErrorMessage(null)

        saveUrl(data, {
            onSuccess: () => {
                setSuccessMessage("URL saved successfully.")
            },
            onError: (error: any) => {
                setErrorMessage(error?.message || "Server error while saving the URL.")
            },
        })
    }

    return (
        <form
            onSubmit={form.handleSubmit(onSubmit)}
            className="flex flex-col space-y-4 p-4 max-w-lg mx-auto"
        >
            <div>
                <label htmlFor="url" className="block text-sm font-medium text-gray-700">
                    Enter URL
                </label>
                <input
                    id="url"
                    type="text"
                    {...form.register("url")}
                    className="mt-1 p-2 border border-gray-300 rounded w-full"
                    disabled={isLoadingInitialUrl || isSaving}
                />

                {form.formState.errors.url && (
                    <p className="text-red-500 text-xs mt-1">
                        {form.formState.errors.url.message}
                    </p>
                )}

                {errorMessage && !form.formState.errors.url && (
                    <p className="text-red-600 text-sm mt-1">{errorMessage}</p>
                )}

                {successMessage && (
                    <p className="text-green-600 text-sm mt-1">{successMessage}</p>
                )}
            </div>

            <button
                type="submit"
                disabled={isSaving}
                className="bg-blue-500 text-white p-2 rounded w-full hover:bg-blue-700 disabled:opacity-50"
            >
                {isSaving ? "Saving..." : "Save URL"}
            </button>

            <button
                type="button"
                onClick={handleFetch}
                disabled={isFetching}
                className="bg-green-500 text-white p-2 rounded w-full hover:bg-green-700 disabled:opacity-50"
            >
                {isFetching ? "Sending fetch..." : "Send Fetch Request"}
            </button>

            {fetchMessage && (
                <p className="text-gray-800 text-sm mt-1">{fetchMessage}</p>
            )}
        </form>
    )
}
