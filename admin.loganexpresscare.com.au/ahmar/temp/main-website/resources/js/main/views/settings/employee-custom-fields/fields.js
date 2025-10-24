import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "field-types?fields=id,xid,name,visible_to_employee,employeeField{id,xid,name,type,is_required,default_value}";
    const addEditUrl = "field-types";
    const { t } = useI18n();

    const initData = {
        name: "",
        visible_to_employee: 1,
        employee_field: []
    };

    const columns = [{
            title: t("employee_custom_field.name"),
            dataIndex: "name",
        },
        {
            title: t("employee_custom_field.employee_fields"),
            dataIndex: "employee_fields",
        },
        {
            title: t("employee_custom_field.visible_to_employee"),
            dataIndex: "visible_to_employee",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [{
        key: "name",
        value: t("employee_custom_field.name"),
    }, ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
    };
};

export default fields;
