"use client";

import { useRouter } from 'next/navigation';
import Button from '@/components/ui/button/button';
import BackButton from '@/components/ui/backbutton/backButton';
import { Monitor, FlaskConical, CodeXml } from 'lucide-react';
import styles from './department.module.scss';

export default function DepartmentsPage() {
    const router = useRouter();

    const actions = [
        {
            title: "MMI",
            desc: "Métiers du Multimédia et de l'Internet",
            subtitle: "",
            icon: Monitor,
            path: "/formulaire/mmi"
        },
        {
            title: "Chimie",
            desc: "Chimie et Génie des Procédés",
            subtitle: "",
            icon: FlaskConical,
            path: "/formulaire/chimie"
        },
        {
            title: "Informatique Graphique",
            desc: "Sciences Informatiques",
            subtitle: "",
            icon: CodeXml,
            path: "/formulaire/informatique"
        },
    ];

    return (
        <div className={styles.mainContainer}>
            <BackButton />
            <header className={styles.header}>
                <div className={styles.logoPlaceholder}>
                    <img src="/assets/images/logo_UCA_long.webp" alt="Logo UCA" />
                </div>
            </header>

            <main className="container">
                <section className={styles.heroSection}>
                    <h1 className={styles.mainTitle}>Journées Portes Ouvertes</h1>
                    <p className={styles.mainSubtitle}>Bienvenue ! Sélectionnez votre département.</p>
                </section>

                <div className={styles.buttonGrid}>
                    {actions.map((item, index) => (
                        <Button
                            key={index}
                            title={item.title}
                            description={item.desc}
                            subtitle={item.subtitle}
                            Icon={item.icon}
                            onClick={() => router.push(item.path)}
                        />
                    ))}
                </div>
            </main>
        </div>
    );
}