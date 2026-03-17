"use client";

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { LogIn, ArrowLeft } from 'lucide-react';
import styles from './admin-connexion.module.scss';
import { adminLogin } from '../api';

export default function AdminLoginPage() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const router = useRouter();

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError('');

        try {
            const data = await adminLogin({ email, mdp: password });
            
            if (data && !data.error) {
                window.location.href = 'http://localhost:8080/admin';
            } else {
                setError(data.error || 'Identifiants incorrects');
            }
        } catch (err) {
            setError('Erreur technique');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className={styles.mainContainer}>
            <button className={styles.backBtn} onClick={() => router.back()}>
                <ArrowLeft size={20} />
                <span>Retour</span>
            </button>
            <div className={styles.logoContainer}>
                <img src="/assets/images/logo_UCA_long.webp" alt="UCA Logo" className={styles.logo} />
            </div>
            <main className={styles.content}>
                <div className={styles.loginCard}>
                    <div className={styles.textHeader}>
                        <h1 className={styles.title}>Administration JPO</h1>
                        <p className={styles.subtitle}>Accès réservé aux secrétaires, administrateur et étudiants</p>
                    </div>

                    <div style={{ 
                        backgroundColor: '#f0f7ff', 
                        border: '1px solid #007bff', 
                        borderRadius: '8px', 
                        padding: '12px', 
                        marginBottom: '20px', 
                        fontSize: '0.85rem', 
                        color: '#0056b3' 
                    }}>
                        <p style={{ margin: '0 0 8px 0' }}><strong>Étudiants :</strong> prenom.nom@etu.uca.fr / mdp: prenom.nom</p>
                        <p style={{ margin: 0 }}><strong>Test Admin :</strong> admin@jpo.fr / mdp: admin123</p>
                    </div>

                    <form className={styles.form} onSubmit={handleLogin}>
                        {error && <div className={styles.errorMessage}>{error}</div>}
                        <div className={styles.inputGroup}>
                            <label className={styles.label}>Email</label>
                            <input
                                type="email"
                                className={styles.input}
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                required
                            />
                        </div>
                        <div className={styles.inputGroup}>
                            <label className={styles.label}>Mot de passe</label>
                            <input
                                type="password"
                                className={styles.input}
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                required
                            />
                        </div>
                        <button type="submit" className={styles.submitBtn} disabled={loading}>
                            <LogIn size={20} />
                            <span>{loading ? 'Connexion...' : 'Se connecter'}</span>
                        </button>
                    </form>
                </div>
            </main>
        </div>
    );
}