"use client";

import { useState, useEffect } from 'react';
import { useParams, useSearchParams } from 'next/navigation';
import BackButton from '@/components/ui/backbutton/backButton';
import styles from './immersion.module.scss';
import { getDepartements, getJourneeImmersions, postInscriptionImmersion } from '../../api';

const VALID_DEPTS = ['mmi', 'informatique', 'chimie'];

export default function ImmersionPage() {
    const params = useParams();
    const searchParams = useSearchParams();
    const currentDept = params.departments as string;
    
    const visitorId = searchParams.get('vId'); 

    const [selectedId, setSelectedId] = useState<number | null>(null);
    const [currentSessions, setCurrentSessions] = useState<any[]>([]);
    const [deptMap, setDeptMap] = useState<Record<string, number>>({});
    const [loading, setLoading] = useState(true);

    // Charger les départements pour faire le mapping slug -> id
    useEffect(() => {
        getDepartements()
            .then(data => {
                const members = data['member'] || data['hydra:member'] || [];
                const map: Record<string, number> = {};
                members.forEach((d: any) => { if (d.slug) map[d.slug] = d.id; });
                setDeptMap(map);
            });
    }, []);

    // Charger les immersions une fois qu'on a le mapping des départements
    useEffect(() => {
        if (!currentDept || !deptMap[currentDept]) return;
        getJourneeImmersions()
            .then(data => {
                const allSessions = data['member'] || data['hydra:member'] || [];
                const filtered = allSessions.filter((s: any) => {
                    const targetId = deptMap[currentDept];
                    return s.departement_id === targetId || s.departement?.includes(`/departements/${targetId}`);
                });
                setCurrentSessions(filtered);
                setLoading(false);
            });
    }, [currentDept, deptMap]);

    if (!VALID_DEPTS.includes(currentDept)) {
        return (
            <div className={styles.mainContainer}>
                <div className={styles.formCard}>
                    <h1 className={styles.title}>Aie Aie AIIIIIIIIIE !</h1>
                    <p className={styles.subtitle}>L'espoir fait vivre mais... ce département n'existe point.</p>
                    <a href="/departements" className={styles.submitBtn} style={{ textAlign: 'center', display: 'block', textDecoration: 'none' }}>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        );
    }

    const selectedDay = currentSessions.find(day => day.id === selectedId);

    const formatName = (name: string) => {
        if (!name) return "";
        return name === 'mmi' ? 'MMI' : name.charAt(0).toUpperCase() + name.slice(1);
    };

    const formatDate = (dateStr: string) => {
        const options: Intl.DateTimeFormatOptions = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        return new Date(dateStr).toLocaleDateString('fr-FR', options);
    };

    const prenom = searchParams.get('prenom') || 'visiteur';

const handleConfirm = async () => {
    if (!selectedDay || !visitorId) return;
    try {
        const result = await postInscriptionImmersion({
            vId: Number(visitorId),
            journeeId: selectedDay.id,
            dept: currentDept
        });
        alert(`Super ${prenom} ! Ton immersion est validée.`);
    } catch (err) {
        console.error(err);
    }
};


    return (
        <div className={styles.mainContainer}>
            <div className={styles.formCard}>
                <BackButton />
                <h1 className={styles.title}>Immersion {formatName(currentDept)}</h1>
                <p className={styles.subtitle}>Sélectionnez la session à laquelle vous souhaitez participer.</p>

                <div className={styles.selectionList}>
                    {loading ? (
                        <p className={styles.noData}>Chargement des dates...</p>
                    ) : currentSessions.length > 0 ? (
                        currentSessions.map((day) => {
                            const isSelected = selectedId === day.id;
                            return (
                                <div
                                    key={day.id}
                                    className={`${styles.dateItem} ${isSelected ? styles.selected : ''}`}
                                    onClick={() => setSelectedId(day.id)}
                                >
                                    <div className={styles.dateInfo}>
                                        <span className={styles.dateText}>{formatDate(day.date)}</span>
                                        <span className={styles.titleText}>{day.nom || "Journée d'immersion standard"}</span>
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
