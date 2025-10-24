import { useAuthStore } from "../store/authStore";

export default [
    {
        path: '/',
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: '/admin/dashboard',
                component: () => import("../views/Dashboard.vue"),
                name: 'admin.dashboard.index',
                meta: {
                    requireAuth: true,
                    menuParent: "dashboard",
                    menuKey: route => "dashboard",
                },
                beforeEnter: (to, from, next) => {
                    const authStore = useAuthStore();
                    if (!authStore.user.is_manager) {
                        return next({ name: "admin.self.dashboard.index" });
                    }
                    next();
                },
            }
        ]

    }
]
