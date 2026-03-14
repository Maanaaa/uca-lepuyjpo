export const adminLogin = async (credentials: { email: string; mdp: string }) => {

    const response = await fetch('/api/proxy-login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(credentials),
    });
    return response.json();
};