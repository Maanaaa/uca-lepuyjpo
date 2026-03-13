"use client";

import { useParams, useSearchParams } from 'next/navigation';
import { QRCodeSVG } from 'qrcode.react';
import { CheckCircle, Home, Map, Calendar, Download } from 'lucide-react';
import Link from 'next/link';
import styles from './page-qr.module.scss';

export default function PageQr() {
    const params = useParams();
    const searchParams = useSearchParams();

    const departments = (params.departments as string) || "Général";
    
    const nom = searchParams.get('nom') || "";
    const prenom = searchParams.get('prenom') || "Étudiant";
    const email = searchParams.get('email') || "";
    const visitorId = searchParams.get('vId') || "0000";

    const baseUrl = `http://localhost:3000/immersion/${departments}`;
    const queryParams = new URLSearchParams({
    nom: nom,
    prenom: prenom,
    email: email,
    vId: visitorId
    });
    const immersionLink = `${baseUrl}?${queryParams.toString()}`;

    const planPdfLink = `https://uca.fr/plans/${departments}-plan.pdf`;

    return (
        <div className={styles.mainContainer}>
            <main className={styles.content}>
                <div className={styles.successCard}>

                    <div className={styles.topSection}>
                        <div className={styles.iconCircle}>
                            <CheckCircle size={40} />
                        </div>
                        <h1 className={styles.title}>Parfait, {prenom} !</h1>
                        <p className={styles.subtitle}>
                            Tes renseignements sont validés. Voici tes accès pour la journée :
                        </p>
                    </div>
                    <div className={styles.qrGrid}>
                        <div className={styles.qrCard}>
                            <div className={styles.qrHeader}>
                                <Calendar size={20} className={styles.icon} />
                                <h3>Inscription immersion</h3>
                            </div>
                            <div className={styles.qrContainer}>
                                <QRCodeSVG 
                                value={immersionLink} 
                                size={160} 
                                level="L" 
                                includeMargin={true}
                                />
                            </div>
                            <p className={styles.qrInfo}>Scanne ce code pour t'inscrire à la journée d'immersion</p>
                        </div>

                        <div className={styles.qrCard}>
                            <div className={styles.qrHeader}>
                                <Map size={20} className={styles.icon} />
                                <h3>Plan {departments.toUpperCase()}</h3>
                            </div>
                            <div className={styles.qrContainer}>
                                <QRCodeSVG value={planPdfLink} size={160} level="H" includeMargin={true} />
                            </div>
                            <p className={styles.qrInfo}>Scanne pour accéder au plan PDF du bâtiment</p>
                        </div>
                    </div>

                    <Link href="/departements" className={styles.homeBtn}>
                        <Home size={20} />
                        Retour à l'accueil
                    </Link>
                </div>
            </main>
        </div>
    );
}