"use client";

import Link from 'next/link';
import { FileQuestion, Home } from 'lucide-react';
import styles from './error.module.scss';

export default function NotFound() {
    return (
        <div className={styles.mainContainer}>
            <main className={styles.content}>
                <div className={styles.errorCard}>
                    <div className={styles.iconWrapper}>
                        <FileQuestion size={80} strokeWidth={1.5} />
                    </div>

                    <div className={styles.textContainer}>
                        <h1 className={styles.title}>Aie Aie AIIIIIIIIIE !</h1>
                        <p className={styles.subtitle}>
                            On a cherché partout (même sous les bureaux), mais cette page n'existe point.
                        </p>
                    </div>

                    <div className={styles.btnGroup}>
                        <Link href="/" className={styles.primaryButton}>
                            <Home size={20} />
                            Retour à l'accueil
                        </Link>
                    </div>
                </div>
            </main>
        </div>
    );
}