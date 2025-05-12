import React, {
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';
import { MaterialReactTable, useMaterialReactTable, type MRT_ColumnFiltersState, type MRT_SortingState, type MRT_Virtualizer } from 'material-react-table';
import { Typography } from '@mui/material';
import { useInfiniteQuery } from 'react-query';
import { createTheme, ThemeProvider, useTheme } from '@mui/material';
import { MRT_Localization_FA } from 'material-react-table/locales/fa';
import { faIR } from '@mui/material/locale';
import { createColumnHelper } from '@tanstack/react-table';

const fetchSize = 25;


const radmanTaskMrzData={
    apiEndpointUrl:"http://localhost/plugin-lab/wp-json/radmantaskmrz/v1/"
}

const Example = () => {
    const tableContainerRef = useRef<HTMLDivElement>(null);
    const rowVirtualizerInstanceRef = useRef<MRT_Virtualizer<HTMLDivElement, HTMLTableRowElement>>(null);

    const [filterEnabled, setFilterEnabled] = useState(false);
    const [columnFilters, setColumnFilters] = useState<MRT_ColumnFiltersState>([]);
    const [globalFilter, setGlobalFilter] = useState<string>();
    const [sorting, setSorting] = useState<MRT_SortingState>([]);

    const { data, fetchNextPage, isError, isFetching, isLoading } = useInfiniteQuery({
        queryKey: ['Logs-data', columnFilters, globalFilter, sorting],
        queryFn: async ({ pageParam = 1 }) => {




            const url = new URL(`${radmanTaskMrzData.apiEndpointUrl}` + 'logs');
            const params = new URLSearchParams();

            // Handle filters
            if (filterEnabled) {
                if (columnFilters.length !== 0) {
                    params.set('filters', JSON.stringify(columnFilters));
                }
                if (globalFilter && globalFilter !== '') {
                    params.set('globalFilter', globalFilter);
                }
                if (sorting.length !== 0) {
                    params.set('sorting', JSON.stringify(sorting));
                }
            }

            if (fetchSize) {
                params.set('per_page', String(fetchSize));
            }

            if (pageParam !== undefined && typeof pageParam === 'number') {
                params.set('page', String(pageParam));
            }

            url.search = params.toString();

            try {
                const response = await fetch(url.href);
                const data = await response.json();
                return data;
            } catch (error) {
                throw new Error(`Error fetching logs: ${error.message}`);
            }
        },
        getNextPageParam: (lastPage) => {
            return lastPage.current_page < lastPage.last_page ? lastPage.current_page + 1 : undefined;
        },
        refetchOnWindowFocus: false,
    });

    const flatData = useMemo(() => data?.pages.flatMap((page) => page.data) ?? [], [data?.pages]);
    const columns = useMemo(() => getOfferTableCols(), []);
    const offerRowsVals = useMemo(() => getModelValues(flatData), [flatData]);

    const totalDBRowCount = data?.pages?.[0]?.total ?? 0;
    const totalFetched = flatData.length;

    const fetchMoreOnBottomReached = useCallback(
        (containerRefElement?: HTMLDivElement | null) => {
            if (containerRefElement) {
                const { scrollHeight, scrollTop, clientHeight } = containerRefElement;
                if (scrollHeight - scrollTop - clientHeight < 400 && !isFetching && totalFetched < totalDBRowCount) {
                    fetchNextPage();
                }
            }
        },
        [fetchNextPage, isFetching, totalFetched, totalDBRowCount],
    );

    useEffect(() => {
        try {
            rowVirtualizerInstanceRef.current?.scrollToIndex?.(0);
        } catch (error) {
            console.error(error);
        }
    }, [sorting, columnFilters, globalFilter]);

    useEffect(() => {
        fetchMoreOnBottomReached(tableContainerRef.current);
    }, [fetchMoreOnBottomReached]);

    const table = useMaterialReactTable({
        columns,
        data: offerRowsVals,
        enablePagination: true,
        enableRowVirtualization: true,
        defaultColumn: {
            maxSize: 400,
            minSize: 80,
            size: 260,
        },
        manualFiltering: true,
        manualSorting: true,
        muiTableContainerProps: {
            ref: tableContainerRef,
            sx: { maxHeight: '600px' },
            onScroll: (event) => fetchMoreOnBottomReached(event.target as HTMLDivElement),
        },
        muiToolbarAlertBannerProps: isError ? {
            color: 'error',
            children: 'Error loading data',
        } : undefined,
        onColumnFiltersChange: setColumnFilters,
        onGlobalFilterChange: setGlobalFilter,
        onSortingChange: setSorting,
        renderBottomToolbarCustomActions: () => (
            <Typography>
                Fetched {totalFetched} of {totalDBRowCount} total rows.
            </Typography>
        ),
        state: {
            columnFilters,
            globalFilter,
            isLoading,
            showAlertBanner: isError,
            showProgressBars: isFetching,
            sorting,
        },
        rowVirtualizerInstanceRef,
        rowVirtualizerOptions: { overscan: 4 },
        paginationDisplayMode: 'pages',
        localization: MRT_Localization_FA,
    });

    return <MaterialReactTable table={table} />;
};


const LogsTableWithReactQueryProvider = () => {
    const globalTheme = useTheme();
    const tableTheme = useMemo(
        () => createTheme(getMuiOptions(globalTheme), faIR),
        [globalTheme],
    );

    return (
        <ThemeProvider theme={tableTheme}>
            <Example />
        </ThemeProvider>
    );
};

export default LogsTableWithReactQueryProvider;

export function getModelValues(flatData) {
    const data = getRowsBasedOnModel(flatData);
    const extractedData = data.map(item => {
        const extractedItem = {};
        Object.keys(item).forEach(key => {
            extractedItem[key] = item[key].value;
        });
        return extractedItem;
    });
    return extractedData;
}

export function getOfferTableCols<TValue, TAccessor, TReturn>() {
    const cols = getLogsModelWithValueIfItemNotNull();
    if (!cols) return [];
    const columnHelper = createColumnHelper();
    let columns = [];
    for (const key in cols) {
        if (Object.prototype.hasOwnProperty.call(cols, key)) {
            columns.push({
                accessorKey: key,
                header: cols[key].label,
                enableSorting: cols[key].sortable,
                muiTableHeadCellProps: { align: 'right' },
                muiTableBodyCellProps: { align: 'right' },
            });
        }
    }
    return columns;
}


function getLogsModelWithValueIfItemNotNull(item) {
    return {
        id: {value: item?.id, label: "id",},
        response_content	: {value: item?.response_content	, label: "response_content",sortable:true},
        request_method	: {value: item?.request_method	, label: "request_method",sortable:true},
        request_date	: {value:( item?.request_date	), label: "request_date",sortable:true},

    };
}




export function getRowsBasedOnModel(dataArray) {
    if(!dataArray)return
    const revisedPackages = dataArray?.map(
        (item, idx) => {

            return getLogsModelWithValueIfItemNotNull(item);

        }
    )

    return revisedPackages
}


export function getMuiOptions(globalTheme) {
    return {
        direction: "ltr",
        palette: {
            mode: globalTheme.palette.mode,
            primary: { main: "#178c5f" },
            secondary: { main: "#1a73e8" },
            info: { main: '#ecfef7' },
            background: { default: globalTheme.palette.mode === 'light' ? '#ffffff' : '#000' },
        },
        typography: {
            fontFamily: "IRANSansX",
            button: { textTransform: 'none', fontSize: '1rem' },
        },
        components: {
            MuiTooltip: {
                styleOverrides: { tooltip: { fontSize: '1rem' } },
            },
            MuiSwitch: {
                styleOverrides: { thumb: { color: '#178c5f' } },
            },
        },
    };
}
