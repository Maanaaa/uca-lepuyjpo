import { NextResponse } from 'next/server';

export async function POST(request: Request) {
    try {
        const body = await request.json();

        const symfonyRes = await fetch('http://host.docker.internal:8080/api/admin-auth/login', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(body),
        });

        if (!symfonyRes.ok) {
            const errorText = await symfonyRes.text();
            return NextResponse.json(
                { error: `Symfony (Docker) a répondu : ${symfonyRes.status}` }, 
                { status: symfonyRes.status }
            );
        }

        const data = await symfonyRes.json();
        const setCookie = symfonyRes.headers.get('set-cookie');

        const response = NextResponse.json(data);

        // On transmet le cookie de session au navigateur
        if (setCookie) {
            response.headers.set('set-cookie', setCookie);
        }

        return response;

    } catch (error: any) {
        console.error("ERREUR PROXY DOCKER:", error.message);
        return NextResponse.json(
            { error: "Impossible de joindre Symfony via host.docker.internal:8080" }, 
            { status: 500 }
        );
    }
}