"use client";

import { useState } from 'react';
import { Star, Send, Loader2, CheckCircle } from 'lucide-react';
import styles from './avis.module.scss';

export default function AvisPage() {
    const [rating, setRating] = useState(0);
    const [hover, setHover] = useState(0);
    const [comment, setComment] = useState("");
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [submitted, setSubmitted] = useState(false);
    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (rating === 0) return;
        setIsSubmitting(true);

        try {
            const response = await fetch('/api/avis', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    note: rating,
                    commentaire: comment,
                }),
            });

            if (response.ok) {
                setSubmitted(true);
            } else {
                const text = await response.text();
                const errorData = text ? JSON.parse(text) : {};
                console.error("Erreur API :", errorData);
                alert("Impossible d'envoyer l'avis pour le moment.");
            }
        } catch (error) {
            console.error("Erreur réseau :", error);
            alert("Erreur de connexion au serveur.");
        } finally {
            setIsSubmitting(false);
        }
    };



    if (submitted) {
        return (
            <div className={styles.mainContainer}>
                <div className={styles.reviewCard}>
                    <div className={styles.successContainer}>
                        <CheckCircle size={56} className={styles.successIcon} />
                        <div className={styles.successText}>
                            <h1 className={styles.title}>Merci beaucoup !</h1>
                            <p className={styles.subtitle}>
                                Ton avis a bien été enregistré.
                            </p>
                        </div>
                        <a href="/" className={styles.submitButton} style={{ textDecoration: 'none' }}>
                            Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        );
    }


    return (
        <div className={styles.mainContainer}>
            <div className={styles.reviewCard}>
                <h1 className={styles.title}>Ton avis nous intéresse ;)</h1>
                <p className={styles.subtitle}>Comment s'est passée ta visite ?</p>

                <div className={styles.starContainer}>
                    {[1, 2, 3, 4, 5].map((star) => (
                        <Star
                            key={star}
                            size={30}
                            className={`${styles.star} ${(hover || rating) >= star ? styles.active : ''}`}
                            onClick={() => setRating(star)}
                            onMouseEnter={() => setHover(star)}
                            onMouseLeave={() => setHover(0)}
                        />
                    ))}
                </div>

                <form onSubmit={handleSubmit}>
                    <textarea
                        className={styles.textarea}
                        placeholder="Un petit mot sur l'accueil, l'ambiance, les projets présentés... (optionnel)"
                        value={comment}
                        onChange={(e) => setComment(e.target.value)}
                        disabled={isSubmitting}
                    />

                    <button
                        type="submit"
                        className={styles.submitButton}
                        disabled={rating === 0 || isSubmitting}
                    >
                        {isSubmitting ? (
                            <Loader2 size={16} className="spinner" />
                        ) : (
                            <Send size={16} />
                        )}
                        <span>{isSubmitting ? "Envoi..." : "Envoyer mon avis"}</span>
                    </button>
                </form>
            </div>
        </div>
    );
}