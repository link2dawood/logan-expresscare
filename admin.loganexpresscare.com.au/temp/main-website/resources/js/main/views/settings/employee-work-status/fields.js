import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "employee-work-status?fields=id,xid,name";
    const addEditUrl = "employee-work-status";
    const { t } = useI18n();

    const initData = {
        name: "",
    };

    const columns = [
        {
            title: t("employee_work_status.name"),
            dataIndex: "name",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("employee_work_status.name"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
    };
};

export default fields;
