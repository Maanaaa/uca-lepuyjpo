import { request } from './client';

export const getDepartements = () => request('/departements');

export const getJourneeImmersions = () => request('/journee_immersions');

export const postInscriptionImmersion = (payload: { vId: number; journeeId: number; dept: string }) => 
    request('/inscription-immersion', {
        method: 'POST',
        body: JSON.stringify(payload)
    });