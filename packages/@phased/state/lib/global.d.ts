import { AxiosInstance } from "axios";

interface context {
    name: string;
    length: number;
    extras?: string[];
}

interface Window {
    __PHASE_STATE__: object;
    axios: AxiosInstance;
}
