export default [
    {
        path: "/admin/news/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "news",
                component: () => import("../views/news/index.vue"),
                name: "admin.news.index",
                meta: {
                    requireAuth: true,
                    menuParent: "news",
                    menuKey: (route) => "news",
                    permission: "news_view",
                },
            },
        ],
    },
];
