import './globals.scss';
import Footer from '@/components/layout/footer/footer';

export default function RootLayout({
  children,
}: { children: React.ReactNode }) {
  return (
    <html lang="fr">
      <body>
        <div className="fr-flag-line" />

        <div className="main-wrapper">
          <main className="content-grow">
            {children}
          </main>
          <Footer />
        </div>
      </body>
    </html>
  );
}