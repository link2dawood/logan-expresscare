export default [
    {
        path: "/admin/forms/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "forms",
                component: () => import("../views/forms/template/index.vue"),
                name: "admin.forms.index",
                meta: {
                    requireAuth: true,
                    menuParent: "forms",
                    menuKey: (route) => "forms",
                    permission: "forms_view",
                },
            },
            {
                path: "feedbacks",
                component: () => import("../views/forms/survey-form/index.vue"),
                name: "admin.feedbacks.index",
                meta: {
                    requireAuth: true,
                    menuParent: "forms",
                    menuKey: (route) => "feedbacks",
                    permission: "feedbacks_view",
                },
            },
            {
                path: "indicators",
                component: () => import("../views/forms/indicators/index.vue"),
                name: "admin.indicators.index",
                meta: {
                    requireAuth: true,
                    menuParent: "forms",
                    menuKey: (route) => "indicators",
                    permission: "indicators_view",
                },
            },
        ],
    },
];
