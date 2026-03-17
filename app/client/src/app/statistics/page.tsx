"use client";

import { useEffect, useState } from 'react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';
import { Users, Star, Calendar } from 'lucide-react';
import styles from './statistics.module.scss';

export default function StatisticsPage() {
    const [data, setData] = useState({
        totalVisitors: 0,
        avgNote: 0,
        totalImmersions: 0,
        geoData: [] as any[],
        reviews: [] as any[],
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const params = "?itemsPerPage=1000";
                
                const [visiteursRes, avisRes, immersionRes] = await Promise.all([
                    fetch(`http://localhost:8080/api/visiteurs${params}`),
                    fetch(`http://localhost:8080/api/avis${params}`),
                    fetch(`http://localhost:8080/api/inscription_immersions${params}`)
                ]);

                const vJson = await visiteursRes.json();
                const aJson = await avisRes.json();
                const iJson = await immersionRes.json();

                const vList = vJson["member"] || vJson["hydra:member"] || [];
                const aList = aJson["member"] || aJson["hydra:member"] || [];
                const iList = iJson["member"] || iJson["hydra:member"] || [];

                // Stats Géographiques (Fonctionne avec departementOrigine ou Ville)
                const geoStats: Record<string, number> = {};
                vList.forEach((v: any) => {
                    let code = "";
                    if (v.departementOrigine) {
                        code = String(v.departementOrigine).padStart(2, '0');
                    } else if (v.ville) {
                        const city = v.ville.toLowerCase();
                        if (city.includes('clermont')) code = "63";
                        else if (city.includes('puy')) code = "43";
                    }

                    if (code) geoStats[code] = (geoStats[code] || 0) + 1;
                    else geoStats["Autres"] = (geoStats["Autres"] || 0) + 1;
                });

                const totalNotes = aList.reduce((acc: number, curr: any) => acc + (curr.note || 0), 0);

                setData({
                    totalVisitors: vJson["totalItems"] || vList.length,
                    totalImmersions: iJson["totalItems"] || iList.length,
                    avgNote: aList.length > 0 ? parseFloat((totalNotes / aList.length).toFixed(1)) : 0,
                    geoData: Object.entries(geoStats)
                        .map(([name, count]) => ({ name, count }))
                        .sort((a, b) => b.count - a.count)
                        .slice(0, 5),
                    reviews: aList.slice(-3).reverse(),
                });
            } catch (err) {
                console.error("Erreur Stats:", err);
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    if (loading) return <div className={styles.loader}>Chargement des graphiques...</div>;

    return (
        <div className={styles.container}>
            <header className={styles.header}>
                <h1>Statistiques JPO</h1>
            </header>

            <div className={styles.kpiGrid}>
                <div className={styles.card}>
                    <Users size={22} className={styles.icon} />
                    <div className={styles.info}>
                        <span>Visiteurs</span>
                        <strong>{data.totalVisitors}</strong>
                    </div>
                </div>
                <div className={styles.card}>
                    <Star size={22} className={styles.icon} />
                    <div className={styles.info}>
                        <span>Note Satisfaction</span>
                        <strong>{data.avgNote}/5</strong>
                    </div>
                </div>
                <div className={styles.card}>
                    <Calendar size={22} className={styles.icon} />
                    <div className={styles.info}>
                        <span>Immersions</span>
                        <strong>{data.totalImmersions}</strong>
                    </div>
                </div>
            </div>

            <div className={styles.chartsGrid}>
                <div className={styles.chartCard}>
                    <h3>Origine Géographique (Département)</h3>
                    <ResponsiveContainer width="100%" height={250}>
                        <BarChart data={data.geoData}>
                            <CartesianGrid strokeDasharray="3 3" vertical={false} />
                            <XAxis dataKey="name" tick={{fontSize: 14}} />
                            <YAxis tick={{fontSize: 14}} />
                            <Tooltip cursor={{fill: 'rgba(0,0,0,0.05)'}} />
                            <Bar dataKey="count" fill="#006C82" radius={[4, 4, 0, 0]} />
                        </BarChart>
                    </ResponsiveContainer>
                </div>
            </div>

            <div className={styles.reviewsSection}>
                <h3>Derniers Avis</h3>
                <div className={styles.reviewsList}>
                    {data.reviews.map((rev: any, i) => (
                        <div key={i} className={styles.reviewItem}>
                            <div className={styles.reviewHeader}>
                                <span className={styles.note}>{"★".repeat(rev.note)}</span>
                                <span className={styles.date}>{new Date(rev.creation).toLocaleDateString()}</span>
                            </div>
                            <p>"{rev.commentaire}"</p>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}