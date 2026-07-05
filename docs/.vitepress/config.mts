import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Graphita",
  description: "High-performance PHP Graph Theory and Pathfinding Engine.",

  base: '/graphita/',

  themeConfig: {
    // Top Navigation Bar
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Documentation', link: '/guide/getting-started' },
      { text: 'v2.0.0', link: 'https://github.com/graphita/graphita/releases' }
    ],

    // Left Sidebar Configuration
    sidebar: [
      {
        text: 'Introduction',
        items: [
          { text: 'Getting Started', link: '/guide/getting-started' },
          { text: 'Upgrading to V2', link: '/guide/upgrading' },
        ]
      },
      {
        text: 'Core Architecture',
        items: [
          { text: 'The Graph', link: '/core/graph' },
          { text: 'Vertices & Edges', link: '/core/vertices-edges' },
        ]
      },
      {
        text: 'Algorithms',
        items: [
          { text: 'Which Algorithm Do I Use?', link: '/algorithms/choosing' },
          { text: 'Shortest Path Routing', link: '/algorithms/shortest-path' },
          { text: 'Structural Analysis', link: '/algorithms/structural' },
          { text: 'Exhaustive Traversals (DFS)', link: '/algorithms/exhaustive-dfs' }
        ]
      }
    ],

    // Social Links
    socialLinks: [
      { icon: 'github', link: 'https://github.com/graphita/graphita' }
    ],

    // Enables an instant, local search bar
    search: {
      provider: 'local'
    },

    // Footer Configuration (Only shows on the home page usually)
    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2026-present Graphita'
    }
  }
})