"use client";

import { useState } from 'react';
import { useParams, notFound } from 'next/navigation'; // Ajout de notFound
import InputField from '@/components/ui/forms/inputfield/inputfield';
import SelectField from '@/components/ui/forms/selectfield/selectfield';
import BackButton from '@/components/ui/backbutton/backButton';
import { COUNTRIES, DEPARTMENTS } from '@/constants/location';
import styles from './inscription.module.scss';

const VALID_DEPTS = ['mmi', 'informatique', 'chimie'];

export default function InscriptionPage() {
    const params = useParams();
    const currentDept = params.departments as string;

    if (!VALID_DEPTS.includes(currentDept)) {
        notFound();
    }

    const [selectedCountry, setSelectedCountry] = useState('FR');

    const formatName = (name: string) => {
        if (!name) return "";
        return name === 'mmi' ? 'MMI' : name.charAt(0).toUpperCase() + name.slice(1);
    };

    const deptName = formatName(currentDept);

    return (
        <div className={styles.mainContainer}>
            <main className="container">
                <div className={styles.formCard}>
                    <BackButton />
                    <h1 className={styles.title}>Inscription {deptName}</h1>
                    <p className={styles.subtitle}>Remplissez vos coordonnées pour continuer</p>

                    <form className={styles.form}>
                        <div className={styles.row}>
                            <InputField label="Nom" name="lastname" placeholder="Ex: Thévann" required />
                            <InputField label="Prénom" name="firstname" placeholder="Ex: MANYAPOFF" required />
                        </div>

                        <InputField label="Adresse Email" type="email" name="email" placeholder="thevann.manyapoff@example.com" required />
                        <InputField label="Numéro de téléphone" type="tel" name="phone" placeholder="06 01 02 03 04" required />

                        <SelectField
                            label="Pays"
                            name="country"
                            options={COUNTRIES}
                            value={selectedCountry}
                            onChange={(e) => setSelectedCountry(e.target.value)}
                            required
                        />

                        {selectedCountry === 'FR' ? (
                            <SelectField
                                label="Département"
                                name="department"
                                options={DEPARTMENTS}
                                onChange={() => { }}
                                required
                            />
                        ) : (
                            <div className={styles.emptySpace} />
                        )}

                        <InputField label="Établissement / situation actuelle" name="school" placeholder="Lycée, Université, Travail..." required />
                        <InputField label="Études / filière actuelle" name="studies" placeholder="Terminale générale / BUT Informatique..." required />

                        <button type="submit" className={styles.submitBtn}>
                            Confirmer les informations
                        </button>
                    </form>
                </div>
            </main>
        </div>
    );
}