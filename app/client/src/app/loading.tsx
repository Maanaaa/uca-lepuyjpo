import { Loader2 } from 'lucide-react';
import styles from './loading.module.scss';

export default function Loading() {
    return (
        <div className={styles.loaderContainer}>
            <Loader2 className={styles.spinner} size={48} />
            <p className={styles.text}>Chargement en cours...</p>
        </div>
    );
}