"use client";

import { useState, useEffect } from 'react';
import InputField from '@/components/ui/forms/inputfield/inputfield';
import SelectField from '@/components/ui/forms/selectfield/selectfield';
import BackButton from '@/components/ui/backbutton/backButton';
import { useParams, useRouter } from 'next/navigation';
import { COUNTRIES, DEPARTMENTS } from '@/constants/location';
import styles from './inscription.module.scss';

export default function InscriptionPage() {
    const params = useParams();
    const router = useRouter();
    const currentDept = params.departments as string;

    const [selectedCountry, setSelectedCountry] = useState('FR');
    const [loading, setLoading] = useState(false);
    const [dbDepartments, setDbDepartments] = useState<any[]>([]);

    useEffect(() => {
        fetch("http://localhost:8080/api/departements")
            .then(res => res.json())
            .then(data => {
                setDbDepartments(data['member'] || data['hydra:member'] || []);
            })
            .catch(err => console.error("Erreur API Departments:", err));
    }, []);

    const isValidDept = dbDepartments.some(d => 
        d.slug?.toLowerCase() === currentDept.toLowerCase() || 
        d.nom?.toLowerCase() === currentDept.toLowerCase()
    );

    if (!VALID_DEPTS.includes(currentDept)) {
        notFound();
    }

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

                    <form className={styles.form} onSubmit={handleSubmit}>
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

                        <InputField label="Établissement actuel" name="school" placeholder="Lycée, Université..." required />
                        <InputField label="Études / filière actuelle" name="studies" placeholder="Terminale générale / BUT Informatique..." required />

                        <button type="submit" className={styles.submitBtn} disabled={loading}>
                            {loading ? "Envoi..." : "Confirmer les informations"}
                        </button>
                    </form>
                </div>
            </main>
        </div>
    );
}
