import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "salary-groups?fields=id,xid,name,salaryGroupComponents{id,xid,x_salary_group_id,x_salary_component_id},salaryGroupComponents:salaryComponent{id,xid,name,type,value_type,bi_weekly,weekly,monthly,semi_monthly},salaryGroupUsers{id,xid,x_user_id,x_salary_group_id},salaryGroupUsers:users{id,xid,name}";
    const addEditUrl = "salary-groups";
    const { t } = useI18n();
    const hashableColumns = ["salary_component_id"];

    const initData = {
        name: "",
    };

    const columns = [
        {
            title: t("salary_group.name"),
            dataIndex: "name",
        },
        {
            title: t("salary_group.total_users"),
            dataIndex: "total_users",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("salary_group.name"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
