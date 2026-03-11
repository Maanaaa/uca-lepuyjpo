import styles from './inputfield.module.scss';

interface InputFieldProps {
    label: string;
    type?: string;
    placeholder?: string;
    name: string;
    required?: boolean;
}

const InputField = ({ label, type = "text", placeholder, name, required }: InputFieldProps) => {
    return (
        <div className={styles.wrapper}>
            <label className={styles.label}>
                {label}
                {required && <span className={styles.required}> *</span>}
            </label>
            <input
                type={type}
                name={name}
                placeholder={placeholder}
                className={styles.input}
                required={required}
                suppressHydrationWarning={true}
            />
        </div>
    );
};

export default InputField;