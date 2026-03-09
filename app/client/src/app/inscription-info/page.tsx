"use client";
import InputField from '@/components/ui/forms/inputfield';
import styles from './inscription.module.scss';

export default function InscriptionPage() {
    return (
        <div className={styles.mainContainer}>
            <header className={styles.header}>
                <img src="/assets/images/logo_UCA_long.webp" alt="Logo UCA" className={styles.logo} />
            </header>

            <main className="container">
                <div className={styles.formCard}>
                    <h1 className={styles.title}>Inscription Informatique</h1>
                    <p className={styles.subtitle}>Remplissez vos coordonnées pour continuer</p>

                    <form className={styles.form}>
                        <div className={styles.row}>
                            <InputField label="Nom" name="lastname" placeholder="Ex: Dupont" />
                            <InputField label="Prénom" name="firstname" placeholder="Ex: Jean" />
                        </div>

                        <InputField label="Adresse Email" type="email" name="email" placeholder="jean.dupont@example.com" />

                        <InputField label="Numéro de téléphone" type="tel" name="phone" placeholder="06 01 02 03 04" />

                        <InputField label="Établissement actuel" name="school" placeholder="Lycée, Université..." />

                        <button type="submit" className={styles.submitBtn}>
                            S'inscrire
                        </button>
                    </form>
                </div>
            </main>
        </div>
    );
}