"use client";

import { useEffect } from 'react';
import { AlertTriangle, RefreshCcw, Home } from 'lucide-react';
import Link from 'next/link';
import styles from './error.module.scss';

export default function Error({
    error,
    reset,
}: {
    error: Error & { digest?: string };
    reset: () => void;
}) {
    useEffect(() => {
        console.error(error);
    }, [error]);

    return (
        <div className={styles.mainContainer}>
            <main className={styles.content}>
                <div className={styles.errorCard}>
                    <div className={styles.iconWrapper}>
                        <AlertTriangle size={80} strokeWidth={1.5} />
                    </div>

                    <div className={styles.textContainer}>
                        <h1 className={styles.title}>Tu essayes de t'infiltrer dans nos systèmes petit coquin O_O)</h1>
                        <p className={styles.subtitle}>
                            Une erreur inattendue est survenue. Nos techniciens (et peut-être un chat) sont sur le coup.
                        </p>
                    </div>

                    <div className={styles.btnGroup}>
                        <button onClick={() => reset()} className={styles.primaryButton}>
                            <RefreshCcw size={20} />
                            Réessayer
                        </button>
                        <Link href="/" className={styles.primaryButton} style={{ backgroundColor: '#64748b' }}>
                            <Home size={20} />
                            Accueil
                        </Link>
                    </div>
                </div>
            </main>
        </div>
    );
}