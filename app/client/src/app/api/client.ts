export const API_BASE_URL = "http://localhost:8080/api";
export const PDF_BASE_URL = "http://localhost:8080/plans";

interface RequestOptions extends RequestInit {
    headers?: Record<string, string>;
}

export const request = async (endpoint: string, options: RequestOptions = {}) => {
    const headers = {
        'Content-Type': 'application/json',
        ...(options.headers || {}),
    };

    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        ...options,
        headers: headers,
    });

    return response.json();
};