export default [
    {
        path: "/admin/leaves/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "leaves",
                component: () => import("../views/leave/leaves/index.vue"),
                name: "admin.leaves.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    menuKey: (route) => "leaves",
                    permission: "leaves_view",
                },
            },
            {
                path: "leave_types",
                component: () => import("../views/leave/leave-types/index.vue"),
                name: "admin.leave_types.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    menuKey: (route) => "leave_types",
                    permission: "leave_types_view",
                },
            },
            {
                path: "remaining-leaves",
                component: () =>
                    import("../views/leave/remaining-leaves/index.vue"),
                name: "admin.remaining-leaves.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    menuKey: (route) => "remaining_leaves",
                    permission: "leaves_view",
                },
            },
            {
                path: "unpaid-leaves",
                component: () =>
                    import("../views/leave/unpaid-leaves/index.vue"),
                name: "admin.unpaid-leaves.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    menuKey: (route) => "unpaid_leaves",
                    permission: "leaves_view",
                },
            },
            {
                path: "paid-leaves",
                component: () => import("../views/leave/paid-leaves/index.vue"),
                name: "admin.paid-leaves.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    menuKey: (route) => "paid_leaves",
                    permission: "leaves_view",
                },
            },
        ],
    },
];
