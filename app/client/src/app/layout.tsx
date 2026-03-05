// src/app/layout.tsx
import './globals.scss'; // On vérifie l'extension !

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="fr">
      <body>{children}</body>
    </html>
  );
}