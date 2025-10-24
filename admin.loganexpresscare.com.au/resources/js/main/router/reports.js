export default [
    {
        path: "/admin/reports/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "reports",
                component: () => import("../views/reports/index.vue"),
                name: "admin.reports.index",
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: (route) => "reports",
                    permission: "reports_view",
                },
            },
        ],
    },
];
