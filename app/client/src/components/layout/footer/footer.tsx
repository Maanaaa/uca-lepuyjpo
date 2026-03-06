import styles from './footer.module.scss';

const Footer = () => {
  return (
    <footer className={styles.footer}>
      <div className={styles.container}>
        <p className={styles.text}>
          © 2026 IUT Clermont Auvergne — Université Clermont Auvergne
        </p>
      </div>
    </footer>
  );
};

export default Footer;