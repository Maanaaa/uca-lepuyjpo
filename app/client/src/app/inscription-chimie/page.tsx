"use client";

import { useState } from 'react';
import InputField from '@/components/ui/forms/inputfield/inputfield';
import SelectField from '@/components/ui/forms/selectfield/selectfield';
import { COUNTRIES, DEPARTMENTS } from '@/constants/location';
import styles from './inscription_chimie.module.scss';

export default function InscriptionPage() {

    // Dans le menu déroulant des pays, on initialise directement sur la France.
    const [selectedCountry, setSelectedCountry] = useState('FR');

    return (
        <div className={styles.mainContainer}>
            <main className="container">
                <div className={styles.formCard}>
                    <h1 className={styles.title}>Inscription Chimie</h1>
                    <p className={styles.subtitle}>Remplissez vos coordonnées pour continuer</p>

                    <form className={styles.form}>

                        <InputField label="Nom" name="lastname" placeholder="Ex: MANYAPOFF" />
                        <InputField label="Prénom" name="firstname" placeholder="Ex: Thévann" />

                        <InputField label="Adresse Email" type="email" name="email" placeholder="jean.dupont@example.com" />
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
                            S'inscrire
                        </button>
                    </form>
                </div>
            </main>
        </div>
    );
}