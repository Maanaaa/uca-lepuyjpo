"use client";

import { useState, useEffect } from 'react';
import InputField from '@/components/ui/forms/inputfield/inputfield';
import SelectField from '@/components/ui/forms/selectfield/selectfield';
import BackButton from '@/components/ui/backbutton/backButton';
import { useParams, useRouter } from 'next/navigation';
import { COUNTRIES, DEPARTMENTS } from '@/constants/location';
import styles from './inscription.module.scss';
import { getDepartements, registerVisitor } from '../../api';

export default function InscriptionPage() {
    const params = useParams();
    const router = useRouter();
    const currentDept = params.departments as string;

    const [selectedCountry, setSelectedCountry] = useState('FR');
    const [loading, setLoading] = useState(false);
    const [dbDepartments, setDbDepartments] = useState<any[]>([]);

    useEffect(() => {
        getDepartements()
            .then(data => setDbDepartments(data['member'] || data['hydra:member'] || []))
            .catch(err => console.error("Erreur API:", err));
    }, []);

    const isValidDept = dbDepartments.some(d =>
        d.slug?.toLowerCase() === currentDept.toLowerCase() ||
        d.nom?.toLowerCase() === currentDept.toLowerCase()
    );

    if (!isValidDept) {
        return (
            <div className={styles.mainContainer}>
                <div className={styles.formCard}>
                    <h1 className={styles.title}>Dommage pour toi !</h1>
                    <p className={styles.subtitle}>Inutile d'essayer d'inventer un nouveau département ;)</p>
                    <a href="/departements" className={styles.submitBtn} style={{ textAlign: 'center', display: 'block', textDecoration: 'none' }}>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        );
    }

    const formatName = (name: string) => {
        if (!name) return "";
        return name === 'mmi' ? 'MMI' : name.charAt(0).toUpperCase() + name.slice(1);
    };

    const deptName = formatName(currentDept);

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData(e.currentTarget);
        const rawData = Object.fromEntries(formData.entries()) as Record<string, string>;

        const foundDept = dbDepartments.find(d =>
            d.slug?.toLowerCase() === currentDept.toLowerCase() ||
            d.nom?.toLowerCase() === currentDept.toLowerCase()
        );
        const deptId = foundDept ? foundDept.id : null;

        if (!deptId) {
            alert("Erreur : Département non trouvé en base de données.");
            setLoading(false);
            return;
        }

        const payload = {
            nom: rawData.lastname,
            prenom: rawData.firstname,
            email: rawData.email,
            telephone: rawData.phone,
            lycee: rawData.school,
            Pays: rawData.country,
            departementOrigine: rawData.department ? parseInt(rawData.department) : null,
            etudes: rawData.studies,
            departementId: deptId
        };

        try {
            const data = await registerVisitor(payload);

            if (data.visiteId) {
                router.push(
                    `/page-qr/${currentDept}` +
                    `?nom=${encodeURIComponent(payload.nom)}` +
                    `&prenom=${encodeURIComponent(payload.prenom)}` +
                    `&email=${encodeURIComponent(payload.email)}` +
                    `&vId=${encodeURIComponent(data.visiteId)}`
                );
            } else {
                alert("Erreur lors de l'inscription.");
            }
        } catch (err) {
            console.error(err);
        }

    };

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
