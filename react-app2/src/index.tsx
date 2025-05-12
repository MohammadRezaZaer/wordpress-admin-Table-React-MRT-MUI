import React from 'react';
import ReactDOM from 'react-dom';
import './styles/main.css';
import { QueryClient, QueryClientProvider } from 'react-query';
import {ProductTableContainer} from "./components/RequestLogsTable";
import {InputForm} from "./components/url-save-form";
import LogsTableWithReactQueryProvider from "./components/LogsTableMrt";

const queryClient = new QueryClient();
ReactDOM.render(

    <QueryClientProvider client={queryClient}>

        <InputForm/>
<LogsTableWithReactQueryProvider/>
        {/*<ProductTableContainer />*/}

    </QueryClientProvider>

    , document.getElementById('root'));
