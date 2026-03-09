import styles from './inputfield.module.scss';

interface InputFieldProps {
    label: string;
    type?: string;
    placeholder?: string;
    name: string;
}

const InputField = ({ label, type = "text", placeholder, name }: InputFieldProps) => {
    return (
        <div className={styles.wrapper}>
            <label className={styles.label}>{label}</label>
            <input
                type={type}
                name={name}
                placeholder={placeholder}
                className={styles.input}
                suppressHydrationWarning={true}
            />
        </div>
    );
};

export default InputField;