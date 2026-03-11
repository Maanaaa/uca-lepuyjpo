"use client";

import { useState } from 'react';
import { useParams } from 'next/navigation';
import InputField from '@/components/ui/forms/inputfield/inputfield';
import SelectField from '@/components/ui/forms/selectfield/selectfield';
import { COUNTRIES, DEPARTMENTS } from '@/constants/location';
import styles from './inscription.module.scss';

// Ici on va éviter qu'on puisse mettre dans le lien dynamique des noms inexistants.
const VALID_DEPTS = ['mmi', 'informatique', 'chimie'];

export default function InscriptionPage() {
    const params = useParams();
    const currentDept = params.departments as string;

    // Si le paramètre dans l'URL n'est pas dans notre liste, on affiche un message d'erreur
    if (!VALID_DEPTS.includes(currentDept)) {
        return (
            <div className={styles.mainContainer}>
                <div className={styles.formCard}>
                    <h1 className={styles.title}>Dommage pour toi !</h1>
                    <p className={styles.subtitle}>Inutile d'essayer d'inventer un nouveau département ;)</p>
                    <a href="/" className={styles.submitBtn} style={{ textAlign: 'center', display: 'block', textDecoration: 'none' }}>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        );
    }

    const [selectedCountry, setSelectedCountry] = useState('FR');

    const formatName = (name: string) => {
        // Si on ne met aucun nom, cela évite qu'on fasse planter le site
        if (!name) return "";
        // Pour faire apparaître les noms avec un majuscule au début. Utile en cas de création de nouveau département. MMI est un acronyme donc on le met en majuscule en entier.
        return name === 'mmi' ? 'MMI' : name.charAt(0).toUpperCase() + name.slice(1);
    };

    const deptName = formatName(currentDept);

    return (
        <div className={styles.mainContainer}>
            <main className="container">
                <div className={styles.formCard}>
                    <h1 className={styles.title}>Inscription {deptName}</h1>
                    <p className={styles.subtitle}>Remplissez vos coordonnées pour continuer</p>

                    <form className={styles.form}>
                        <div className={styles.row}>
                            <InputField label="Nom" name="lastname" placeholder="Ex: Thévann" />
                            <InputField label="Prénom" name="firstname" placeholder="Ex: MANYAPOFF" />
                        </div>

                        <InputField label="Adresse Email" type="email" name="email" placeholder="thevann.manyapoff@example.com" />
                        <InputField label="Numéro de téléphone" type="tel" name="phone" placeholder="06 01 02 03 04" />

                        <SelectField
                            label="Pays"
                            name="country"
                            options={COUNTRIES}
                            value={selectedCountry}
                            onChange={(e) => setSelectedCountry(e.target.value)}
                        />

                        {selectedCountry === 'FR' ? (
                            <SelectField
                                label="Département"
                                name="department"
                                options={DEPARTMENTS}
                                onChange={() => { }}
                            />
                        ) : (
                            <div className={styles.emptySpace} />
                        )}

                        <InputField label="Établissement actuel" name="school" placeholder="Lycée, Université..." />
                        <InputField label="Études / filière actuelle" name="studies" placeholder="Terminale générale / BUT Informatique..." />

                        <button type="submit" className={styles.submitBtn}>
                            S'inscrire en {deptName}
                        </button>
                    </form>
                </div>
            </main>
        </div>
    );
}