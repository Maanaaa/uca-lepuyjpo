"use client";

import { useRouter } from 'next/navigation';
import { ArrowLeft } from 'lucide-react';
import styles from './backbutton.module.scss';

export default function BackButton() {
    const router = useRouter();

    return (
        <button
            type="button"
            className={styles.backBtn}
            onClick={() => router.back()}
            aria-label="Retour"
        >
            <ArrowLeft size={20} strokeWidth={2.5} />
            <span>Retour</span>
        </button>
    );
}