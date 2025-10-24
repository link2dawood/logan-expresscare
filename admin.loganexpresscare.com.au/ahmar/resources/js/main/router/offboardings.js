export default [
    {
        path: "/admin/offboardings",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "warnings",
                component: () => import("../views/offboardings/warnings/index.vue"),
                name: "admin.warnings.index",
                meta: {
                    requireAuth: true,
                    menuParent: "offboardings",
                    menuKey: (route) => "warnings",
                },
            },
            {
                path: "resignations",
                component: () => import("../views/offboardings/resignations/index.vue"),
                name: "admin.resignations.index",
                meta: {
                    requireAuth: true,
                    menuParent: "offboardings",
                    menuKey: (route) => "resignations",
                },
            },
            {
                path: "terminations",
                component: () => import("../views/offboardings/terminations/index.vue"),
                name: "admin.terminations.index",
                meta: {
                    requireAuth: true,
                    menuParent: "offboardings",
                    menuKey: (route) => "terminations",
                },
            },
            {
                path: "complaints",
                component: () => import("../views/complaint/complaints/index.vue"),
                name: "admin.complaints.index",
                meta: {
                    requireAuth: true,
                    menuParent: "offboardings",
                    menuKey: (route) => "complaints",
                },
            },
        ],
    },
];
