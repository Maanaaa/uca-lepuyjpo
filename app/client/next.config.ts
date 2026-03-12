import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  /* 1. Configuration SCSS */
  sassOptions: {
    // Permet d'importer automatiquement les variables et mixins SCSS dans tous les fichiers .scss sans avoir à les importer manuellement à chaque fois.
    prependData: `@use "@/styles/abstracts/_index.scss" as *;`,
  },

  /* 2. Configuration Webpack (Stabilité Docker) */
  webpack: (config, { dev, isServer }) => {
    config.watchOptions = {
      poll: 1000,
      aggregateTimeout: 300,
    };

    if (dev && !isServer) {
      config.devServer = {
        ...config.devServer,
        client: {
          webSocketURL: 'ws://localhost:3000/_next/webpack-hmr',
        },
      };
    }

    return config;
  },
};

export default nextConfig;