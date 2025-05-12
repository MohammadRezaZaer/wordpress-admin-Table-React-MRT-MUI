import React from 'react';
import ReactDOM from 'react-dom';
import './styles/main.css';
import { QueryClient, QueryClientProvider } from 'react-query';
import {ProductTableContainer} from "./components/RequestLogsTable";
import {InputForm} from "./components/url-save-form";

const queryClient = new QueryClient();
ReactDOM.render(

    <QueryClientProvider client={queryClient}>

        <InputForm/>
        <ProductTableContainer />

    </QueryClientProvider>

    , document.getElementById('root'));
