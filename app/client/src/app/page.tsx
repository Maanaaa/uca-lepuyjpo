"use client";

import { useRouter } from 'next/navigation';
import Button from '@/components/ui/button/button';
import { Monitor, Smartphone, Settings } from 'lucide-react';
import styles from './page.module.scss';

export default function HomePage() {
  const router = useRouter();

  const actions = [
    {
      title: "Borne d'Accueil",
      desc: "Inscription des visiteurs et QR Codes",
      subtitle: "Interface borne tactile",
      icon: Monitor,
      path: "/departements"
    },
    {
      title: "WebApp Accompagnateur",
      desc: "Prise en charge et suivi des visiteurs",
      subtitle: "Mobile / Tablette",
      icon: Smartphone,
      path: "/"
    },
    {
      title: "Administration",
      desc: "Statistiques, immersions et utilisateurs",
      subtitle: "PC Bureau / Portable",
      icon: Settings,
      path: "/"
    },
  ];

  return (
    <div className={styles.mainContainer}>
      <header className={styles.header}>
        <div className={styles.logoPlaceholder}>
          <img src="/assets/images/logo_UCA_long.webp" alt="Logo UCA" />
        </div>
      </header>

      <main className="container">
        <section className={styles.heroSection}>
          <h1 className={styles.mainTitle}>Journées Portes Ouvertes</h1>
          <p className={styles.mainSubtitle}>IUT Clermont Auvergne - Site du Puy-en-Velay.</p>
        </section>

        <div className={styles.buttonGrid}>
          {actions.map((item, index) => (
            <Button
              key={index}
              title={item.title}
              description={item.desc}
              subtitle={item.subtitle}
              Icon={item.icon}
              onClick={() => router.push(item.path)}
            />
          ))}
        </div>
      </main>
    </div>
  );
}