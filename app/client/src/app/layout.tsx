import type { Metadata } from "next";
import { Inter } from "next/font/google";
import footer from "@/components/layout/footer/footer";
import "./globals.scss";

// On charge Inter via Next.js Font pour la typographie secondaire
const inter = Inter({
  subsets: ["latin"],
  variable: "--font-secondary", // On crée une variable CSS pour le SCSS
});

export const metadata: Metadata = {
  title: "Application IUT Clermont Auvergne",
  description: "Plateforme développée par l'Université Clermont Auvergne",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="fr">
      <body
        className={inter.variable}
        style={{
          display: "flex",
          flexDirection: "column",
          minHeight: "100vh",
          margin: 0,
        }}
      >
        {/* Le <main> avec flex: 1 permet de pousser le footer vers le bas */}
        <main style={{ flex: 1 }}>
          {children}
        </main>

        <footer />
      </body>
    </html>
  );
}