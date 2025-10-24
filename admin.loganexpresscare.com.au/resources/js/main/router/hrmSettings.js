export default [
    {
        path: "/admin/hrm-settings/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "hrm-settings",
                component: () => import("../views/hrm-settings/HrmSetting.vue"),
                name: "admin.hrm_settings.index",
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: (route) => "hrm_settings",
                    permission: "hrm_settings",
                },
            },
        ],
    },
];
