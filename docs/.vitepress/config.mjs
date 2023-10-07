import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "SimpList Documentation",
  description: "Setup guide & documentations of SimpList Telegram WebApp",
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Getting Started', link: '/getting-started' },
      { text: 'TeleVue', link: 'https://github.com/erfanmola/TeleVue' }
    ],

    sidebar: [
      {
        text: 'SimpList',
        items: [
          { text: 'Getting Started', link: '/getting-started' },
          { text: 'Technologies Used', link: '/technologies-used' }
        ]
      },
      {
        text: 'FrontEnd',
        items: [
          { text: 'Setting Up FrontEnd', link: '' },
        ]
      },
      {
        text: 'BackEnd',
        items: [
          { text: 'Setting Up BackEnd', link: '' },
        ]
      },
      {
        text: 'TeleVue',
        items: [
          { text: 'TeleVue Github', link: 'https://github.com/erfanmola/TeleVue' },
          { text: 'TeleVue Documentation', link: 'https://erfanmola.github.io/TeleVue/' },
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/erfanmola/SimpList' }
    ]
  }
})
