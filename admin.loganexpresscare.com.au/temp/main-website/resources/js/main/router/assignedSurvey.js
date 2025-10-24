export default [
    {
        path: "/employee-survey/:id",
        component:() => import("../views/forms/survey-form/Feedback.vue"),
        name: "feedback.form",
        meta: {
            requireAuth: true,
            menuKey: "form",
        },
    },
]
