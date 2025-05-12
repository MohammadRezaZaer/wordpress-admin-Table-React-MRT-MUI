import {useMutation, useQuery} from "react-query";

const radmanTaskMrzData = {
    apiEndpointUrl: "http://localhost/plugin-lab/wp-json/radmantaskmrz/v1/"
}

export function useGetUrl() {


    return useQuery("url:get", async () => {
        const res = await fetch(`${radmanTaskMrzData.apiEndpointUrl}url`)
        if (!res.ok) throw new Error("Failed to fetch URL.")
        return res.json()
    })
}

export function useSaveUrl() {
    return useMutation(async (data: { url: string }) => {
        const res = await fetch(`${radmanTaskMrzData.apiEndpointUrl}url`, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(data),
        })

        if (!res.ok) {
            const err = await res.json().catch(() => null)
            throw new Error(err?.message || "Unknown error.")
        }

        return res.json()
    })
}

export function useFetchUrl() {
    return useMutation(async (data: { fetch: boolean }) => {
        const res = await fetch(`${radmanTaskMrzData.apiEndpointUrl}run-fetch-from-url`, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(data),
        })

        if (!res.ok) {
            const err = await res.json().catch(() => null)
            throw new Error(err?.message || "Unknown error.")
        }

        return res.json()
    })
}
