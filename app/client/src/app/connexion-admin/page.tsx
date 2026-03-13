"use client";

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import BackButton from '@/components/ui/backbutton/backButton';
import { LogIn, ArrowLeft } from 'lucide-react';
import styles from './admin-connexion.module.scss';

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
            const response = await fetch('/api/proxy-login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email, mdp: password }),
            });

            const data = await response.json();

            if (response.ok) {
                window.location.href = 'http://localhost:8080/admin';
            } else {
                setError(data.error || 'Identifiants incorrects');
            }
        } catch (err) {
            setError('Erreur technique (Proxy)');
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
                        <p className={styles.subtitle}>Accès réservé aux secrétaires et administrateurs</p>
                    </div>

                    <form className={styles.form} onSubmit={handleLogin}>
                        <BackButton />
                        <div className={styles.inputGroup}>
                            <label htmlFor="email" className={styles.label}>Email</label>
                            <input
                                id="email"
                                type="email"
                                className={styles.input}
                                placeholder="marie@uca.fr"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                required
                            />
                        </div>

                        <div className={styles.inputGroup}>
                            <label htmlFor="password" className={styles.label}>Mot de passe</label>
                            <input
                                id="password"
                                type="password"
                                className={styles.input}
                                placeholder="••••••••"
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