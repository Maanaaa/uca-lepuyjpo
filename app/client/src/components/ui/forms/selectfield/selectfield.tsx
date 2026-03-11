import styles from './selectfield.module.scss';

interface SelectFieldProps {
    label: string;
    name: string;
    options: { code: string; name: string }[];
    value?: string;
    onChange?: (e: React.ChangeEvent<HTMLSelectElement>) => void;
}

const SelectField = ({ label, name, options, value, onChange }: SelectFieldProps) => {
    return (
        <div className={styles.wrapper}>
            <label className={styles.label}>{label}</label>
            <select
                name={name}
                className={styles.input}
                value={value}
                onChange={onChange}
                suppressHydrationWarning={true}
            >
                <option value="">Sélectionnez...</option>
                {options.map((opt) => (
                    <option key={opt.code} value={opt.code}>
                        {opt.name}
                    </option>
                ))}
            </select>
        </div>
    );
};

export default SelectField;