import {PageProps as InertiaPageProps} from '@inertiajs/core';
import {AxiosInstance} from 'axios';
import {route as ziggyRoute} from 'ziggy-js';
import {PageProps as AppPageProps} from './';
import {Echo} from 'laravel-echo';
import Pusher from "pusher-js";
import dayjs from "dayjs";

declare global {
    interface Window {
        axios: AxiosInstance;
        Echo: typeof Echo;
        Pusher: typeof Pusher;
        dayjs: typeof dayjs;
    }

    const route: typeof ziggyRoute;
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof ziggyRoute;
    }
}

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {
    }
}

declare module '*.vue' {
    import type {DefineComponent} from 'vue'
    const component: DefineComponent<object, object, any>
    export default component
}
