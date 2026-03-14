import { request } from './client';

export const registerVisitor = (payload: any) => 
    request('/register-visitor', {
        method: 'POST',
        body: JSON.stringify(payload)
    });