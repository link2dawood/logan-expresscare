export default [
    {
        path: "/admin/referrals/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "referrals",
                component: () => import("../views/referrals/index.vue"),
                name: "admin.referrals.index",
                meta: {
                    requireAuth: true,
                    menuParent: "referrals",
                    menuKey: (route) => "referrals",
                    permission: "referrals_view",
                },
            },
        ],
    },
];
