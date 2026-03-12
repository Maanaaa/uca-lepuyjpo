"use client";

import { useState, useEffect } from 'react';
import { useParams, useSearchParams } from 'next/navigation';
import BackButton from '@/components/ui/backbutton/backButton';
import styles from './immersion.module.scss';

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
        fetch("http://localhost:8080/api/departements")
            .then(res => res.json())
            .then(data => {
                const members = data['member'] || data['hydra:member'] || [];
                const map: Record<string, number> = {};

                members.forEach((d: any) => {
                    if (d.slug && typeof d.id === 'number') {
                        map[d.slug] = d.id;
                    }
                });

                setDeptMap(map);
            })
            .catch(err => console.error("Erreur API Departments:", err));
    }, []);

    // Charger les immersions une fois qu'on a le mapping des départements
    useEffect(() => {
        if (!currentDept || !deptMap[currentDept]) return;

        const targetId = deptMap[currentDept];

        fetch("http://localhost:8080/api/journee_immersions")
            .then(res => res.json())
            .then(data => {
                const allSessions = data['member'] || data['hydra:member'] || [];
                const filtered = allSessions.filter((s: any) => {
                    // Priorité au champ int departement_id (ton entité l'a)
                    if (typeof s.departement_id !== 'undefined') {
                        return s.departement_id === targetId;
                    }
                    // Fallback sur l'IRI de la relation
                    if (typeof s.departement === 'string') {
                        const match = s.departement.match(/\/departements\/(\d+)/);
                        const deptIdFromIri = match ? Number(match[1]) : null;
                        return deptIdFromIri === targetId;
                    }
                    return false;
                });
                setCurrentSessions(filtered);
                setLoading(false);
            })
            .catch(err => {
                console.error("Erreur immersion API:", err);
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
        const response = await fetch("http://localhost:8080/api/inscription-immersion", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                vId: Number(visitorId),
                journeeId: selectedDay.id,  // ← AJOUTER ÇA !
                dept: currentDept
            }),
        });

        const result = await response.json();

        if (response.ok) {
        // Utilise le prénom de l'URL, pas celui de l'API
        alert(`Super ${prenom} ! Ton immersion en ${formatName(currentDept)} est validée pour le ${formatDate(selectedDay.date)}.`);
        } else {
        alert("Erreur : " + (result.error || "Impossible de s'inscrire"));
        }
    } catch (err) {
        console.error("Erreur lors de l'inscription immersion:", err);
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
