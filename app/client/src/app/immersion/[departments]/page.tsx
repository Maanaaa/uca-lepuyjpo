"use client";

import { useState } from 'react';
import { useParams, notFound } from 'next/navigation';
import BackButton from '@/components/ui/backbutton/backButton';
import styles from './immersion.module.scss';

const DATA_IMMERSION: Record<string, { id: number; date: string; title: string }[]> = {
    mmi: [
        { id: 1, date: "Mardi 24 Mars 2026", title: "Journée en compagnie des MMI 1, 2 et 3." },
        { id: 2, date: "Jeudi 26 Mars 2026", title: "Journée en compagnie des MMI 1, 2 et 3" },
        { id: 3, date: "Lundi 30 Mars 2026", title: "Journée en compagnie des MMI 1, 2 et 3" },
    ],
    informatique: [
        { id: 4, date: "Mardi 17 Mars 2026", title: "Je fé dé je vide é oh" },
        { id: 5, date: "Mercredi 18 Mars 2026", title: "Atelier Algorithmique Python (mais pas IA, on laisse les MMI Dev faire ça)" },
    ],
    chimie: []
};

const VALID_DEPTS = ['mmi', 'informatique', 'chimie'];

export default function ImmersionPage() {
    const params = useParams();
    const currentDept = params.departments as string;
    const [selectedId, setSelectedId] = useState<number | null>(null);

    if (!VALID_DEPTS.includes(currentDept)) {
        notFound();
    }


    const currentSessions = DATA_IMMERSION[currentDept] || [];
    const selectedDay = currentSessions.find(day => day.id === selectedId);

    const formatName = (name: string) => {
        if (!name) return "";
        return name === 'mmi' ? 'MMI' : name.charAt(0).toUpperCase() + name.slice(1);
    };

    const handleConfirm = () => {
        if (selectedDay) {
            alert(`Inscription enregistrée pour l'immersion ${formatName(currentDept)} du ${selectedDay.date} :\n${selectedDay.title}`);
        }
    };

    return (
        <div className={styles.mainContainer}>
            <div className={styles.formCard}>
                <BackButton />
                <h1 className={styles.title}>Immersion {formatName(currentDept)}</h1>
                <p className={styles.subtitle}>Sélectionnez la session à laquelle vous souhaitez participer.</p>

                <div className={styles.selectionList}>
                    {currentSessions.length > 0 ? (
                        currentSessions.map((day) => {
                            const isSelected = selectedId === day.id;
                            return (
                                <div
                                    key={day.id}
                                    className={`${styles.dateItem} ${isSelected ? styles.selected : ''}`}
                                    onClick={() => setSelectedId(day.id)}
                                >
                                    <div className={styles.dateInfo}>
                                        <span className={styles.dateText}>{day.date}</span>
                                        <span className={styles.titleText}>{day.title}</span>
                                    </div>
                                </div>
                            );
                        })
                    ) : (
                        <p className={styles.noData}>Aucune date n'est disponible pour le moment.</p>
                    )}
                </div>

                <button
                    className={styles.submitBtn}
                    disabled={!selectedId}
                    onClick={handleConfirm}
                >
                    Confirmer l'inscription
                </button>
            </div>
        </div>
    );
}