import { LucideIcon } from 'lucide-react';
import styles from './button.module.scss';

interface ButtonProps {
    title: string;
    description: string;
    subtitle: string;
    Icon: LucideIcon;
    onClick?: () => void;
}

const Button = ({ title, description, subtitle, Icon, onClick }: ButtonProps) => {
    return (
        <button className={styles.buttonCard} onClick={onClick}>
            <div className={styles.iconContainer}>
                <Icon size={32} strokeWidth={1.5} />
            </div>
            <div className={styles.textContainer}>
                <h3 className={styles.title}>{title}</h3>
                <p className={styles.description}>{description}</p>
                <span className={styles.platformSubtitle}>{subtitle}</span>
            </div>
        </button>
    );
};

export default Button;