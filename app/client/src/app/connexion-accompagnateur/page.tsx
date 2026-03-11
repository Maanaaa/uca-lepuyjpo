"use client";

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import BackButton from '@/components/ui/backbutton/backButton';
import { LogIn } from 'lucide-react';
import styles from './login.module.scss';

export default function LoginPage() {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const router = useRouter();

    const handleLogin = (e: React.FormEvent) => {
        e.preventDefault();
        if (username.trim() && password.trim()) {
            router.push('/');
        }
    };

    return (
        <div className={styles.mainContainer}>
            <div className={styles.logoContainer}>
                <img src="/assets/images/logo_UCA_long.webp" alt="UCA Logo" className={styles.logo} />
            </div>

            <main className={styles.content}>
                <div className={styles.loginCard}>
                    <div className={styles.textHeader}>
                        <h1 className={styles.title}>Connexion Accompagnateur</h1>
                        <p className={styles.subtitle}>Identifiez-vous pour accéder au dashboard</p>
                    </div>

                    <form className={styles.form} onSubmit={handleLogin}>
                        <BackButton />
                        <div className={styles.inputGroup}>
                            <label htmlFor="username" className={styles.label}>Utilisateur</label>
                            <input
                                id="username"
                                type="text"
                                className={styles.input}
                                placeholder="Nom"
                                value={username}
                                onChange={(e) => setUsername(e.target.value)}
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

                        <button type="submit" className={styles.submitBtn}>
                            <LogIn size={20} />
                            <span>Se connecter</span>
                        </button>
                    </form>
                </div>
            </main>
        </div>
    );
}