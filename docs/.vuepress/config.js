module.exports = {
    base: '/fibers-helper/',
    title: 'Fibers Helper',
    description: 'Collection of assorted helpers for collecting and modifying parts of Laravel',
    head: [
        ['link', { rel: "apple-touch-icon", sizes: "180x180", href: "/apple-touch-icon.png"}],
        ['link', { rel: "icon", type: "image/png", sizes: "32x32", href: "/favicon-32x32.png"}],
        ['link', { rel: "icon", type: "image/png", sizes: "16x16", href: "/favicon-16x16.png"}],
        ['link', { rel: "manifest", href: "/site.webmanifest"}],
        ['link', { rel: "shortcut icon", href: "/favicon.ico"}],
    ],
    theme: '@vuepress/theme-default',
    plugins: [
        '@vuepress/search', { searchMaxSuggestions: 10 },
        '@vuepress/active-header-links',
        '@vuepress/last-updated',
        '@vuepress/nprogress',
    ],
    themeConfig: {
        logo: '/logo-helper-inline.svg',
        nav: [
            { text: 'Home', link: '/' },
            { text: 'Guide', link: '/guide' },
            {
                text: 'Helpers',
                items: [
                    { text: 'Memory', link: '/helpers/memory' },
                    { text: 'Model', link: '/helpers/model' },
                    { text: 'Models', link: '/helpers/models' },
                    { text: 'Template', link: '/helpers/template' },
                    { text: 'User', link: '/helpers/user' },
                    { text: 'View', link: '/helpers/view' },
                ]
            },
        ],
        sidebar: [
            '/',
            ['/guide', 'Guide'],
            {
                title: 'Helpers',
                children: [
                    '/helpers/memory',
                    '/helpers/model',
                    '/helpers/models',
                    '/helpers/template',
                    '/helpers/user',
                    '/helpers/view',
                ]
            },
        ],
        displayAllHeaders: true,
        lastUpdated: 'Last Updated',
        repo: 'zipavlin/fibers-helper',
        editLinks: true,
        editLinkText: 'Help us improve this page!',
        evergreen: true
    }
};
